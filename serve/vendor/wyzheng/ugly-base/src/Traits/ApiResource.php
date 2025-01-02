<?php

namespace Ugly\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * 统一http响应.
 */
trait ApiResource
{
    /**
     * 成功响应.
     *
     * @param  mixed  ...$args
     *
     * @example
     *  1、$this->success(); 状态码 200的空响应
     *  2、$this->success(201); 状态码 201的空响应
     *  3、$this->success(['id' => 1]); 状态码 200，携带数据的响应
     *  4、$this->success(['id' => 1], 201); 状态码 201，携带数据的响应
     */
    final public function success(...$args): JsonResponse
    {
        $argsLength = count($args);
        if (count($args) > 2) {
            throw new \InvalidArgumentException('参数个数不能超过2个');
        }
        $data = [];
        $httpCode = Response::HTTP_OK;
        if ($argsLength === 1) {
            if (is_int($args[0])) {
                // 自定义状态码 $this->success(201);
                $httpCode = $args[0];
            } else {
                // 自定义携带的数据 $this->success(['id' => 1]);
                $data = $args[0];
            }
        } elseif ($argsLength === 2) {
            // 自定义状态码和携带的数据 $this->success(['id' => 1], 201);
            $data = $args[0];
            $httpCode = $args[1];
        }

        return response()->json($data, $httpCode);
    }

    /**
     * 失败响应.
     *
     * @param  string  $msg 失败信息
     * @param  int  $code 失败码
     * @param  int  $httpCode http状态码
     */
    final public function failed(string $msg = '操作失败', int $code = 400, int $httpCode = null): JsonResponse
    {
        if (is_null($httpCode)) {
            // 默认使用code作为http状态码
            $httpCode = $code;
            if ($httpCode < 100 || $httpCode >= 600) {
                // http不合法时，默认使用400
                $httpCode = Response::HTTP_BAD_REQUEST;
            }
        }

        return response()->json([
            'code' => $code,
            'message' => $msg,
        ], is_null($httpCode) ? $code : $httpCode);
    }

    /**
     * 分页响应.
     *
     * @param  Builder  $query 数据库查询构造器
     * @param  null|\Closure|string  $resource 资源转换类或者闭包
     * @param  array  $meta 额外的元数据
     */
    final public function paginate(Builder $query, \Closure|string $resource = null, array $meta = []): JsonResponse
    {
        $data = [];
        $page = request()->integer('page', 1);
        $page_size = min(request()->integer('page_size', 15), 100);
        $total = $query->count();
        $queryData = $query->skip(($page - 1) * $page_size)->take($page_size)->get();

        if ($resource === null) {
            // 不需要转换
            $data['data'] = $queryData;
        } elseif ($resource instanceof \Closure && method_exists($queryData, 'transform')) {
            // 闭包转换
            $data['data'] = $queryData->transform($resource);
        } elseif (method_exists($resource, 'collection')) {
            // 资源转换
            $data['data'] = call_user_func([$resource, 'collection'], $queryData);
        }

        $data['meta'] = array_merge([
            'total' => $total,
            'current_page' => $page,
            'last_page' => ceil($total / $page_size),
        ], $meta);

        return response()->json($data);
    }
}
