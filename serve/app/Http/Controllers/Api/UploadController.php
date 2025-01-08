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

class UploadController extends Controller
{
    use ApiResource;

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
                },
                function ($query) {
                    $query->where('user_id', auth('api')->user()->id);
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

    public function store(Request $request)
    {
        if ($request->isNotFilled('cate_id') && $request->hasFile('image')) {
            $image = $request->file('image');
            $name = $image->getClientOriginalName();
            $path = $image->store(date('Y/m/d'));

            return $this->success([
                'path' => $path,
                'name' => $name,
            ]);
        }
        $request->validate([
            'image' => 'required',
            'name' => 'required',
            'cate_id' => 'required',
        ]);
        $cate_id = $request->input('cate_id');

        $user = auth('api')->user();
        if (
            $user->type != UserType::Admin &&
            MaterialCategory::query()->where('id', $cate_id)->where('type', MaterialCategoryType::System)->exists()
        ) {
            return $this->failed('没有权限');
        }
        $material = Material::query()->create([
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'path' => $request->input('image'),
            'cate_id' => $cate_id,
        ]);

        return $this->success($material->only(['id', 'name', 'path']));
    }

    public function upload(Request $request): JsonResponse
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store(date('Y/m/d'));

            return $this->success(['path' => $path]);
        }

        return $this->failed('上传失败！');
    }

    // 批量删除素材
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->all();
        if (count($ids) < 1) {
            return $this->failed('请选择要删除的素材');
        }

        Material::query()
            ->whereIn('id', $ids)
            ->when(
                auth('api')->user()->type !== UserType::Admin,
                fn ($query) => $query->where('user_id', auth('api')->user()->id),
            )
            ->delete();

        return $this->success();
    }

    public function upload_file(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:zip',
        ]);
        $file = $request->file('file');
        $path = $file->store(date('Y/m/d'));

        return $this->success([
            'path' => $path,
        ]);
    }
}
