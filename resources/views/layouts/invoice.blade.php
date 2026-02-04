<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .card {
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            padding: 2rem;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            font-weight: 600;
            color: var(--text-muted);
            background: #f1f5f9;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .badge-paid {
            background: #dcfce7;
            color: #166534;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            ring: 2px solid var(--primary);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }

        /* Utility */
        .text-right {
            text-align: right;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .nav-link {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 500;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .logout-btn {
            background: none;
            border: none;
            padding: 0;
            color: var(--text-muted);
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            font-size: 1rem;
        }

        .logout-btn:hover {
            color: #ef4444;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav style="background: white; border-bottom: 1px solid var(--border); padding: 1rem 0;">
        <div class="container"
            style="padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <a href="/"
                style="font-weight: 700; font-size: 1.5rem; text-decoration: none; color: var(--primary);">InvoiceSystem</a>
            <div style="display: flex; gap: 1.5rem; align-items: center;">
                @auth
                    @php
                        $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
                    @endphp

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.invoices.index') }}" class="nav-link">Invoices</a>
                        <a href="{{ route('admin.users.index') }}" class="nav-link" style="margin-left: 1rem;">Clients</a>
                        <a href="{{ route('admin.chat.index') }}" class="nav-link" style="margin-left: 1rem;">
                            Support
                            @if($unreadCount > 0)
                                <span
                                    style="background: #ef4444; color: white; border-radius: 9999px; padding: 0.1rem 0.4rem; font-size: 0.75rem; margin-left: 0.25rem;">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    @else
                        <a href="{{ route('client.invoices.index') }}" class="nav-link">My Invoices</a>
                        <a href="{{ route('client.chat.index') }}" class="nav-link" style="margin-left: 1rem;">
                            Support
                            @if($unreadCount > 0)
                                <span
                                    style="background: #ef4444; color: white; border-radius: 9999px; padding: 0.1rem 0.4rem; font-size: 0.75rem; margin-left: 0.25rem;">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    @endif
                    <span style="color: var(--border);">|</span>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="color: var(--text-main); font-weight: 600;">{{ auth()->user()->name }}</span>

                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-btn">(Logout)</button>
                        </form>
                    </div>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                    <a href="{{ route('register') }}" class="btn"
                        style="padding: 0.5rem 1rem; font-size: 0.875rem;">Register</a>
                @endguest
            </div>
        </div>
    </nav>
    <main class="container">
        @if(session('success'))
            <div
                style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #bbf7d0;">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>

</html>