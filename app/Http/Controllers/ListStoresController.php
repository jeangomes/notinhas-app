<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListStoresController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $stores = Purchase::query()->select('store')
            ->distinct()
            ->orderBy('store')
            ->pluck('store');

        $result = [];

        foreach ($stores as $index => $store) {
            $result[] = [
                'id' => $index + 1,
                'store' => $store
            ];
        }

        return response()->json($result);
    }
}
