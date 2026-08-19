<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view orders');
    }

    public function a4(Request $request, Order $order): Response|View
    {
        $order->loadMissing(['items', 'vendor', 'user']);

        if ($request->query('format') !== 'pdf') {
            return view('invoices.a4-print', [
                'order' => $order,
                'issuedFor' => 'admin',
                'printOnLoad' => true,
            ]);
        }

        try {
            return Pdf::loadView('invoices.a4', [
                'order' => $order,
                'issuedFor' => 'admin',
            ])->setPaper('a4')
                ->stream($order->invoice_number . '.pdf');
        } catch (Throwable $exception) {
            report($exception);

            return response()->view('invoices.a4-print', [
                'order' => $order,
                'issuedFor' => 'admin',
                'printOnLoad' => false,
                'warning' => 'PDF rendering failed. Showing printable A4 invoice instead.',
            ]);
        }
    }

    public function thermal(Order $order): View
    {
        $order->loadMissing(['items', 'vendor', 'user']);

        return view('invoices.thermal', [
            'order' => $order,
            'issuedFor' => 'admin',
            'printOnLoad' => true,
        ]);
    }
}
