<?php

namespace App\Exports\Dashboard;

use App\Enums\UserTypes\UserType;
use App\Lib\Helpers\Lang\LangHelper;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdminsExport implements FromCollection,WithMapping,WithHeadings,ShouldAutoSize
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
            ->with('admin')
            ->select('id', 'name', 'email', 'mobile', 'activation')
            ->orderBy('updated_at', 'desc');


        if ($this->filterdata["id"]) {
            $query->where('id', intval($this->filterdata['id']));
        }
        if ($this->filterdata["name"]) {
            $name = "%" . $this->filterdata['name'] . "%";
            $query->where('name', "ilike", $name);
        }
        if ($this->filterdata["email"]) {
            $email = "%" . $this->filterdata['email'] . "%";
            $query->where('email', "ilike", $email);
        }
        if ($this->filterdata["mobile"]) {
            $mobile = "%" . $this->filterdata['mobile'] . "%";
            $query->where('mobile', "ilike", $mobile);
        }
        if ($this->filterdata["activation"]) {
            $query->where('activation', $this->filterdata['activation']);
        }
        if ($this->filterdata["sort_by_id"]) {
            $query->orderBy('id', intval($this->filterdata['sort_by_id']));
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
        $query->where('type_id', UserType::ADMIN);

        return $query->get();
    }

    /**
     * @return array
     * @var Admin $admin
     */
    public function map($admin): array
    {

        return [
            $admin->id,
            $admin->name,
            $admin->email,
            $admin->mobile,
            $admin->activation,
            $admin->created_at,
            $admin->updated_at,
        ];
    }


    public function headings(): array
    {
        return [
            '#',
            trans('form_fields.general.name'),
            trans('form_fields.general.email'),
            trans('form_fields.general.mobile'),
            trans('form_fields.general.status'),
            trans('form_fields.general.created_at'),
            trans('form_fields.general.updated_at'),
        ];
    }

}
