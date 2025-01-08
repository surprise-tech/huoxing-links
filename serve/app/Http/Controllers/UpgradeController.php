<?php

namespace App\Http\Controllers;

use App\Services\SystemConfig;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Ugly\Base\Traits\ApiResource;

class UpgradeController extends Controller
{
    use ApiResource;

    // 升级
    public function index(Request $request): JsonResponse
    {
        $domain = SystemConfig::get('upgrade_domain');
        $system_version = SystemConfig::get('system_version');
        $url = "{$domain}/api/news_version?version={$system_version['version']}";
        $res = Http::get($url);
        $new_version = $res->json();
        if (empty($new_version['path'])) {
            return $this->success(['message' => '没有要更新的版本']);
        }

        $file = basename($new_version['path']); // 本地文件

        // 下载文件到本地
        $client = new Client([
            'timeout' => 600.0,        // 设置总的请求超时时间为 60 秒
            'connect_timeout' => 30.0, // 设置连接超时时间为 30 秒
        ]);
        $response = $client->get($new_version['path']);
        if ($response->getStatusCode() == 200) {
            Storage::put($file, $response->getBody());
        }
        $base_path = base_path();
        $base_arr = explode('/', $base_path);
        $count = count($base_arr);
        $server_str = $base_arr[$count - 1];
        $extractPath = str_replace("/{$server_str}", '', base_path()); // 解压目标目录
        $zipFilePath = Storage::path($file); // ZIP 文件的路径
        // 创建解压目录（如果不存在）
        if (! file_exists($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        // 解压 ZIP 文件
        $zip = new \ZipArchive;
        if ($zip->open($zipFilePath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();

            SystemConfig::set(['system_version' => [
                'version_number' => $new_version['version_number'],
                'version' => $new_version['version'],
            ]]);

            return $this->success();
        }

        return $this->failed();
    }
}
