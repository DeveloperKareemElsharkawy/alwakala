<?php


namespace App\Lib\Helpers\Categories;


use App\Models\Address;
use App\Models\Category;
use App\Models\Store;

class CategoryHelper
{

    public static function checkCategoryLevel($level, $category_id)
    {
        switch ($level) {
            case "parent":
                $prent = Category::query()->whereNull('category_id')->find($category_id);
                if ($prent) {
                    return true;
                }
                return false;

                break;
            case "sub_category":
                $subCategory = Category::query()->with('parent')->whereNotNull('category_id')->find($category_id);
                $parent = $subCategory->parent;
                if ($subCategory && $parent && $parent->category_id == null) {
                    return true;
                }
                return false;

                break;
            case "sub_sub_category":
                $subCategory = Category::query()->with('parent')->whereNotNull('category_id')->find($category_id);
                $parent = $subCategory->parent ?? false;
                if ($subCategory && $parent && $parent->category_id != null) {
                    return true;
                }
                return false;
                break;
        }
    }

}
