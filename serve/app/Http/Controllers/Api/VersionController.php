<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Version;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ugly\Base\Traits\ApiResource;

class VersionController extends Controller
{
    use ApiResource;

    public function version(Request $request): JsonResponse
    {
        $request->validate([
            'version' => 'required|integer',
        ]);

        $version = Version::query()->where('version','>', $request->input('version'))->first();

        return $this->success($version);
    }

    public function index(): JsonResponse
    {
        $query = Version::query()->orderByDesc('id');

        return $this->paginate($query);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'version_number' => 'required|string',
            'version' => 'required|integer',
            'path' => 'required|string',
        ];
        $request->validate($rules);

        $last_version = Version::query()->orderByDesc('id')->value('version');
        $version = (int) request()->input('version');
        if (! empty($last_version) && $last_version > $version) {
            return $this->failed("当前版本号{$version}应大于最后一次版本编号{$last_version}");
        }
        $data = $request->only(array_keys($rules));

        if (strpos('http',$data['path']) === false) {
            $data['path'] = Storage::url($data['path']);
        }

        Version::query()->create($data);

        return $this->success();
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $rules = [
            'version_number' => 'required|string',
            'path' => 'required|string',
        ];
        $request->validate($rules);

        $data = $request->only(array_keys($rules));
        if (strpos('http',$data['path']) === false) {
            $data['path'] = Storage::url($data['path']);
        }

        Version::query()->where('id', $id)->update($data);

        return $this->success();
    }

    public function show(string $id): JsonResponse
    {
        return $this->success(Version::query()->findOrFail($id));
    }

    public function destroy(string $id): JsonResponse
    {
        Version::query()->where('id', $id)->delete();

        return $this->success();
    }
}
