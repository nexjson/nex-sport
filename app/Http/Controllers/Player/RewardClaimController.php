<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\RewardClaimRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RewardClaimController extends Controller
{
    public function __construct(
        protected RewardClaimRepositoryInterface $claimRepository
    ) {}

    /**
     * Display a listing of claims for the player.
     */
    public function index(): Response
    {
        $userId = auth()->id();
        $claims = $this->claimRepository->getByClaimer($userId);

        return Inertia::render('player/claims/Index', [
            'claims' => $claims,
        ]);
    }

    /**
     * Submit bank details and claim payout.
     */
    public function claim(Request $request, int $id): RedirectResponse
    {
        $claim = $this->claimRepository->find($id);

        if (! $claim) {
            abort(404);
        }

        if ($claim->claimed_by_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($claim->status === 'paid') {
            return redirect()->back()->with('error', 'Reward has already been paid.');
        }

        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
        ]);

        $this->claimRepository->update($id, [
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'status' => 'processing',
        ]);

        // Mock Disbursement API payout trigger
        // Negative case: if account number is '99999', simulate bank transfer failure
        if ($validated['account_number'] === '99999') {
            $this->claimRepository->update($id, [
                'status' => 'failed',
            ]);

            return redirect()->route('player.claims.index')->with('error', 'Disbursement failed. Please verify your bank account details.');
        }

        // Positive case: payout success
        $this->claimRepository->update($id, [
            'status' => 'paid',
            'payment_receipt' => 'DISBURSE-TXN-'.time(),
        ]);

        return redirect()->route('player.claims.index')->with('success', 'Prize payout transfer successful!');
    }
}
