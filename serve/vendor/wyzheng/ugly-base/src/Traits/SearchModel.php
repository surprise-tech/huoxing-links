<?php

namespace Ugly\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 *  扩展laravel的ORM，使其拥有简单的搜索能力.
 *
 * @mixin Model
 */
trait SearchModel
{
    /**
     * 搜索.
     *
     * @param  array  $search 搜索条件
     *                      1、['name' => 'like'] PS: name like '%xxx%'
     *                      2、['category_id' => ['=', 'cate_id']]   PS: category_id = request('cate_id')
     *                      3、['type' => fn($query, $input) => $query->where('type', $input)] PS: 自定义查询
     *                      5、['admin.name' => 'like'] PS: whereHas('admin', fn($q) => $->where('name', 'like', '%xxx%')) PS: 关联查询
     */
    final public static function search(array $search = [], array|string $relations = []): Builder
    {
        return self::with($relations)->where(function ($query) use ($search) {
            // 获取请求参数
            foreach ($search as $field => $operator) {
                $fieldInfo = pathinfo($field);
                $isWhereHas = isset($fieldInfo['extension']);

                // whereHas 默认key PS: 'admin.name' 转换成 'admin_name'
                if ($isWhereHas) {
                    $field = str_replace('.', '_', $field);
                }

                // 自定义 key 优先级最高
                $field = is_array($operator) ? $operator[1] : $field;

                // 获取请求参数
                $input = request($field);

                // 跳过空值
                if (blank($input)) {
                    continue;
                }

                // 自定义查询
                if ($operator instanceof \Closure) {
                    $query->where(fn ($q) => call_user_func($operator, $q, $input));

                    continue; // 进入下一个查询参数
                }

                // 操作符号
                $operator = is_array($operator) ? $operator[0] : $operator; // =, >, >=, <,<=, like, in, between
                switch ($operator) {
                    case 'in':
                    case 'between':
                        $method = 'where'.ucfirst($operator);
                        $args = [is_array($input) ? $input : explode(',', $input)];
                        break;
                    case 'like':
                        $method = 'where';
                        $args = ['like', '%'.$input.'%'];
                        break;
                    default:
                        $method = 'where';
                        $args = [$operator, $input];
                        break;
                }

                if ($isWhereHas) {
                    $query->whereHas($fieldInfo['filename'], fn ($q) => $q->$method($fieldInfo['extension'], ...$args));
                } else {
                    $query->$method($fieldInfo['filename'], ...$args);
                }
            }
        });
    }
}
