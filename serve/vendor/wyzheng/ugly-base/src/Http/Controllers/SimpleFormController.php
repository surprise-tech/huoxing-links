<?php

namespace Ugly\Base\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Ugly\Base\Contracts\SimpleForm;
use Ugly\Base\Traits\ApiResource;

/**
 * 简单表单控制器.
 */
class SimpleFormController extends Controller
{
    use ApiResource;

    /**
     * 表单配置.
     */
    protected array $form = [];

    /**
     * 获取表单默认值.
     *
     * @return JsonResponse|void
     */
    public function getForm(string $type)
    {
        if (isset($this->form[$type]) && isset(class_implements($this->form[$type])[SimpleForm::class])) {
            $form = new $this->form[$type];

            return $this->success($form->default());
        }
        abort(Response::HTTP_NOT_FOUND);
    }

    /**
     * 更新表单.
     *
     * @return JsonResponse|void
     *
     * @throws \Throwable
     */
    public function saveForm(Request $request, $type)
    {
        if (isset($this->form[$type]) && isset(class_implements($this->form[$type])[SimpleForm::class])) {
            return $this->success(
                DB::transaction(function () use ($request, $type) {
                    $form = new $this->form[$type];
                    $form->handle($form->policy($request));

                    return $form->default();
                })
            );
        }
        abort(Response::HTTP_NOT_FOUND);
    }
}
