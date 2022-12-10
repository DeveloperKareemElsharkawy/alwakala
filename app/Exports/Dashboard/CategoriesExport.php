<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriesExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
{
    use Exportable;


    public function __construct($filterdata = [])
    {
        $this->filterdata = $filterdata;
        $this->lang = LangHelper::getDefaultLang($filterdata);
    }


    /**
     * @return Collection
     */
    public function collection()
    {
        $query = Category::query()
            ->with('parent')
            ->select('id', 'name_ar', 'name_en', 'category_id', 'activation')
            ->orderBy('updated_at', 'desc');


        if ($this->filterdata["name"]) {
            $searchQuery = "%" . $this->filterdata["name"] . "%";
            $query->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }
        if ($this->filterdata['activation']) {
            $query->where('activation', $this->filterdata['activation']);
        }
        if ($this->filterdata['id']) {
            $query->where('id', intval($this->filterdata['id']));
        }
        if ($this->filterdata['category_id']) {
            $query->where('category_id', intval($this->filterdata['category_id']));
        }
        if ($this->filterdata['sort_by_name_ar']) {
            $query->orderBy('name_ar', $this->filterdata['sort_by_name_ar']);
        }
        if ($this->filterdata['sort_by_name_en']) {
            $query->orderBy('name_en', $this->filterdata['sort_by_name_en']);
        }
        if ($this->filterdata['sort_by_activation']) {
            $query->orderBy('activation', $this->filterdata['sort_by_activation']);
        }
        if ($this->filterdata['sort_by_id']) {
            $query->orderBy('id', $this->filterdata['sort_by_id']);
        }
        if ($this->filterdata['sort_by_category']) {
            $query->orderBy('category_id', $this->filterdata['sort_by_category']);
        }

        return $query->get();

     }



    /**
     * @return array
     * @var category $category
     */
    public function map($category): array
    {
        return [
            $category->id,
            $category->name_ar,
            $category->name_en,
            $this->lang == 'ar' ? $category->parent->name_ar ?? ''  : $category->parent->name_en ?? '',
            $category->activation,
            $category->created_at,
            $category->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.categories.name_ar'),
            trans('form_fields.categories.name_en'),
            trans('form_fields.categories.parent'),
            trans('form_fields.general.status'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }


}
