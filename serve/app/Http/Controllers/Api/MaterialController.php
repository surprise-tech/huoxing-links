<?php

namespace App\Http\Controllers\Api;

use App\Enums\MaterialCategoryType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ugly\Base\Traits\ApiResource;

class MaterialController extends Controller
{
    use ApiResource;

    // 列表
    public function index(): JsonResponse
    {
        $query = Material::search(['name' => 'like', 'cate_id' => '='])
            ->when(
                auth('api')->user()->type !== UserType::Admin,
                function ($query) {
                    $query->where(function ($query) {
                        $query->where('user_id', auth('api')->user()->id)
                            ->orWhereHas('cate', function ($query) {
                                $query->where('type', MaterialCategoryType::System->value);
                            });
                    });
                }
            )
            ->orderBy('user_id')
            ->orderByDesc('id');

        return $this->paginate($query, fn ($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'path' => $item->path,
        ]);
    }

    // 新增
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:3072',
        ]);
        $image = $request->file('image');

        $cate_id = MaterialCategory::query()
            ->where('type', MaterialCategoryType::Other)
            ->value('id');

        $material = Material::query()->create([
            'user_id' => $request->user('api')->id,
            'name' => $image->getClientOriginalName(),
            'path' => $image->store(date('Y/m/d')),
            'cate_id' => $cate_id,
        ]);

        return $this->success($material->only(['id', 'name', 'path']));
    }

    // 修改名称
    public function update(Request $request, Material $material): JsonResponse
    {
        if ($material->user_id != $request->user('api')->id) {
            return $this->failed('无权限');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $material->name = $request->input('name');
        $material->save();

        return $this->success($material->only(['id', 'name', 'path']));
    }

    // 删除
    public function destroy(Material $material): JsonResponse
    {
        if ($material->user_id != auth('api')->user()->id) {
            return $this->failed('无权限');
        }
        $material->delete();

        return $this->success();
    }
}
