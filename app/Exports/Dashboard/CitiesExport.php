<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\City;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CitiesExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = City::query()
            ->select('cities.id',
                'cities.name_en',
                'cities.name_ar',
                'cities.state_id',
                'cities.activation',
                'states.name_en as state_name_en',
                'states.name_ar as state_name_ar'
            )
            ->join('states', 'states.id', '=', 'cities.state_id');

        if ($this->filterdata["name"]) {
            $searchQuery = "%" . $this->filterdata["name"] . "%";
            $query->where('cities.name_ar', "ilike", $searchQuery)
                ->orWhere('cities.name_en', "ilike", $searchQuery);
        }
        if ($this->filterdata['activation']) {
            $query->where('cities.activation', $this->filterdata['activation']);
        }
        if ($this->filterdata['state']) {
            $query->where('cities.state_id', intval($this->filterdata['state']));
        }
        if ($this->filterdata['id']) {
            $query->where('cities.id', intval($this->filterdata['id']));
        }
        if ($this->filterdata["sort_by_name_ar"]) {
            $query->orderBy('cities.name_ar', $this->filterdata['sort_by_name_ar']);
        }
        if ($this->filterdata["sort_by_name_en"]) {
            $query->orderBy('cities.name_en', $this->filterdata['sort_by_name_en']);
        }
        if ($this->filterdata['sort_by_activation']) {
            $query->orderBy('cities.activation', $this->filterdata['sort_by_activation']);
        }
        if ($this->filterdata['sort_by_state']) {
            $query->orderBy('cities.state_id', $this->filterdata['sort_by_state']);
        }
        if ($this->filterdata['sort_by_id']) {
            $query->orderBy('cities.id', $this->filterdata['sort_by_id']);
        }

        return $query->get();

     }

    /**
     * @return array
     * @var City $cities
     */
    public function map($cities): array
    {

        return [
            $cities->id,
            $cities->name_ar,
            $cities->name_en,
            $cities->lang == 'ar' ? $cities->state->name_ar ?? ''  : $cities->state->name_en ?? '',
            $cities->activation,
            $cities->created_at,
            $cities->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name_ar'),
            trans('form_fields.general.name_en'),
            trans('form_fields.general.state'),
            trans('form_fields.general.status'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
