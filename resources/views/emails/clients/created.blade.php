<x-mail::message>
    # Welcome to InvoiceSystem

    Hello {{ $user->name }},

    Your account has been created by the administrator. You can now login to view and pay your invoices.

    **Login Details:**
    - **Email:** {{ $user->email }}
    - **Password:** {{ $password }}

    <x-mail::button :url="route('login')">
        Login to Dashboard
    </x-mail::button>

    Please change your password after logging in.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>