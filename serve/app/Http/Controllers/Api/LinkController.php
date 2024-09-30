<?php

namespace App\Http\Controllers\Api;

use App\Enums\LinkType;
use App\Enums\SwitchType;
use App\Enums\UserType;
use App\Enums\UVLimitType;
use App\Models\Domain;
use App\Models\Link;
use App\Models\LinkVisitLog;
use App\Models\MiniProgram;
use App\Models\User;
use App\Models\VipPackage;
use EasyWeChat\MiniApp\Application as WechatApp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Ugly\Base\Http\Controllers\FormController;
use Ugly\Base\Services\FormService;

class LinkController extends FormController
{
    public function index(Request $request): JsonResponse
    {
        $username = $request->input('username', null);
        $query = Link::search([
            'type' => '=',
            'title' => 'like',
        ], ['user:id,username'])->withCount(['visitLogs as visit_uv_count' => function ($query) {
            $query->select(DB::raw('COUNT(DISTINCT device_uid)'));
        }])
            ->when(
                auth('api')->user()->type !== UserType::Admin,
                fn ($query) => $query->where('user_id', auth('api')->user()->id),
            )
            ->when($username, fn ($query) => $query->whereHas('user', fn ($query) => $query->where('username', 'like', "%{$username}%")))
            ->orderByDesc('id');

        return $this->paginate($query);
    }

    protected function form(): FormService
    {
        $rule = [
            'title' => 'required|string|max:80',
            'type' => ['required', new Enum(LinkType::class)],
            'icon' => 'required',
            'description' => 'nullable|string|max:500',
            'remark' => 'nullable|string',
            'expired_at' => 'string',
        ];
        if (request('type') == LinkType::LANDING_MINI->value) {
            unset($rule['title'], $rule['description']);
        }

        $form = FormService::make(Link::class);
        $form->validate(fn (FormService $form) => array_merge($rule, $this->getConfigRules()), [
            'config.wx.qr.*.name.required' => '请填写二维码名称',
            'config.wx.qr.*.sort.required' => '请填写二维码排序',
            'config.wx.qr.*.path.required' => '请上传二维码',
            'config.url.starts_with' => '请正确上传草料图片类生成的二维码图片',
        ]);

        $user = auth('api')->user();
        $form->policy(function (FormService $form) use ($user) {
            if ($user->type === UserType::Admin) {
                return true;
            }
            if ($form->isCreate()) {
                $config = data_get($user->getPackageConfig(), 'config');

                // 类型限制
                $typeName = LinkType::from((int) request()->input('type'))->name;
                if (! data_get($config, 'allow_type.'.$typeName)) {
                    return '当前套餐不支持该类型链接！';
                }

                // 会员，数量限制
                if (
                    $user->type === UserType::MEMBER &&
                    data_get($config, 'count_limit') <= Link::query()->where('user_id', $user->id)->count()
                ) {
                    return '拥有的链接数量已达上限！';
                }

                // 是否允许自定义落地页
                $inputConfig = request()->input('config');
                if (
                    ! data_get($config, 'cur_index') &&
                    (
                        data_get($inputConfig, 'wx.avatar') ||
                        data_get($inputConfig, 'wx.title') ||
                        data_get($inputConfig, 'wx.sub_title')
                    )
                ) {
                    return '当前套餐不允许自定义落地页！';
                }
            }

            return $form->isCreate() || $form->getModel()->user_id === $user->id;
        });

        $form->saving(function (FormService $form) use ($user) {
            if ($form->isCreate()) {
                $form->safeFormData['status'] = 1;
                $form->safeFormData['user_id'] = $user->id;

                if ($form->safeFormData['type'] == LinkType::LANDING_MINI->value) {
                    foreach ($form->safeFormData['config']['wx']['qr'] as &$it) {
                        $it['visit_uv'] = 0;
                    }
                    $form->safeFormData['title'] = $form->safeFormData['config']['wx']['title'] ?? '';
                    $form->safeFormData['description'] = $form->safeFormData['config']['wx']['sub_title'] ?? '';
                }
                $form->safeFormData['expired_at'] = now()->addMonth();
            }
        });

        return $form;
    }

    // 详情.
    public function show($id): JsonResponse
    {
        $link = Link::query()
            ->where(function ($query) {
                if (auth('api')->user()->type !== UserType::Admin) {
                    $query->where('user_id', auth('api')->user()->id);
                }
            })
            ->findOrFail($id)
            ->toArray();
        $domain = Domain::query()
            ->find(data_get($link, 'config.domain_id'));
        $link['share_link'] = $domain ? rtrim($domain->url, '/').'?code='.data_get($link, 'code') : '';

        return $this->success($link);
    }

    // 生成config字段的验证规则.
    private function getConfigRules(): array
    {
        $user = auth('api')->user();
        $iptType = request()->integer('type');
        $is_wx = $iptType === LinkType::LANDING_MINI->value;

        $res = [
            'config.domain_id' => [
                'required',
                Rule::exists('domains', 'id')->where('enable', true),
            ],
            'config.url' => 'required_unless:type,'.LinkType::LANDING_MINI->value,
        ];

        if ($iptType && in_array($iptType, [LinkType::MINI_PROGRAM->value, LinkType::LANDING_MINI->value])) {
            $res['config.min_id'] = 'required';
        }

        if ($iptType === LinkType::CLI_QR->value) {
            $res['config.url'] = 'required|url|starts_with:https://qr61.cn/';
        }

        return $is_wx ? array_merge($res, [
            'config.wx' => [
                'required_if:type,'.LinkType::LANDING_MINI->value,
                'array',
            ],
            'config.wx.avatar' => 'nullable|string',
            'config.wx.title' => 'nullable|string',
            'config.wx.sub_title' => 'nullable|string',
            'config.wx.qr' => 'required|array',
            'config.wx.qr.*.sort' => 'required|integer|min:0|max:200',
            'config.wx.qr.*.name' => 'required|string',
            'config.wx.qr.*.path' => 'required|string',
            'config.wx.qr.*.uv_limit_num' => 'nullable',
            'config.wx.qr.*.visit_uv' => 'nullable',
            'config.wx.qr.*.expired_at' => 'nullable|date_format:Y-m-d',
            'config.wx.switch_type' => ['required', new Enum(SwitchType::class)],
            'config.wx.uv_limit_type' => ['required', new Enum(UVLimitType::class)],
        ]) : $res;
    }

    // 获取链接跳转目标
    public function target(Request $request, $code): JsonResponse
    {
        $link = Link::query()->where('code', $code)->first();
        if (empty($link)) {
            return $this->failed('此链接已被删除！');
        }
        if ($link->expired_at < now()) {
            return $this->failed('链接已过期！');
        }
        $user = User::query()->findOrFail($link->user_id);
        if (empty($user->status)) {
            return $this->failed('账号被禁用！');
        }
        $isAdmin = $user->type === UserType::Admin;
        if (! $isAdmin && ! $user->vip_id) {
            return $this->failed('会员已到期，禁止访问！');
        }

        $device_uid = $request->input('device_uid') ?: Str::uuid();
        $cache = data_get(LinkVisitLog::query()
            ->where('link_id', $link->id)
            ->where('device_uid', $device_uid)
            ->orderByDesc('id')
            ->first(), 'cache', []);
        if (empty($cache)) {
            if (! $isAdmin) {
                // 会员UV限制
                $totalUV = LinkVisitLog::query()
                    ->where('link_id', $link->id)
                    ->groupBy('device_uid')
                    ->whereBetween('created_at', [$user->start_at, $user->end_at])
                    ->count();
                $limit = (int) data_get(VipPackage::query()->find($user->vip_id), 'config.uv_limit');

                if ($limit > 0 && $totalUV >= $limit) {
                    return $this->failed('当前访问已达上限！');
                }
            }

            $cache = [
                'title' => $link->title,
                'description' => $link->description,
                'icon' => Storage::url($link->icon),
            ];
            if (
                $link->type === LinkType::MINI_PROGRAM ||
                $link->type === LinkType::LANDING_MINI
            ) {
                // 跳转到小程序 参数
                $mp = MiniProgram::query()->findOrFail(data_get($link, 'config.min_id'));
                if (empty($mp) || ! $mp->is_enable) {
                    return $this->failed('当前小程序不能使用');
                }
                if ($link->type === LinkType::LANDING_MINI) {
                    $qr = $this->getWechatNextQr($link);
                    if (empty($qr)) {
                        return $this->failed('当前小程序已无可用二维码！');
                    }
                }
                $cache['params'] = [
                    'appid' => $mp->app_id,
                    'secret' => $mp->secret,
                    'qr' => $qr ?? null,
                    'path' => data_get($link, 'config.url') ?: $mp->url,
                ];
            } elseif ($link->type === LinkType::KING_DOC) {
                $cache['params']['sid'] = str_replace('https://kdocs.cn/l/', '', data_get($link, 'config.url'));
            } elseif ($link->type === LinkType::CLI_QR) {
                /**
                 * 1、电脑端访问草料二维码官网：https://cli.im/img
                 * 2、上传微信二维码、公众号二维码、企业微信二维码、视频号二维码等等，然后点生成活码
                 * 3、后台创建草料
                 */
                $configUrl = str_replace('https://qr61.cn/', '', data_get($link, 'config.url'));
                [$userID, $cliID] = explode('/', $configUrl);
                $cache['params'] = [
                    'userID' => $userID,
                    'cliID' => $cliID,
                ];
            } elseif ($link->type === LinkType::WORK_WECHAT) {
                $cache['params'] = [
                    'url' => data_get($link, 'config.url'),
                ];
            } else {
                return $this->failed('暂不支持此链接跳转！');
            }
        } else {
            unset($cache['target']);
        }

        // 生成链接
        if ($link->type === LinkType::WORK_WECHAT) {
            $cache['target'] = data_get($cache, 'params.url');
        } elseif ($link->type === LinkType::CLI_QR) {
            $userID = data_get($cache, 'params.userID');
            $cliID = data_get($cache, 'params.cliID');
            $getTicketUrl = file_get_contents('https://nc.cli.im/api/weixin/getWxUrlScheme/?query=q=qr61.cn/'.$userID.'/'.$cliID.'&path=pages/code/code&appid=wx5db79bd23a923e8e&org_coding='.$userID);
            $fetchUrlData = file_get_contents(data_get(json_decode($getTicketUrl, true), 'data.wx_url_scheme.fetchUrl'));
            $arr = json_decode($fetchUrlData, true);
            $cache['target'] = data_get($arr, 'data.urlScheme');
        } elseif ($link->type === LinkType::KING_DOC) {
            $sidUrl = data_get($cache, 'params.sid');
            preg_match('/https:\/\/kdocs\.cn\/l\/([a-zA-Z0-9]+)/', $sidUrl, $matches);
            $url_link = Http::withUserAgent('Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36')
                ->get('https://account.kdocs.cn/api/v3/miniprogram/urllink', [
                    'appid' => 'wx5b97b0686831c076',
                    'path' => 'pages/navigate/navigate',
                    'query' => http_build_query([
                        'url' => 'pages/preview/preview?from=wxminiprogram&fid=256465035925&sid='.$matches[1].'&fname='.urlencode('扫码加微信.docx'),
                        'scene' => '102',
                        'jump_from' => 'wechatlogin_guide_passive',
                        'comp' => 'docx',
                        'dw' => '1',
                    ]),
                    'env_version' => 'release',
                    'is_expire' => 'true',
                    'expire_time' => time() + 7200,
                ])->json('url_link');
            $jinShanResponse = Http::get($url_link)->throw();
            preg_match("/url_scheme:\s*'([^']+)'/", $jinShanResponse, $matches);

            $cache['target'] = $matches[1];
        } else {
            // 小程序
            $miniApp = new WechatApp([
                'app_id' => data_get($cache, 'params.appid'),
                'secret' => data_get($cache, 'params.secret'),
            ]);
            try {
                $res = $miniApp->getClient()->postJson('wxa/generatescheme', [
                    'jump_wxa' => [
                        'path' => data_get($cache, 'params.path'),
                        'query' => 'code='.data_get($link, 'code').'&device_uid='.$device_uid,
                    ],
                ])->toArray();
                if (isset($res['errcode']) && $res['errcode'] === 40001) {
                    $cache = new Psr16Cache(new FilesystemAdapter('easywechat', 1500));
                    $cache->clear();
                }
            } catch (\Exception $e) {
                $res = [
                    'errcode' => 1,
                    'errmsg' => '小程序配置错误',
                ];
            }
            if (! empty($res['errcode'])) {
                return $this->failed($res['errmsg']);
            }

            $cache['target'] = data_get($res, 'openlink');
        }

        if (isset($cache['target']) && $cache['target']) {
            LinkVisitLog::query()->create([
                'link_id' => $link->id,
                'user_id' => $link->user_id,
                'ip' => $request->getClientIp(),
                'device_uid' => $device_uid,
                'cache' => $cache,
            ]);

            unset($cache['params']);

            return $this->success($cache);
        } else {
            return $this->failed('访问错误！');
        }
    }

    // 获取需要展示的二维码
    public function getShowQr($code): JsonResponse
    {
        $link = Link::query()->where('code', $code)->first();
        if (empty($link) || ! $link->status) {
            return $this->success([
                'id' => '',
                'avatar' => '',
                'title' => '数据不存在~',
                'sub_title' => '',
                'qr' => '',
            ]);
        }
        $config = data_get($link, 'config.wx');
        $device_uid = request()->input('device_uid') ?: Str::uuid();
        $cache_qr = data_get(LinkVisitLog::query()
            ->where('link_id', $link->id)
            ->where('device_uid', $device_uid)
            ->orderByDesc('id')
            ->first(), 'cache.params.qr', []);
        if (empty($cache_qr)) {
            return $this->failed('参数错误！');
        }

        return $this->success([
            'id' => $link->id,
            'avatar' => Storage::url(data_get($config, 'avatar')),
            'title' => data_get($config, 'title'),
            'sub_title' => data_get($config, 'sub_title'),
            'qr' => Storage::url(data_get($cache_qr, 'path')),
        ]);
    }

    // 获取下一次访问时展示的二维码
    private function getWechatNextQr($link)
    {
        $qrCodes = data_get($link, 'config.wx.qr', []);
        // 对链接中的二维码们按照 sort 从小到大排序
        usort($qrCodes, function ($a, $b) {
            return $a['sort'] <=> $b['sort'];
        });

        $UVLimitType = (int) data_get($link, 'config.wx.uv_limit_type');
        $keyPrefix = "link-wx-type{$UVLimitType}-{$link->id}";

        $singleUVViews = [];
        if ($UVLimitType === UVLimitType::ACCUMULATE->value) {
            $key = "{$keyPrefix}-accumulate";
            $singleUVViews = Redis::hgetall($key);
        } elseif ($UVLimitType === UVLimitType::DAILY->value) {
            $ymdDate = date('Ymd');
            $key = "{$keyPrefix}-daily-{$ymdDate}";
            $singleUVViews = Redis::hgetall($key);
        }

        // 排除掉已过期的二维码和超出访问上限的二维码
        $filteredQrCodes = array_filter($qrCodes, function ($qrCode) use ($singleUVViews) {
            $expiredAt = ! empty($expiredAt) ? Carbon::parse($qrCode['expired_at']) : now()->addHour();
            $sort = $qrCode['sort'];
            $visitUV = $singleUVViews[$sort] ?? 0;
            $uvLimitNum = $qrCode['uv_limit_num'] ?? 99999999;

            return $expiredAt->greaterThan(now()) && (is_null($uvLimitNum) || $visitUV < $uvLimitNum);
        });
        if (empty($filteredQrCodes)) {
            return [];
        }
        $filteredQrCodes = array_values($filteredQrCodes);

        // 切换方式.
        $nextIndex = 0; // 默认返回第一个二维码
        $switch_type = data_get($link, 'config.wx.switch_type');
        if ($switch_type === SwitchType::SEQUENCE->value) {
            // 顺序切换
            $lockKey = "link-sequence-{$link->id}";
            $indexKey = "link-sequence-index-{$link->id}";
            $lock = Cache::lock($lockKey, 1);
            if ($lock->get()) {
                try {
                    $currentIndex = Cache::get($indexKey, -1);
                    $nextIndex = ($currentIndex + 1) % count($filteredQrCodes);
                    Cache::put($indexKey, $nextIndex);
                } finally {
                    $lock->release();
                }
            } else {
                Log::warning('获取锁失败', ['key' => $lockKey]);
            }
        } elseif ($switch_type === SwitchType::RANDOM->value) {
            // 随机切换
            $nextIndex = array_rand($filteredQrCodes);
        }

        $selectedQrCode = $filteredQrCodes[$nextIndex];
        // 更新 Redis 中的访问计数
        if ($UVLimitType === UVLimitType::ACCUMULATE->value) {
            $key = "{$keyPrefix}-accumulate";
            Redis::hincrby($key, $selectedQrCode['sort'], 1);
        } elseif ($UVLimitType === UVLimitType::DAILY->value) {
            $ymdDate = date('Ymd');
            $key = "{$keyPrefix}-daily-{$ymdDate}";
            Redis::hincrby($key, $selectedQrCode['sort'], 1);
        }

        return $selectedQrCode;
    }

    // 获取下拉数据源
    public function link_list(): JsonResponse
    {
        $list = Link::query()
            ->where('user_id', auth('api')->user()->id)
            ->orderByDesc('type')
            ->orderByDesc('id')
            ->get(['id', 'icon', 'title', 'type']);

        return $this->success($list);
    }
}
