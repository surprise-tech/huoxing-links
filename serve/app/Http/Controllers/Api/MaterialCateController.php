<?php

namespace App\Http\Controllers\Api;

use App\Models\MaterialCategory;
use Illuminate\Http\JsonResponse;
use Ugly\Base\Http\Controllers\FormController;
use Ugly\Base\Services\FormService;

class MaterialCateController extends FormController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->success(MaterialCategory::query()->orderByDesc('sort')->get());
    }

    protected function form(): FormService
    {
        $form = FormService::make(MaterialCategory::class);
        $form->validate([
            'name' => 'required',
            'sort' => 'required',
            'type' => 'required',
        ]);
        $form->inlineEdit(['enable']);

        return $form;
    }
}
