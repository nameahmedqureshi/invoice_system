@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">Invoice #{{ $invoice->id }}</h1>
        <div>
            <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" class="btn"
                style="background: var(--text-muted); margin-right: 0.5rem;">Download PDF</a>
            <a href="{{ route('admin.invoices.index') }}" style="color: var(--text-muted); text-decoration: none;">&larr;
                Back to List</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div class="card">
            <h2 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">Invoice Details</h2>
            <div class="mt-4">
                <p><strong>Client:</strong> {{ $invoice->client->name }} ({{ $invoice->client->email }})</p>
                <p><strong>Amount:</strong> <span style="font-size: 1.25rem; font-weight: 700;">{{ $invoice->currency }}
                        {{ number_format($invoice->amount, 2) }}</span></p>
                <p><strong>Status:</strong>
                    @if($invoice->status == 'paid')
                        <span class="badge badge-paid">Paid</span>
                    @elseif($invoice->is_expired)
                        <span class="badge" style="background: #fef2f2; color: #b91c1c;">Expired</span>
                    @else
                        <span class="badge badge-pending">Pending</span>
                    @endif
                </p>
                <p><strong>Due Date:</strong>
                    {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : '-' }}</p>
                <div style="margin-top: 2rem;">
                    <strong>Description:</strong>
                    <p style="color: var(--text-muted); background: #f9fafb; padding: 1rem; border-radius: 8px;">
                        {{ $invoice->description }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">Payment History</h2>
            @if($invoice->payments->isEmpty())
                <p style="color: var(--text-muted); margin-top: 1rem;">No payments recorded.</p>
            @else
                <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                    @foreach($invoice->payments as $payment)
                        <li style="padding: 1rem; border-bottom: 1px solid var(--border);">
                            <div style="font-weight: 600;">{{ ucfirst($payment->gateway) }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $payment->transaction_id }}</div>
                            <div style="margin-top: 0.25rem;">
                                <span class="badge badge-paid">{{ ucfirst($payment->status) }}</span>
                                <span style="float: right;">{{ $invoice->currency }} {{ number_format($payment->amount, 2) }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection