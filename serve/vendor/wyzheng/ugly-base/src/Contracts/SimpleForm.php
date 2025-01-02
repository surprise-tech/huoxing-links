<?php

namespace Ugly\Base\Contracts;

use Illuminate\Http\Request;

interface SimpleForm
{
    /**
     * 表单册罗.
     */
    public function policy(Request $request): array;

    /**
     * 处理表单.
     */
    public function handle(array $input);

    /**
     * 表单默认值.
     */
    public function default(): array;
}
