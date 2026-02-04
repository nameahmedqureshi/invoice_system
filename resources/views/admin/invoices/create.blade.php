@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">Create Invoice</h1>
        <a href="{{ route('admin.invoices.index') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back
            to List</a>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form action="{{ route('admin.invoices.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Client</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Select a client...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" step="0.01" class="form-control" placeholder="0.00" required>
                @error('amount') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" min="{{ date('Y-m-d') }}" required>
                @error('due_date') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Invoice details..."></textarea>
                @error('description') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div class="text-right">
                <button type="submit" class="btn">Create Invoice</button>
            </div>
        </form>
    </div>
@endsection