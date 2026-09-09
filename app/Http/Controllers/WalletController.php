<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(Request $request): View
    {
        return view('wallet.index', ['walletBalance' => $request->user()->wallet_balance]);
    }

    public function topUp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $user->increment('wallet_balance', $validated['amount']);
        });

        return back()->with('success', 'Your e-wallet has been topped up successfully.');
    }
}
