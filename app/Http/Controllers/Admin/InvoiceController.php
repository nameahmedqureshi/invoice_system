<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('client')->latest()->paginate(10);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.invoices.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after_or_equal:today',
            'description' => 'required|string|max:255',
        ]);

        Invoice::create($validated);

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['client', 'payments'])->findOrFail($id);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['client', 'payments'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}
