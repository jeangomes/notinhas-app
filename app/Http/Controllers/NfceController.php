<?php

namespace App\Http\Controllers;

use App\Models\Nfce;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NfceController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        return response()->json(Nfce::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => ['required', 'unique:nfces'],
        ]);
        DB::transaction(function () use ($request) {
            $purchase = new Nfce();
            $purchase->content = $request->input('content');
            $purchase->save();
        });
        //return redirect('/purchase');
        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA',
        ], 201);
    }

    public function destroy(Nfce $nfce_key_or_url): void
    {
        $nfce_key_or_url->delete();
    }
}
