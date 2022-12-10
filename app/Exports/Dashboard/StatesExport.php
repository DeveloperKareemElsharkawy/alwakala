<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\State;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StatesExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = State::query()

            ->select('states.id',
                'states.name_en',
                'states.name_ar',
                'states.region_id',
                'states.activation',
                'regions.name_en as region_name_en',
                'regions.name_ar as region_name_ar'
            )
            ->join('regions', 'regions.id', '=', 'states.region_id');

        if ($this->filterdata["name"]) {
            $searchQuery = "%" . $this->filterdata["name"] . "%";
            $query->where('states.name_ar', "ilike", $searchQuery)
                ->orWhere('states.name_en', "ilike", $searchQuery);
        }
        if ($this->filterdata['activation']) {
            $query->where('states.activation', $this->filterdata['activation']);
        }
        if ($this->filterdata['region']) {
            $query->where('states.region_id', intval($this->filterdata['region']));
        }
        if ($this->filterdata['id']) {
            $query->where('states.id', intval($this->filterdata['id']));
        }
        if ($this->filterdata["sort_by_name_ar"]) {

            $query->orderBy('states.name_ar', $this->filterdata['sort_by_name_ar']);
        }
        if ($this->filterdata["sort_by_name_en"]) {

            $query->orderBy('states.name_en', $this->filterdata['sort_by_name_en']);
        }
        if ($this->filterdata['sort_by_activation']) {
            $query->orderBy('states.activation', $this->filterdata['sort_by_activation']);
        }
        if ($this->filterdata['sort_by_region']) {
            $query->orderBy('states.region_id', $this->filterdata['sort_by_region']);
        }
        if ($this->filterdata['sort_by_id']) {
            $query->orderBy('states.id', $this->filterdata['sort_by_id']);
        }

        return $query->get();

    }

    /**
     * @return array
     * @var Region $region
     */
    public function map($region): array
    {

        return [
            $region->id,
            $region->name_ar,
            $region->name_en,
            $this->lang == 'ar' ? $region->region->name_ar ?? ''  : $region->region->name_en ?? '',
            $region->activation,
            $region->created_at,
            $region->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name_ar'),
            trans('form_fields.general.name_en'),
            trans('form_fields.general.region'),
            trans('form_fields.general.status'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
