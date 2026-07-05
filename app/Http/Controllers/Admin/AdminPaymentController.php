<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventPaymentStatus;
use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\EventPayment;
use App\Models\ServiceFeeConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminPaymentController extends Controller
{
    /**
     * Display a listing of event payments and service fee configs.
     */
    public function index(): Response
    {
        $payments = EventPayment::with(['event.organizer'])->get();
        $configs = ServiceFeeConfig::all();

        return Inertia::render('admin/payments/Index', [
            'payments' => $payments,
            'configs' => $configs,
        ]);
    }

    /**
     * Verify/approve or reject a deposit payment.
     */
    public function verify(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approved,rejected',
        ]);

        $payment = EventPayment::find($id);

        if (! $payment) {
            abort(404);
        }

        if ($validated['action'] === 'approved') {
            $payment->update([
                'status' => EventPaymentStatus::Approved,
                'verified_by_id' => auth()->id(),
                'verified_at' => now(),
            ]);

            if ($payment->event) {
                $payment->event->update(['status' => EventStatus::Registration]);
            }

            return redirect()->back()->with('success', 'Payment approved successfully. Tournament is now open for registration.');
        } else {
            $payment->update([
                'status' => EventPaymentStatus::Rejected,
                'verified_by_id' => auth()->id(),
                'verified_at' => now(),
            ]);

            if ($payment->event) {
                $payment->event->update(['status' => EventStatus::Draft]);
            }

            return redirect()->back()->with('success', 'Payment rejected. Tournament status set back to Draft.');
        }
    }

    /**
     * Store or update service fee configs.
     */
    public function updateFeeConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'configs' => 'required|array',
            'configs.*.min_reward' => 'required|integer|min:0',
            'configs.*.max_reward' => 'required|integer|min:0',
            'configs.*.service_fee' => 'required|integer|min:0',
        ]);

        ServiceFeeConfig::truncate();

        foreach ($validated['configs'] as $cfg) {
            ServiceFeeConfig::create($cfg);
        }

        return redirect()->back()->with('success', 'Service fee configurations updated successfully.');
    }
}
