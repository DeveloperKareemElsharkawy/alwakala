<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Color;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ColorsExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = Color::query()
            ->orderBy('updated_at', 'desc');

        if ($this->filterdata['name']) {
            $query->where('name_ar', 'ilike', '%' . $this->filterdata['name'] . '%')
                ->orWhere('name_en', 'ilike', '%' . $this->filterdata['name'] . '%');
        }
        if ($this->filterdata['sort_by_name_en']) {
            $query->orderBy('name_en', $this->filterdata['sort_by_name_en']);
        }
        if ($this->filterdata['sort_by_name_ar']) {
            $query->orderBy('name_ar', $this->filterdata['sort_by_name_ar']);
        }

        if ($this->filterdata['sort_by_id']) {
            $query->orderBy('id', $this->filterdata['sort_by_id']);
        }

        return $query->get();
    }

    /**
     * @return array
     * @var Color $color
     */
    public function map($color): array
    {

        return [
            $color->id,
            $color->name_ar,
            $color->name_en,
            $color->hex,
            $color->created_at,
            $color->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name_ar'),
            trans('form_fields.general.name_en'),
            trans('form_fields.general.hex'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
