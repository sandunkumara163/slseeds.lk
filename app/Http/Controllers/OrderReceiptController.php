<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderReceiptController extends Controller
{
    public function download(Order $order)
    {
        // Check authorization: only the customer who owns the order OR an admin can download
        if (auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['user', 'items.product.translations']);

        $pdf = Pdf::loadView('pdf.receipt', compact('order'));
        
        return $pdf->download("SLSeeds-Receipt-{$order->id}.pdf");
    }
}
