<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Material;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class MaterialsExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = Material::query();

        if ($this->filterdata['name']) {
            $query->where('name_ar', 'ilike', '%' . $this->filterdata['name'] . '%')
                ->orWhere('name_en', 'ilike', '%' . $this->filterdata['name'] . '%');
        }

        return $query->get();
    }

    /**
     * @return array
     * @var Color $color
     */
    public function map($material): array
    {

        return [
            $material->id,
            $material->name_ar,
            $material->name_en,
            $material->created_at,
            $material->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name_ar'),
            trans('form_fields.general.name_en'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
