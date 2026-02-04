<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }

        .meta {
            text-align: right;
        }

        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        table td {
            padding: 5px;
            vertical-align: top;
        }

        table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        table tr.item td {
            border-bottom: 1px solid #eee;
        }

        table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .status {
            padding: 5px 10px;
            color: white;
            border-radius: 5px;
            font-size: 12px;
        }

        .paid {
            background-color: #10b981;
        }

        .pending {
            background-color: #f59e0b;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <span class="logo">InvoiceSystem</span>
                            </td>
                            <td style="text-align: right;">
                                Invoice #: {{ $invoice->id }}<br>
                                Created: {{ $invoice->created_at->format('M d, Y') }}<br>
                                Due:
                                {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Bill To:</strong><br>
                                {{ $invoice->client->name }}<br>
                                {{ $invoice->client->email }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Description</td>
                <td style="text-align: right;">Amount</td>
            </tr>
            <tr class="item">
                <td>{{ $invoice->description }}</td>
                <td style="text-align: right;">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td style="text-align: right;">
                    Total: {{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}
                </td>
            </tr>
        </table>

        <br>

        <div style="text-align: center;">
            <strong>Status:</strong>
            <span class="status {{ $invoice->status == 'paid' ? 'paid' : 'pending' }}">
                {{ ucfirst($invoice->status) }}
            </span>
        </div>
    </div>
</body>

</html>