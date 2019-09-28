<?php

namespace App\Services;

use App\Category;

class CategoryService
{
    /**
     * 获取类目下所有子类
     *
     * @param null $parentId 类目父类 id,null 表示要取出所有根类目
     * @param null $allCategories 需要处理的类目，null 需要从新从库里查询
     */
    public function getCategoryTree($parentId = null, $allCategories = null)
    {
        if (is_null($allCategories)) {
            $allCategories = Category::all();
        }

        return $allCategories->where('parent_id', $parentId)
            ->map(function (Category $category) use ($allCategories) {
                $data = ['id' => $category->id, 'name' => $category->name];
                if ($category->is_directory) {
                    $data['children'] = $this->getCategoryTree($category->id, $allCategories);
                }

                return $data;
            });
    }
}
