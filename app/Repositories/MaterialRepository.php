<?php


namespace App\Repositories;


use App\Http\Controllers\Controller;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Material;

class MaterialRepository extends Controller
{
    public static function showAll($request)
    {
        $query = Material::query()->select('id', 'name_ar', 'name_en');
        if ($request->filled('name')) {
            $query->where("name_ar", 'ilike', '%' . $request->get('name') . '%')
                ->orWhere("name_en", 'ilike', '%' . $request->get('name') . '%');
        }
        if ($request->filled('sort_by_id')) {
            $query->orderBy('id', $request->sort_by_id);
        }
        if ($request->filled('sort_by_name_ar')) {
            $query->orderBy('name_ar', $request->sort_by_name_ar);
        }
        if ($request->filled('sort_by_name_en')) {
            $query->orderBy('name_en', $request->sort_by_name_en);
        }
        $query->orderByRaw('materials.updated_at DESC NULLS LAST');
        return $query->get();
    }

    public static function store($request)
    {
        $newRow = new Material();
        $newRow->name_ar = $request->name_ar;
        $newRow->name_en = $request->name_en;
        $newRow->save();
        return $newRow;
    }

    public static function update($request)
    {
        $material = Material::query()->where('id', $request->id)->first();
        $material->name_ar = $request->name_ar;
        $material->name_en = $request->name_en;
        $material->save();
        return $material;
    }

    public static function destroy($id)
    {
        Material::destroy($id);
    }

    public static function show($id)
    {
        return Material::query()->where('id', $id)->first();
    }
}
