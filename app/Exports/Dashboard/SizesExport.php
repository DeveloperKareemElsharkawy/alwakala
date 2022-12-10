<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Size;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SizesExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = Size::query()
            ->select('id', 'size', 'size_type_id')
            ->with('sizeType')
            ->with('categories')
            ->orderBy('updated_at', 'desc');

        if ($this->filterdata['id']) {
            $query->where('id', intval($this->filterdata['id']));
        }

        if ($this->filterdata['category_id']) {
            $category_id = $this->filterdata['category_id'];
            $query->whereHas('categories', function ($query) use ($category_id) {
                $query->where("category_size.category_id", intval($category_id));
            });
        }
        if ($this->filterdata['size']) {
            $size = "%" . $this->filterdata['size'] . "%";
            $query->where('size', "ilike", $size);
        }
        if ($this->filterdata['size_type_id']) {
            $query->where('size_type_id', intval($this->filterdata['size_type_id']));
        }
        if ($this->filterdata['sort_by_id']) {
            $query->orderBy('id', $this->filterdata['sort_by_id']);
        }

        if ($this->filterdata['sort_by_size']) {
            $query->orderBy('size', $this->filterdata['sort_by_size']);
        }
        if ($this->filterdata['sort_by_size_type_id']) {
            $query->orderBy('size_type_id', $this->filterdata['sort_by_size_type_id']);
        }

        return$query->get();

     }

    /**
     * @return array
     * @var Size $size
     */
    public function map($size): array
    {
        $categories = '';
        foreach ($size->categories as $category){
            $categories .= ($this->lang == 'ar' ? $category->name_ar ?? ''  : $category->name_en ?? '' ) .',';
        }

        return [
            $size->id,
            $size->size,
            $this->lang == 'ar' ? $size->sizeType->type_ar ?? ''  : $size->sizeType->type_en ?? '',
            $categories,
            $size->created_at,
            $size->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.sizes.size'),
            trans('form_fields.sizes.size_type'),
            trans('form_fields.sizes.size_categories'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }


}
