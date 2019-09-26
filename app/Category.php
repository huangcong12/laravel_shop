<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'is_directory',
        'level',
        'path',
    ];

    protected $casts = [
        'is_directory' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听 Category 的创建事件，用于初始化 path 和 level 字段值
        static::creating(function (Category $category) {
            if (is_null($category->parent_id)) {
                // 将层级设为 0
                $category->level = 0;
                // 将 path 设为 -
                $category->path = '-';
            } else {
                // 将层级设为父类目的层级 + 1
                $category->level = $category->parent->level + 1;
                // 将 path 值设为父类目的 path 追加父类 ID 以及最后一个更上 -
                $category->path = $category->parent->path . $category->parent_id . '-';
            }
        });
    }

    /**
     * 父类
     */
    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 子类
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * 一对多产品
     *
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * 获取数组形式的 path 属性
     *
     * @return array
     */
    public function getPathIdsAttribute()
    {
        return array_filter(explode('-', trim($this->path, '-')));
    }

    /**
     * 取出所有祖先类目
     *
     * @return Builder[]|Collection
     */
    public function getAncestorsAttribute()
    {
        return Category::query()
            ->whereIn('id', $this->path_ids)
            ->orderBy('level')
            ->get();
    }

    /**
     * 获取全称
     *
     * @return mixed
     */
    public function getFullNameAttribute()
    {
        return $this->ancestors     // 获取所有祖先类目
        ->pluck('name')         // 取出所有祖先类目的 name 字段作为一个数组
        ->push($this->name)     // 将当前类目的 name 字段值加到数组的末尾
        ->implode('-');         // 使用 - 符号将数组的值组装成一个字符串
    }
}
