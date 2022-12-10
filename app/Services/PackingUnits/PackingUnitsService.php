<?php

namespace App\Services\PackingUnits;

use App\Repositories\PackingUnitsRepository;

class PackingUnitsService
{
    private $packingUnitsRepository;

    public function __construct(PackingUnitsRepository $packingUnitsRepository)
    {
        $this->packingUnitsRepository = $packingUnitsRepository;
    }

    public function createPackingUnit($data)
    {
        return $this->packingUnitsRepository->create($data);
    }

    public function showPackingUnit($id)
    {
        $fields = ['id', 'name_ar', 'name_en'];
        $object = $this->packingUnitsRepository->showById($id, $fields);
        return $object;
    }

    public function editPackingUnit($data): int
    {
        return $this->packingUnitsRepository->update($data);
    }

    public function deletePackingUnit($data)
    {
        $object = $this->packingUnitsRepository->showById($data['id'], ['*']);
        return $this->packingUnitsRepository->destroy($data);
    }

    public function getAllForSelection($request)
    {
        $objects = $this->packingUnitsRepository->getPackingUnitsForSelection( $request);
        return $objects;
    }
}
