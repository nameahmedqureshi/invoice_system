@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">Add New Client</h1>
        <a href="{{ route('admin.users.index') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back to
            List</a>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                @error('name') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                @error('email') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="text-right">
                <button type="submit" class="btn">Create Client</button>
            </div>

            <p style="margin-top: 1rem; font-size: 0.875rem; color: var(--text-muted);">
                An email will be sent to the client with their login credentials.
            </p>
        </form>
    </div>
@endsection