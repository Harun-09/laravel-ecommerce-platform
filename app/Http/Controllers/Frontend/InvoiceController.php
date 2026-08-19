<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function a4(Request $request, string $orderNumber): Response|View
    {
        $order = auth()->user()->orders()
            ->where('order_number', $orderNumber)
            ->with(['items', 'vendor', 'user'])
            ->firstOrFail();

        if ($request->query('format') !== 'pdf') {
            return view('invoices.a4-print', [
                'order' => $order,
                'issuedFor' => 'customer',
                'printOnLoad' => true,
            ]);
        }

        try {
            return Pdf::loadView('invoices.a4', [
                'order' => $order,
                'issuedFor' => 'customer',
            ])->setPaper('a4')
                ->stream($order->invoice_number . '.pdf');
        } catch (Throwable $exception) {
            report($exception);

            return response()->view('invoices.a4-print', [
                'order' => $order,
                'issuedFor' => 'customer',
                'printOnLoad' => false,
                'warning' => 'PDF rendering failed. Showing printable A4 invoice instead.',
            ]);
        }
    }

    public function thermal(string $orderNumber): View
    {
        $order = auth()->user()->orders()
            ->where('order_number', $orderNumber)
            ->with(['items', 'vendor', 'user'])
            ->firstOrFail();

        return view('invoices.thermal', [
            'order' => $order,
            'issuedFor' => 'customer',
            'printOnLoad' => true,
        ]);
    }
}
