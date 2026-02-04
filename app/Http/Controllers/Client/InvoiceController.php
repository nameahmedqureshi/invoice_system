<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('user_id', auth()->id())->latest()->paginate(10);
        return view('client.invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);

        $clientSecret = null;
        $stripeKey = config('services.stripe.key');

        // Check if invoice is expired (Due date passed and not today)
        $isExpired = $invoice->is_expired;

        if ($invoice->status === 'pending' && !$isExpired) {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create or retrieve PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($invoice->amount * 100), // cents
                'currency' => strtolower($invoice->currency),
                'metadata' => ['invoice_id' => $invoice->id],
            ]);

            $clientSecret = $paymentIntent->client_secret;
        }

        return view('client.invoices.show', compact('invoice', 'clientSecret', 'stripeKey', 'isExpired'));
    }

    public function paymentSuccess(Request $request, $id)
    {
        // This is the return_url for Stripe
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);

        // In production, rely on Webhooks. For this demo, we can check PI status here or just show success.
        // We will optimistically show success message. Actual status update should be via Webhook.
        // But let's retrieve the PI to confirm.

        Stripe::setApiKey(config('services.stripe.secret'));
        if ($request->payment_intent) {
            $pi = PaymentIntent::retrieve($request->payment_intent);
            if ($pi->status === 'succeeded') {
                $invoice->update(['status' => 'paid']); // Optimistic update
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'gateway' => 'stripe',
                    'transaction_id' => $pi->id,
                    'amount' => $invoice->amount,
                    'status' => 'succeeded',
                    'payload' => $pi->toArray()
                ]);
            }
        } elseif ($request->paypal_order_id) {
            // Validate with PayPal API if needed in production
            $invoice->update(['status' => 'paid']);
            Payment::create([
                'invoice_id' => $invoice->id,
                'gateway' => 'paypal',
                'transaction_id' => $request->paypal_order_id,
                'amount' => $invoice->amount,
                'status' => 'succeeded',
                'payload' => ['order_id' => $request->paypal_order_id]
            ]);
        }

        return redirect()->route('client.invoices.show', $id)->with('success', 'Payment successful!');
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);
        if ($invoice->status !== 'paid') {
            abort(403, 'You can only download paid invoices.');
        }
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}
