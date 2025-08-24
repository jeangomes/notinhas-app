<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Services\ProcessDataPurchase;
use DateTime;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function create(): View
    {
        return view('purchase.create');
    }

    public function index(Request $request): JsonResponse
    {
        $date = $request->input('filter');
        $store = $request->input('filter2');
        $nfce = $request->input('filter3');
        $purchases = Purchase::query()->withCount('items')->orderByDesc('purchased_at')
            ->when($date, function ($query, string $date) {
                $query->whereDate('purchased_at', '=', $date);
            })
            ->when($store, function ($query, string $store) {
                $query->whereRaw('LOWER(store) like ?', ['%' . strtolower($store) . '%']);
            })
            ->when($nfce, function ($query, string $nfce) {
                $query->where('nfce_key_access', '=', $nfce);
            })
            ->paginate(20);
        return response()->json($purchases);
    }

    public function show(Purchase $purchase): JsonResponse
    {
        $purchase->load(['items' => function ($query) {
            $query->orderBy('id');
        }]);
        return response()->json(['purchase' => $purchase]);
        //return view('purchase.show', ['purchase' => $purchase]);
    }

    public function destroy(Purchase $purchase): JsonResponse
    {
        $purchase->items()->delete();
        $purchase->delete();
        return response()->json(['purchase' => $purchase]);
        //return view('purchase.show', ['purchase' => $purchase]);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nfce_key_access' => ['required', 'unique:purchases'],
        ]);
        $purchase = new Purchase();
        DB::transaction(function () use ($purchase, $request) {
            $purchase->store = strtoupper($request->input('store'));
            $purchase->purchased_at = $request->input('purchased_at');
            $purchase->paid_at = $request->input('purchased_at');
            $purchase->amount = $request->input('amount');
            $purchase->tax = $request->input('tax');
            $purchase->nfce_key_access = $request->input('nfce_key_access');
            $purchase->save();
            $make = new ProcessDataPurchase();
            $recordsProcessed = $make->handle($request->input('content_write'));

            $purchase->items()->createMany($recordsProcessed);
        });

        if ($purchase->exists) {
            return response()->json([
                'purchased_at' => (new DateTime($purchase->purchased_at))->format('Y-m-d')
            ], 201);
        }
        return response()->json([
            'erro' => 'Não criado',
        ], 400);
    }
}
