<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Services\ProcessDataPurchase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseItemController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $role = $request->input('filter');
        $purchases = PurchaseItem::query()->selectRaw('purchase_items.*')->with('purchase:id,store,purchased_at,amount')
            ->join('purchases', 'purchase_id','=','purchases.id')
            //product_name
            ->when($role, function ($query, string $role) {
                    $query->where('product_name', 'like', '%'.$role.'%');
                })
            ->orderBy('product_name')
            ->orderByRaw($request->input('order_by', 'purchased_at'))->paginate(50);
        return response()->json($purchases);
        //return view('purchase.index', ['purchases' => $purchases]);
    }
}
