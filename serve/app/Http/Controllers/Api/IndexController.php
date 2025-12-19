<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\LinkVisitLog;
use App\Models\MiniProgram;
use App\Models\Notice;
use App\Models\User;
use App\Services\SystemConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            // 用户增长曲线 按月统计
            $type = $request->integer('type', 1); // 1 最近30天；2 最近12个月

            $res['user_count'] = User::query()
                ->where('type', UserType::MEMBER)
                ->where('created_at', '>=',
                    $type === 1 ?
                        now()->subDays(30)->startOfDay()
                        : now()->subYear()->startOfDay()
                )
                ->count();
            $res['card_count'] = Link::query()
                ->where('created_at', '>=',
                    $type === 1 ?
                        now()->subDays(30)->startOfDay()
                        : now()->subYear()->startOfDay()
                )
                ->count();
            $res['uv_count'] = LinkVisitLog::query()
                ->where('created_at', '>=',
                    $type === 1 ?
                        now()->subDays(30)->startOfDay()
                        : now()->subYear()->startOfDay()
                )
                ->count();
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
            $res = [];
            $res['used_uv'] = LinkVisitLog::query()
                ->where('user_id', $user->id)
                ->count();
            $res['month_uv'] = LinkVisitLog::query()
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()])
                ->count();

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
                )
                ->get(['id', 'name']);
        }

        return $this->success($configs);
    }
}
