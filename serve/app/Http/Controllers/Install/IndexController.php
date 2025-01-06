<?php

namespace App\Http\Controllers\Install;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Ugly\Base\Traits\ApiResource;

class IndexController extends Controller
{
    use ApiResource;

    // 安装
    public function index(Request $request)
    {
        $step = $request->get('step', 1);

        try {
            if (DB::table('sys_configs')->count() > 0) {
                return redirect('/');
            }
        } catch (\Exception $e) {
        }

        $is_goon = true;
        $data = [
            'step' => $step,
            'nextStep' => $is_goon ? $step + 1 : $step,
            'lastStep' => $step < 1 ? 1 : $step - 1,
        ];
        if ($step == '2') {
            $data = array_merge($data, [
                'nextStep' => $is_goon ? $step + 1 : $step,
                'is_goon' => $is_goon,
                'chk' => $this->base_ckh(),
            ]);
        }

        if ($step == '3') {
            $chks = $this->base_ckh();
            foreach ($chks as $index => $chk) {
                if ($index != 'fred_disk_space' && $chk != '✓') {
                    return redirect('install?step='.$step - 1)
                        ->withErrors("{$index} 检测未同通过")
                        ->withInput();
                }
            }

            $data = array_merge($data, [
                'is_goon' => $is_goon,
                'post' => [
                    'host' => $_POST['host'] ?? '127.0.0.1',
                    'port' => $_POST['port'] ?? '3306',
                    'user' => $_POST['user'] ?? 'root',
                    'password' => $_POST['password'] ?? '',
                    'name' => $_POST['name'] ?? 'huoxing_db',
                    'admin_user' => $_POST['admin_user'] ?? '',
                    'admin_password' => $_POST['admin_password'] ?? '',
                    'admin_confirm_password' => $_POST['admin_confirm_password'] ?? '',
                ],
            ]);
        }
        if ($step == '4') {
            $rule = [
                'host' => 'required',
                'port' => 'required',
                'user' => 'required',
                'password' => 'required',
                'name' => 'required',
                'admin_user' => 'required',
                'admin_password' => 'required|string|min:6|confirmed',
            ];
            $validator = Validator::make($request->all(), $rule, [
                'host.required' => '数据库主机不能为空',
                'port.required' => '端口号不能为空',
                'user.required' => '数据库用户不能为空',
                'password.required' => '数据库密码不能为空',
                'name.required' => '数据库名称不能为空',
                'admin_user.required' => '管理员账号不能为空',
                'admin_password.required' => '管理员密码不能为空',
                'admin_password.min' => '管理员密码不能少于6位',
                'admin_password.confirmed' => '确认密码不一致',
            ]);
            if ($validator->fails()) {
                return redirect('install?step='.$step - 1)
                    ->withErrors($validator)
                    ->withInput();
            }

            $params = $request->only(array_keys($rule));

            // 临时修改数据库配置
            config([
                'database.connections.test' => [
                    'driver' => 'mysql',
                    'host' => $params['host'],
                    'port' => $params['port'],
                    'database' => $params['name'],
                    'username' => $params['user'],
                    'password' => $params['password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                ],
            ]);

            try {
                // 使用自定义配置创建数据库连接
                DB::connection('test')->getPdo();

            } catch (\Exception $e) {
                return redirect('install?step='.$step - 1)
                    ->withErrors('数据库链接不正确')
                    ->withInput();
            }

            try {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $domain = $protocol.'://'.$_SERVER['HTTP_HOST'];
                $this->updateEnv('APP_URL', $domain);
                $this->updateEnv('DB_HOST', $params['host']);
                $this->updateEnv('DB_PORT', $params['port']);
                $this->updateEnv('DB_DATABASE', $params['name']);
                $this->updateEnv('DB_USERNAME', $params['user']);
                $this->updateEnv('DB_PASSWORD', $params['password']);

                $base_path = base_path();
                $new_path = str_replace('/serve', '/admin/dist/config.js', $base_path);
                $content = File::get($new_path);
                if (!str_contains($content, $domain)) {
                    File::put($new_path, "window.config = {
  // 你的后端域名
  url: '{$domain}/api'
}");
                }

            } catch (\Exception $e) {
                return redirect('install?step='.$step - 1)
                    ->withErrors($e->getMessage())
                    ->withInput();
            }
            sleep(1);
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('storage:link');
            sleep(1);
            try {
                DB::connection('test')->unprepared(file_get_contents(database_path('base.sql')));
                DB::connection('test')->table('users')->insert([
                    'username' => $params['admin_user'],
                    'password' => bcrypt($params['admin_password']),
                    'type' => UserType::Admin,
                    'referral_code' => '100000',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            } catch (\Exception $e) {
                return redirect('install?step='.$step - 1)
                    ->withErrors('安装失败！:'.$e->getMessage())
                    ->withInput();
            }

            $data = array_merge($data, [
                'is_goon' => $is_goon,
            ]);
        }

        $step = $step > 4 ? 1 : $step;

        return view("install/step{$step}", $data);
    }

    public function updateEnv($search, $replace): void
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $content = file_get_contents($path);

            $content = preg_replace(
                '/^'.$search.'=(.*)$/m',
                $search.'='.$replace,
                $content
            );

            file_put_contents($path, $content);
        }
    }

    private function base_ckh(): array
    {
        return [
            'php_version' => version_compare(PHP_VERSION, '7.2.0') >= 0 ? '✓' : '×',
            'fred_disk_space' => $this->fred_disk_space(),
            'pdo_mysql' => extension_loaded('pdo_mysql') ? '✓' : '×',
            'curl' => extension_loaded('curl') ? '✓' : '×',
            'gd2' => extension_loaded('gd') ? '✓' : '×',
            'fileinfo' => extension_loaded('fileinfo') ? '✓' : '×',
            'putenv' => function_exists('putenv') ? '✓' : '×',
            'symlink' => function_exists('symlink') ? '✓' : '×',
            'proc_open' => function_exists('proc_open') ? '✓' : '×',
            'log' => File::isWritable(storage_path('logs')) ? '✓' : '×',
            'upload' => File::isWritable(storage_path('app')) ? '✓' : '×',
            'cache' => File::isWritable(storage_path('framework')) ? '✓' : '×',
            'env' => File::isWritable(base_path()) ? '✓' : '×',
        ];
    }

    private function fred_disk_space(): string
    {
        // M
        $freeDiskSpace = disk_free_space(realpath(__DIR__)) / 1024 / 1024;

        // G
        if ($freeDiskSpace > 1024) {
            return number_format($freeDiskSpace / 1024, 2).'G';
        }

        return number_format($freeDiskSpace, 2).'M';
    }
}
