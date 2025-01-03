<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function welcome()
    {
        try {
            DB::connection()->getPdo();
            if (DB::table('sys_configs')->count() < 1) {
                return redirect('install?step=1');
            }
        } catch (\Exception $e) {
            return redirect('install?step=1');
        }

        return view('welcome');
    }
}
