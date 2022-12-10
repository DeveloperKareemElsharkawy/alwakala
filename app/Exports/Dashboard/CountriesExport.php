<?php

namespace App\Exports\Dashboard;

use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Country;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CountriesExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = Country::query()
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
        if ($this->filterdata['iso']) {
            $query->where('iso', $this->filterdata['iso']);
        }
        if ($this->filterdata['country_code']) {
            $query->where('country_code', $this->filterdata['country_code']);
        }
        if ($this->filterdata["sort_by_name_ar"]) {
            $query->where('name_ar', $this->filterdata['sort_by_name_ar']);
        }
        if ($this->filterdata["sort_by_name_en"]) {
            $query->where('name_en', $this->filterdata['sort_by_name_en']);
        }
        if ($this->filterdata['sort_by_activation']) {
            $query->where('activation', $this->filterdata['sort_by_activation']);
        }
        if ($this->filterdata['sort_by_id']) {
            $query->where('id', $this->filterdata['sort_by_id']);
        }
        if ($this->filterdata['sort_by_iso']) {
            $query->where('iso', $this->filterdata['sort_by_iso']);
        }
        if ($this->filterdata['sort_by_country_code']) {
            $query->where('country_code', $this->filterdata['sort_by_country_code']);
        }

        return $query->get();
    }

    /**
     * @return array
     * @var Country $country
     */
    public function map($country): array
    {

        return [
            $country->id,
            $country->name_ar,
            $country->name_en,
            $country->activation,
            $country->created_at,
            $country->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name_ar'),
            trans('form_fields.general.name_en'),
            trans('form_fields.general.status'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}

