<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ugly\Base\Traits\ApiResource;

class UserController extends Controller
{
    use ApiResource;

    public function index(): JsonResponse
    {
        $query = User::search([
            'type' => '=',
            'username' => 'like',
        ], ['parent'])
            ->whereIn('type', [UserType::MEMBER])
            ->orderByDesc('id');

        return $this->paginate($query, UserResource::class);
    }

    // 添加会员
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|regex:/^1[3-9]\d{9}$/|unique:users,username',
            'password' => 'required|min:6',
        ]);
        $params = $request->only(['username', 'password', 'vip_id']);

        if (! empty($params['vip_id'])) {
            $params['start_at'] = now();
            $params['end_at'] = now()->addMonth();
        }

        $params['status'] = true;
        $params['type'] = UserType::MEMBER;
        $params['password'] = bcrypt($params['password']);

        User::query()->create($params);

        return $this->success();
    }

    // 修改用户、代理
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::query()->whereIn('type', [UserType::MEMBER, UserType::AGENT])->findOrFail($id);

        $params = $request->only(['status', 'vip_id', 'end_at', 'credit']);

        $user->update($params);

        return $this->success();
    }

    public function agent_tree()
    {
        $list = User::query()
            ->select(['id', 'username as label', 'parent_id'])
            ->where('type', UserType::AGENT)
            ->get()
            ->toArray();
        $list = $this->treeLevel($list, 0); // treeLevel

        return $this->success($list);
    }

    private function treeLevel($array, $pid): array
    {
        $tree = [];
        foreach ($array as $key => $value) {
            if ($value['parent_id'] == $pid) {
                $value['children'] = $this->treeLevel($array, $value['id']);
                if (! $value['children']) {
                    unset($value['children']);
                }
                $tree[] = $value;
            }
        }

        return $tree;
    }

    // 代理邀请记录
    public function invite(): JsonResponse
    {
        $query = User::query()
            ->where('parent_id', auth('api')->user()->id)
            ->orderByDesc('id');

        return $this->paginate($query, UserResource::class);
    }
}
