@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">Edit Client: {{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back to
            List</a>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                @error('name') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
                @error('email') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>

            <div style="border-top: 1px solid var(--border); margin: 2rem 0; padding-top: 1rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Security</h3>

                <div class="form-group">
                    <label class="form-label">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control">
                    @error('password') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div style="border-top: 1px solid var(--border); margin: 2rem 0; padding-top: 1rem;">
                <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Status</h3>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <span style="font-weight: 500;">Account Active</span>
                </label>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">Inactive users cannot log in.
                </p>
            </div>

            <div class="text-right">
                <button type="submit" class="btn">Update Client</button>
            </div>
        </form>
    </div>
@endsection