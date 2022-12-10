<?php

namespace App\Services\Badges;

use App\Lib\Services\ImageUploader\UploadImage;
use App\Repositories\BadgesRepository;

class BadgesService
{
    private $badgesRepository;

    public function __construct(BadgesRepository $badgesRepository)
    {
        $this->badgesRepository = $badgesRepository;
    }

    public function createBadge($data)
    {
        $data['icon'] = UploadImage::uploadImageToStorage($data['icon'], 'badges');

        return $this->badgesRepository->create($data);
    }

    public function showBadge($id)
    {
        $fields = ['id', 'name_ar', 'name_en', 'color_id', 'icon','is_product','is_seller','is_store','activation'];
        $badge = $this->badgesRepository->showById($id, $fields);
        if ($badge) {
            $badge->icon = $badge->icon ? config('filesystems.aws_base_url') . $badge->icon : null;
        }
        return $badge;
    }

    public function editBadge($data): int
    {
        if (array_key_exists('icon', $data)) {
            $data['icon'] = UploadImage::uploadImageToStorage($data['icon'], 'badges');
        }

        return $this->badgesRepository->update($data);
    }

    public function deleteBadge($data)
    {
        $badge = $this->badgesRepository->showById($data['id'], ['*']);

        $badge->load(['products', 'stores']);

        if (count($badge->products) || count($badge->stores)) {
            return ['status' => false, 'message' => 'Cant Delete Badge'];
        }

        return $this->badgesRepository->destroy($data);
    }

    public function getAllForSelection($request)
    {
        $badges = $this->badgesRepository->getBadgesForSelection( $request);
        return $badges;
    }
}
