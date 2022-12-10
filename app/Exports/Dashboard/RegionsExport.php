<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Region;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegionsExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = Region::query()
            ->select('regions.id',
                'regions.name_en',
                'regions.name_ar',
                'regions.country_id',
                'regions.activation',
                'countries.name_en as country_name_en',
                'countries.name_ar as country_name_ar'
            )
            ->join('countries', 'countries.id', '=', 'regions.country_id');

        if ($this->filterdata["name"]) {
            $searchQuery = "%" . $this->filterdata["name"] . "%";
            $query->where('regions.name_ar', "ilike", $searchQuery)
                ->orWhere('regions.name_en', "ilike", $searchQuery);
        }
        if ($this->filterdata['id']) {
            $query->where('regions.id', intval($this->filterdata['id']));
        }
        if ($this->filterdata['activation']) {
            $query->where('regions.activation', $this->filterdata['activation']);
        }
        if ($this->filterdata['country']) {
            $query->where('regions.country_id', intval($this->filterdata['country']));
        }
        if ($this->filterdata["sort_by_name_ar"]) {
            $query->orderBy('regions.name_ar', $this->filterdata['sort_by_name_ar']);
        }
        if ($this->filterdata["sort_by_name_en"]) {
            $query->orderBy('regions.name_en', $this->filterdata['sort_by_name_en']);

        }
        if ($this->filterdata['sort_by_id']) {
            $query->orderBy('regions.id', $this->filterdata['sort_by_id']);
        }
        if ($this->filterdata['sort_by_activation']) {
            $query->orderBy('regions.activation', $this->filterdata['sort_by_activation']);
        }
        if ($this->filterdata['sort_by_country']) {
            $query->orderBy('regions.country_id', $this->filterdata['sort_by_country']);
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
            $this->lang == 'ar' ? $region->country->name_ar ?? ''  : $region->country->name_en ?? '',
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
            trans('form_fields.general.country'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}

