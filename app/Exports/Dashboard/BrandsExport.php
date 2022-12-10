<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Brand;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BrandsExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = Brand::query()
            ->select('id', 'name_ar','name_en', 'activation', 'image')
            ->orderBy('updated_at', 'desc')
            ->with('categoryBrand');

        if ($this->filterdata['category']) {
            $category = $this->filterdata['category'];
            $query->whereHas('categoryBrand', function ($q) use ($category) {
                $q->where('brand_category.category_id', $category);
            });
        }
        if ($this->filterdata['name']) {
            $query->where('name_en', 'ilike', '%' . $request->query('name') . '%');
        }
        if ($this->filterdata['id']) {
            $query->where('id', intval($this->filterdata['id']));
        }
        if ($this->filterdata['activation']) {
            $query->where('activation', $this->filterdata['activation']);
        }
        if ($this->filterdata['sort_by_name']) {
            $query->orderBy('name_ar', $this->filterdata['sort_by_name']);
        }
        if ($this->filterdata['sort_by_id']) {
            $query->orderBy('id', ['sort_by_id']);
        }
        if ($this->filterdata['sort_by_activation']) {
            $query->orderBy('activation', $this->filterdata['sort_by_activation']);
        }

        return $query->get();

     }

    /**
     * @return array
     * @var Size $size
     */
    public function map($brand): array
    {
        $categories  = '';
        foreach ($brand->categoryBrand as $category){
            $categories .=  $category->name_en  .',';
        }

        return [
            $brand->id,
            $brand->name_ar,
            $brand->name_en,
            $categories,
            $brand->activation,
            $brand->created_at,
            $brand->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name_en'),
            trans('form_fields.general.name_ar'),
            trans('form_fields.general.categories'),
            trans('form_fields.general.status'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }


}
