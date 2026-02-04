@extends('layouts.invoice')

@section('content')
    <div class="header">
        <h1 class="title">Support Chat</h1>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="table" style="margin-top: 0;">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Unread</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr style="{{ $user->unread_count > 0 ? 'background: #eff6ff;' : '' }}">
                        <td style="font-weight: 500;">
                            {{ $user->name }}
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->unread_count > 0)
                                <span class="badge" style="background: #ef4444; color: white;">{{ $user->unread_count }} New</span>
                            @else
                                <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.chat.show', $user->id) }}" class="btn"
                                style="padding: 0.5rem 1rem; font-size: 0.875rem;">Open Chat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($users->isEmpty())
            <div style="padding: 2rem; text-align: center; color: var(--text-muted);">No clients found.</div>
        @endif
    </div>
@endsection