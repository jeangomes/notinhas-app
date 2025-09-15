<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class PurchaseItemController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $purchaseItems = QueryBuilder::for(PurchaseItem::class)
            ->selectRaw('purchase_items.*')
            ->with('purchase:id,store,purchased_at,amount')
            ->join('purchases', 'purchase_id','=','purchases.id')
            ->allowedFilters([
                'product_name',
                AllowedFilter::partial('store', 'purchases.store', false),
                AllowedFilter::scope('purchased_at'),
            ])

            ->allowedSorts([
                'product_name',
                'unit_price',
                'total_price',
                'quantity',
                AllowedSort::field('purchased_at', 'purchases.purchased_at'),
                AllowedSort::field('store', 'purchases.store'),
                AllowedSort::field('amount', 'purchases.amount'),
            ])
            ->defaultSort('-purchased_at')
            ->paginate(50)
            ->appends($request->query());

        return response()->json($purchaseItems);
    }
}
