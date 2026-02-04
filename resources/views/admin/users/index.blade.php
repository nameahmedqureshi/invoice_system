@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">Clients</h1>
        <a href="{{ route('admin.users.create') }}" class="btn">Add New Client</a>
    </div>

    @if(session('success'))
        <div
            style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #bbf7d0;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div
            style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #fecaca;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="table" style="margin-top: 0;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-paid">Active</span>
                            @else
                                <span class="badge" style="background: #fee2e2; color: #b91c1c;">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                style="color: var(--primary); font-weight: 500; text-decoration: none; margin-right: 1rem;">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;"
                                onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    style="background: none; border: none; padding: 0; color: #ef4444; font-weight: 500; cursor: pointer; font-size: inherit; font-family: inherit;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($users->isEmpty())
            <div style="padding: 2rem; text-align: center; color: var(--text-muted);">No clients found.</div>
        @endif
    </div>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection