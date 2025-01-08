<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    public static function qr(string $path)
    {
        $contents = Storage::get($path);

        $tmpPath = tempnam(sys_get_temp_dir(), 'qr');
        $handle = fopen($tmpPath, 'w');
        fwrite($handle, $contents);
        fclose($handle);

        $command = sprintf('python3 %s %s', base_path('services/wechatqr-opencv/cli.py'), $tmpPath);
        $scanResult = Process::timeout(20)->run($command);
        unlink($tmpPath);

        if ($scanResult->failed()) {
            Log::error('微信二维码识别失败', ['path' => $path, 'error' => $scanResult->output()]);

            return false;
        }

        return trim($scanResult->output());
    }
}
