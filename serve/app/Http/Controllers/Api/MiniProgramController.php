<?php

namespace App\Http\Controllers\Api;

use App\Enums\MiniType;
use App\Enums\UserType;
use App\Models\MiniProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Ugly\Base\Http\Controllers\FormController;
use Ugly\Base\Services\FormService;

class MiniProgramController extends FormController
{
    public function home(): JsonResponse
    {
        $user = auth('api')->user();
        $is_pre_min = false;
        if ($user->start_at < now() && $user->end_at > now()) {
            // 套餐包含官方小程序池使用
            $is_pre_min = $user->getPackageConfig('pre_min');
        }
        $query = MiniProgram::search([
            'name' => 'like',
            'type' => '=',
            'is_enable' => '=',
        ], ['user:id,username'])
            ->where(fn ($query) => $query->where('user_id', $user->id)
                ->when(
                    $is_pre_min,
                    fn ($query) => $query->orWhere('is_pre_min', true)
                )
            )
            ->orderByDesc('id')
            ->get();

        return $this->success($query);
    }

    public function index(Request $request): JsonResponse
    {
        $username = $request->input('username', null);
        $query = MiniProgram::search([
            'name' => 'like',
            'type' => '=',
            'is_enable' => '=',
        ], ['user:id,username'])
            ->when(
                auth('api')->user()->type !== UserType::Admin,
                fn ($query) => $query->where('user_id', auth('api')->user()->id),
            )
            ->when($username, fn ($query) => $query->whereHas('user', fn ($query) => $query->where('username', 'like', "%{$username}%")))
            ->orderByDesc('id')
            ->get();

        return $this->success($query);
    }

    protected function form(): FormService
    {
        $form = FormService::make(MiniProgram::class);
        $form->validate([
            'name' => 'required',
            'app_id' => 'required',
            'secret' => 'required',
            'type' => ['required', new Enum(MiniType::class)],
            'url' => 'required',
            'is_enable' => 'required|boolean',
        ]);

        $user = auth('api')->user();
        $form->policy(function (FormService $form) use ($user) {
            if ($user->type === UserType::Admin) {
                return true;
            }
            if ($form->isCreate()) {
                $min_count_limit = (int) $user->getPackageConfig('min_count_limit');
                if ($min_count_limit <= MiniProgram::query()->where('user_id', $user->id)->count()) {
                    return '创建小程序数量已达上限！';
                }

                return true;
            }

            return $form->getModel()->user_id === $user->id;
        });

        $form->handleInput(function (FormService $formService) use ($user) {
            if ($formService->isCreate()) {
                $formService->safeFormData['user_id'] = $user->id;
            }

            if ($user->type === UserType::Admin) { // 管理员可以设置是否预置小程序
                $formService->safeFormData['is_pre_min'] = request()->boolean('is_pre_min');
            }

            return $formService->safeFormData;
        });

        return $form;
    }
}
