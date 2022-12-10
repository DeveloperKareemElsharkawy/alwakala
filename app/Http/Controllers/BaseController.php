<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * @param $data
     * @return JsonResponse
     */
    public function success($data): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $data['message'] ?? trans('messages.general.listed'),
            'data' => $data['data'] ?? '',
        ], Response::HTTP_OK);
    }

    public function accepted($data): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $data['message'],
            'data' => $data['data'],
        ], Response::HTTP_ACCEPTED);
    }

    public function created($data): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $data['message'],
            'data' => $data['data'] ?? '',
        ], Response::HTTP_CREATED);
    }

    public function unAuthorize($data): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $data['message'],
            'data' => $data['data'],
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function notFound(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => trans('messages.general.not_found'),
            'data' => '',
        ], Response::HTTP_NOT_FOUND);
    }

    public function forbidden($data): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $data['message'],
            'data' => $data['data'],
        ], Response::HTTP_FORBIDDEN);
    }

    public function error($data): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $data['message'],
            'data' => $data['data'] ?? [],
        ], Response::HTTP_BAD_REQUEST);
    }

    public function connectionError($e): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage() . ' ' . $e->getMessage() . $e->getFile() . ' ' . $e->getLine(),
//            'message' => trans('messages.errors.500') ,
            'data' => '',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Respond.
     *
     * @param array $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public function respond(array $data, array $headers = []): JsonResponse
    {
        return response()->json($data, 200, $headers);
    }


    /**
     * respond with pagination.
     *
     * @param $items
     * @return JsonResponse
     */
    public function respondPaginationWithAdditionalData($items, $data): JsonResponse
    {
        $data = array_merge(
            [
                'status' => true,
                'code' => 200,
                'message' => '',
                'data' => $data,
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'hasMorePages' => $items->hasMorePages(),
                ]
            ]);

        return $this->respond($data);
    }


    /**
     * respond with pagination.
     *
     * @param $items
     * @return JsonResponse
     */
    public function respondWithPagination($items): JsonResponse
    {
        $data = array_merge(
            [
                'status' => true,
                'code' => 200,
                'message' => '',
                'data' => $items->items(),
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'hasMorePages' => $items->hasMorePages(),
                ]
            ]);

        return $this->respond($data);
    }

}
