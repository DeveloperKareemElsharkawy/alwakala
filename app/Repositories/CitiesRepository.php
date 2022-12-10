<?php


namespace App\Repositories;


use App\Models\City;

class CitiesRepository
{
    public function getCitiesForSelection($lang, $stateId = null)
    {
        $query = City::query()
            ->select('id', 'name_' . $lang . ' as name');
        if ($stateId)
            $query->where('state_id', $stateId);
        $query->where('activation', true)
            ->orderBy('name_' . $lang);
        return $query->get();
    }
}
