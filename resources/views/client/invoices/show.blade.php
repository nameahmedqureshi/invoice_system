@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">Invoice #{{ $invoice->id }}</h1>
        <div>
            @if($invoice->status === 'paid')
                <a href="{{ route('client.invoices.pdf', $invoice->id) }}" class="btn" style="background: var(--text-muted); margin-right: 0.5rem;">Download PDF</a>
            @endif
            <a href="{{ route('client.invoices.index') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back to List</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Invoice Details -->
        <div class="card">
            <h2 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">Detials</h2>
            <div style="margin-top: 1rem;">
                <p><strong>Amount:</strong> <span style="font-size: 1.5rem; font-weight: 700;">{{ $invoice->currency }}
                        {{ number_format($invoice->amount, 2) }}</span></p>
                <p><strong>Status:</strong>
                    <span class="badge {{ $invoice->status == 'paid' ? 'badge-paid' : 'badge-pending' }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </p>
                <p><strong>Due Date:</strong>
                    {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : '-' }}</p>
                <div style="margin-top: 2rem;">
                    <strong>Description:</strong>
                    <p style="color: var(--text-muted);">{{ $invoice->description }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="card">
            <h2 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">Payment</h2>

            @if($invoice->status === 'paid')
                <div style="text-align: center; padding: 2rem; color: var(--success);">
                    <svg style="width: 64px; height: 64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h3>This invoice is paid.</h3>
                </div>
            @elseif(isset($isExpired) && $isExpired)
                <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                    <svg style="width: 64px; height: 64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 style="color: #ef4444;">Inverse Expired</h3>
                    <p>The due date for this invoice has passed. Please contact the administrator.</p>
                </div>
            @else
                <!-- Stripe -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Pay with Card</h3>

                    @if($clientSecret)
                        <form id="payment-form">
                            <div id="payment-element">
                                <!-- Stripe Elements will be inserted here -->
                            </div>
                            <button id="submit" class="btn" style="width: 100%; margin-top: 1rem;">
                                <span id="button-text">Pay Now</span>
                                <span id="spinner" style="display: none;">Processing...</span>
                            </button>
                            <div id="payment-message" style="margin-top: 1rem; color: var(--warning); display: none;"></div>
                        </form>
                    @else
                        <p style="color: red;">Configuration Error: Could not initiate payment.</p>
                    @endif
                </div>

                <div style="border-top: 1px solid var(--border); margin: 2rem 0;"></div>

                <!-- PayPal Placeholder -->
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Pay with PayPal</h3>
                    <!-- In a real integration, include PayPal SDK script and container -->
                    <div id="paypal-button-container"></div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <!-- PayPal SDK -->
    @if($invoice->status !== 'paid')
        <script
            src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency={{ $invoice->currency }}"></script>
        <script>
            if (typeof paypal !== 'undefined') {
                paypal.Buttons({
                    createOrder: function (data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '{{ $invoice->amount }}'
                                }
                            }]
                        });
                    },
                    onApprove: function (data, actions) {
                        return actions.order.capture().then(function (details) {
                            window.location.href = "{{ route('client.invoices.payment_success', $invoice->id) }}?paypal_order_id=" + data.orderID;
                        });
                    }
                }).render('#paypal-button-container');
            }
        </script>
    @endif

    @if($invoice->status !== 'paid' && $clientSecret)
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe('{{ $stripeKey }}');
            const elements = stripe.elements({
                clientSecret: '{{ $clientSecret }}',
                appearance: { theme: 'stripe' }
            });

            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');

            const form = document.getElementById('payment-form');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                setLoading(true);

                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: '{{ route("client.invoices.payment_success", $invoice->id) }}',
                    },
                });

                if (error) {
                    const messageContainer = document.querySelector('#payment-message');
                    messageContainer.style.display = 'block';
                    messageContainer.textContent = error.message;
                    setLoading(false);
                } else {
                    // Return URL will handle success
                }
            });

            function setLoading(isLoading) {
                if (isLoading) {
                    document.querySelector("#submit").disabled = true;
                    document.querySelector("#spinner").style.display = "inline";
                    document.querySelector("#button-text").style.display = "none";
                } else {
                    document.querySelector("#submit").disabled = false;
                    document.querySelector("#spinner").style.display = "none";
                    document.querySelector("#button-text").style.display = "inline";
                }
            }
        </script>
    @endif
@endpush