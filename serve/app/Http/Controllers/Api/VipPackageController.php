<?php

namespace App\Http\Controllers\Api;

use App\Enums\LinkType;
use App\Models\User;
use App\Models\VipPackage;
use Illuminate\Http\JsonResponse;
use Ugly\Base\Exceptions\ApiCustomError;
use Ugly\Base\Http\Controllers\FormController;
use Ugly\Base\Services\FormService;

class VipPackageController extends FormController
{
    public function index(): JsonResponse
    {
        return $this->success(VipPackage::query()->get());
    }

    protected function form(): FormService
    {
        $form = FormService::make(VipPackage::class);

        $form->validate(fn (FormService $form) => [
            'name' => ['required', $form->unique()],
            'price' => 'required|numeric|min:0',
            'level' => 'required|integer|min:0',
            'config' => 'required|array',
            'config.uv_limit' => 'required|integer|min:0',
            'config.count_limit' => 'required|integer|min:0', // 链接数量限制
            'config.min_count_limit' => 'required|integer|min:0',
            'config.pre_min' => 'required|boolean', // 平台落地小程序
            'config.min_disabled_check' => 'required|boolean',
            'config.support' => 'required|boolean',
            'config.cur_index' => 'required|boolean',
            'config.allow_type' => 'required_array_keys:'.implode(',', LinkType::getAllType()),
            'config.allow_type.*' => 'required|boolean',
        ]);
        $form->deleting(function (FormService $form) {
            throw_if(
                User::query()->where('vip_id', $form->getKey())->exists(),
                new ApiCustomError('该套餐下有未过期的会员，不能删除！')
            );
        });

        return $form;
    }
}
