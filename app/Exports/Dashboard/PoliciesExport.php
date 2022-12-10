<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Policy;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PoliciesExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
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
        $query = Policy::query()
            ->select('id', 'name_en', 'name_ar', 'description_en', 'description_ar', 'created_at')
            ->orderBy('id', 'desc');
        if ($this->filterdata["id"]) {
            $query->where('id', intval($this->filterdata['id']));
        }
        if ($this->filterdata['name']) {
            $searchQuery = "%" . $this->filterdata['name'] . "%";
            $query->where('name_ar', "ilike", $searchQuery)
                ->orWhere('name_en', "ilike", $searchQuery);
        }
        return $query->get();
    }

    /**
     * @return array
     * @var Store $store
     */
    public function map($store): array
    {

        return [
            $store->id,
            $store->name_en,
            $store->name_ar,
            $store->description_en,
            $store->description_ar,
            $store->created_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name_en'),
            trans('form_fields.general.name_ar'),
            trans('form_fields.general.description_en'),
            trans('form_fields.general.description_ar'),
            trans('form_fields.general.created_at'),
        ];
    }
}
