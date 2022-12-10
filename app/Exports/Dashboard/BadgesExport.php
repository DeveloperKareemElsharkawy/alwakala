<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Badge;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BadgesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
        $query = Badge::query()
            ->with('color')
            ->select('id', 'name_en', 'name_ar', 'color_id', 'icon', 'is_product', 'is_seller', 'is_store', 'activation')
            ->orderBy('updated_at', 'desc');

        if ($this->filterdata["name"]) {
            $searchQuery = "%" . $this->filterdata["name"] . "%";
            $query->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }
        if ($this->filterdata["name_en"]) {
            $searchQuery = "%" . $this->filterdata["name_en"] . "%";
            $query->where('name_en', "ilike", $searchQuery);
        }
        if ($this->filterdata["name_ar"]) {
            $searchQuery = "%" . $this->filterdata["name_en"] . "%";
            $query->where('name_en', "ilike", $searchQuery);
        }
        if ($this->filterdata['activation']) {
            $query->where('activation', $this->filterdata['activation']);
        }
        if ($this->filterdata['color_name_en']) {
            $query->where('color_id', $this->filterdata['color_name_en']);
        }
        if ($this->filterdata['is_seller']) {
            $query->where('is_seller', $this->filterdata['is_seller']);
        }
        if ($this->filterdata['is_store']) {
            $query->where('is_store', $this->filterdata['is_store']);
        }
        if ($this->filterdata['is_product']) {
            $query->where('is_product', $this->filterdata['is_product']);
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
            $category->color ? $category->color->name_en : '',
            $category->name_ar,
            $category->name_en,
            $category->is_product,
            $category->is_seller,
            $category->is_store,
            $category->activation,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.color'),
            trans('form_fields.categories.name_ar'),
            trans('form_fields.categories.name_en'),
            trans('form_fields.general.is_product'),
            trans('form_fields.general.is_seller'),
            trans('form_fields.general.is_store'),
            trans('form_fields.general.activation'),
        ];
    }
}
