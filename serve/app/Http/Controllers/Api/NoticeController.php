<?php

namespace App\Http\Controllers\Api;

use App\Models\Notice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ugly\Base\Http\Controllers\FormController;
use Ugly\Base\Services\FormService;

class NoticeController extends FormController
{
    public function index(): JsonResponse
    {
        $query = Notice::search(['title' => 'like', 'type' => '='])->orderByDesc('id');

        return $this->paginate($query);
    }

    protected function form(): FormService
    {
        $form = FormService::make(Notice::class);
        $form->validate([
            'title' => 'required',
            'content' => 'required',
            'show' => 'required|boolean',
            'sort' => 'required|integer|min:0',
        ]);

        $form->inlineEdit(['sort', 'show']);

        return $form;
    }

    public function get_notice(): JsonResponse
    {
        return $this->success(Notice::query()->orderByDesc('id')->first());
    }

    public function set_notice(Request $request): JsonResponse
    {
        $rule = [
            'title' => 'required',
            'content' => 'required',
            'show' => 'required|boolean',
        ];
        $request->validate($rule);
        $data = $request->only(array_keys($rule));
        $notice = Notice::query()->orderByDesc('id')->first();
        if (empty($notice)) {
            Notice::query()->create($data);
        } else {
            $notice->update($data);
        }

        return $this->success();
    }
}
