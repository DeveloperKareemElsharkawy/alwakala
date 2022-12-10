<?php


namespace App\Repositories;

use App\Http\Controllers\Controller;
use App\Models\SystemSetup;

class SystemSetupRepository extends Controller
{
    public static function save($data)
    {
        $system = new SystemSetup();
        $system->key = $data['title'];
        $system->value = $data['value'];
        $system->save();
    }

    public static function update($data)
    {
        $system = SystemSetup::query()->findOrFail($data['id']);
        $system->value = $data['value'];
        $system->save();
    }

    public static function delete($id)
    {
        SystemSetup::query()->delete($id);
    }

    public static function view()
    {
        return SystemSetup::query()->select('id', 'key', 'value', 'created_at')->paginate(10);
    }
}
