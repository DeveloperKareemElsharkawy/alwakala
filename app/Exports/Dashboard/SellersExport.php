<?php

namespace App\Exports\Dashboard;

use App\Enums\UserTypes\UserType;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SellersExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
        $query = User::query()
            ->select('id', 'name', 'email', 'mobile', 'activation')
            ->orderBy('updated_at', 'desc');

        if ($this->filterdata["id"]) {
            $query->where('id', intval($this->filterdata['id']));
        }
        if ($this->filterdata["name"]) {
            $searchQuery = "%" . $this->filterdata["name"] . "%";
            $query->where('name', "ilike", $searchQuery);
        }
        if ($this->filterdata["email"]) {
            $searchQuery = "%" . $this->filterdata["email"] . "%";
            $query->where('email', "ilike", $searchQuery);
        }
        if ($this->filterdata["mobile"]) {
            $searchQuery = "%" . $this->filterdata["mobile"] . "%";
            $query->where('mobile', "ilike", $searchQuery);
        }
        if ($this->filterdata["activation"]) {
            $query->where('activation', $this->filterdata['activation']);
        }
        if ($this->filterdata["sort_by_id"]) {
            $query->orderBy('id', $this->filterdata['sort_by_id']);
        }
        if ($this->filterdata["sort_by_name"]) {
            $query->orderBy('name', $this->filterdata['sort_by_name']);
        }
        if ($this->filterdata["sort_by_email"]) {
            $query->orderBy('email', $this->filterdata['sort_by_email']);
        }
        if ($this->filterdata["sort_by_mobile"]) {

            $query->orderBy('mobile', $this->filterdata['sort_by_mobile']);
        }
        if ($this->filterdata["sort_by_activation"]) {
            $query->orderBy('activation', $this->filterdata['sort_by_activation']);
        }
        $query->where('type_id', UserType::SELLER);

        return $query->get();
     }

    /**
     * @return array
     * @var Size $size
     */
    public function map($brand): array
    {

        return [
            $brand->id,
            $brand->name,
            $brand->email,
            $brand->mobile,
            $brand->created_at,
            $brand->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name'),
            trans('form_fields.general.email'),
            trans('form_fields.general.mobile'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
