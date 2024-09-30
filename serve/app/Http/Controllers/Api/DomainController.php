<?php

namespace App\Http\Controllers\Api;

use App\Models\Domain;
use Illuminate\Http\JsonResponse;
use Ugly\Base\Http\Controllers\FormController;
use Ugly\Base\Services\FormService;

class DomainController extends FormController
{
    public function index(): JsonResponse
    {
        return $this->success(Domain::search(['enable' => '='])->get());
    }

    protected function form(): FormService
    {
        $form = FormService::make(Domain::class);
        $form->validate([
            'url' => 'required|url',
            'title' => 'required',
            'enable' => 'required|boolean',
        ]);
        $form->inlineEdit(['enable']);

        return $form;
    }
}
