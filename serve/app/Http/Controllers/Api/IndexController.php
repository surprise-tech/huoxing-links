<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Jobs\PayVip;
use App\Models\Domain;
use App\Models\Link;
use App\Models\LinkVisitLog;
use App\Models\MiniProgram;
use App\Models\Notice;
use App\Models\User;
use App\Models\VipLogs;
use App\Models\VipPackage;
use App\Services\SystemConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ugly\Base\Enums\PaymentStatus;
use Ugly\Base\Models\Payment;
use Ugly\Base\Traits\ApiResource;

class IndexController extends Controller
{
    use ApiResource;

    // 首页统计
    public function index(Request $request)
    {
        $user = auth('api')->user();
        // 超级管理的首页统计
        if ($user->type === UserType::Admin) {
            $res['user_count'] = (string) User::query()->where('type', UserType::MEMBER)->count();
            $res['pay_count'] = User::query()->where('type', UserType::MEMBER)->whereNotNull('vip_id')->count();
            $res['pay_vip'] = bcdiv(Payment::query()
                ->where('status', PaymentStatus::Success)
                ->where('job', PayVip::class)
                ->sum('amount'), 100, 2);

            // 用户增长曲线 按月统计
            $type = $request->integer('type', 1); // 1 最近30天；2 最近12个月
            $res['user_growth'] = User::query()
                ->select([
                    DB::raw('DATE_FORMAT(`created_at`, '.($type === 1 ? "'%Y-%m-%d'" : "'%Y-%m'").') AS date'),
                    DB::raw('SUM(CASE WHEN `users`.`type` = '.UserType::MEMBER->value.' THEN 1 ELSE 0 END) AS member_count'),
                ])
                ->groupBy('date')
                ->orderBy('date')
                ->where('created_at', '>=',
                    $type === 1 ?
                        now()->subDays(30)->startOfDay()
                        : now()->subYear()->startOfDay()
                )
                ->where('created_at', '<=', now())
                ->get()
                ->toArray();

            return $this->success($res);
        }

        // 会员的统计
        if ($user->type === UserType::MEMBER) {
            $vip = VipPackage::query()->find($user->vip_id);
            $res = [];
            $res['link_count'] = Link::query()->where('user_id', $user->id)->count();

            if ($vip) {
                $res['used_uv'] = LinkVisitLog::query()
                    ->where('user_id', $user->id)
                    ->when($user->start_at, function ($query, $start_at) {
                        $query->where('created_at', '>=', $start_at);
                    })
                    ->when($user->end_at, function ($query, $end_at) {
                        $query->where('created_at', '>=', $end_at);
                    })
                    ->groupBy('device_uid')
                    ->count();
            }
            $res['vip_logs'] = VipLogs::with(['payment', 'vipPackage'])
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->limit(30)
                ->get()->transform(function ($item) {
                    return [
                        'id' => $item->id,
                        'amount' => $item->payment?->amount ?: 0,
                        'start_at' => $item->start_at,
                        'end_at' => $item->end_at,
                        'status' => $item->status,
                        'vip_package' => [
                            'id' => $item->vipPackage?->id,
                            'name' => $item->vipPackage?->name,
                        ],
                    ];
                });

            $res['notices'] = Notice::query()
                ->where('type', UserType::MEMBER)
                ->where('show', true)
                ->orderByDesc('sort')
                ->get(['id', 'title', 'content']);

            return $this->success($res);
        }

        return $this->success([]);
    }

    // 系统配置
    public function config(): JsonResponse
    {
        $configs = [
            'code_mode' => SystemConfig::get('send_code_mode'),
            'verify_code_is_open' => SystemConfig::get('verify_code_is_open'),
            'package' => VipPackage::query()->orderBy('level')->get(['id', 'name', 'price', 'config']),
            'web_site_customer_service' => SystemConfig::get('web_site_customer_service'),
            'web_site_title' => SystemConfig::get('web_site_title'),
            'web_site_logo' => SystemConfig::get('web_site_logo'),
            'web_site_bottom_logo' => SystemConfig::get('web_site_bottom_logo'),
            'asset_url' => Storage::url(''),  // 图片路径
        ];

        $user = auth('api')->user();
        if ($user) {
            // 安全域名
            // $configs['domains'] = Domain::query()->where('enable', true)->get(['id', 'title']);
            // 小程序 (区分是否有权限使用平台小程序池)
            $configs['mini_programs'] = MiniProgram::query()
                ->where(
                    fn ($query) => $query->where('user_id', $user->id)
                        ->when($user->getPackageConfig('pre_min'), fn ($query) => $query->orWhere('is_pre_min', true))
                )
                ->get(['id', 'name']);
        }

        return $this->success($configs);
    }
}
