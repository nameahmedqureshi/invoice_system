@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">My Invoices</h1>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="table" style="margin-top: 0;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr>
                        <td>#{{ $invoice->id }}</td>
                        <td>{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</td>
                        <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : '-' }}</td>
                        <td>
                            @if($invoice->status == 'paid')
                                <span class="badge badge-paid">Paid</span>
                            @elseif($invoice->is_expired)
                                <span class="badge" style="background: #fef2f2; color: #b91c1c;">Expired</span>
                            @else
                                <span class="badge badge-pending">Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('client.invoices.show', $invoice->id) }}"
                                style="color: var(--primary); font-weight: 500; text-decoration: none;">
                                View & Pay
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($invoices->isEmpty())
            <div style="padding: 2rem; text-align: center; color: var(--text-muted);">You have no invoices.</div>
        @endif
    </div>
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
@endsection