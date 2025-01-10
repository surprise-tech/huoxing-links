<?php

namespace App\Http\Controllers\Api;

use App\Forms\BaseConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ugly\Base\Traits\ApiResource;

class ConfigController extends Controller
{
    use ApiResource;

    /**
     * 表单配置.
     */
    protected array $form = [];

    /**
     * 获取表单默认值.
     *
     * @return JsonResponse|void
     */
    public function getForm(string $type)
    {
        $form = new BaseConfig;

        return $this->success($form->default());
    }

    /**
     * 更新表单.
     *
     * @return JsonResponse|void
     *
     * @throws \Throwable
     */
    public function saveForm(Request $request)
    {
        return $this->success(
            DB::transaction(function () use ($request) {
                $form = new BaseConfig;
                $form->handle($form->policy($request));

                return $form->default();
            })
        );
    }
}
