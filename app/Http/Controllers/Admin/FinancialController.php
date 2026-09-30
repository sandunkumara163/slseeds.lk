<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinancialController extends Controller
{
    private function applyFilters($query, $request)
    {
        $filter = $request->get('filter', 'all');
        $method = $request->get('method', 'all');

        if ($filter == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($filter == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filter == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        }

        if ($method != 'all') {
            $query->where('payment_method', $method);
        }

        return [$query, $filter, $method];
    }

    public function index(Request $request)
    {
        $query = Order::query();
        list($query, $filter, $method) = $this->applyFilters($query, $request);

        $orders = $query->latest()->get();

        $codIncome = clone $query;
        $codIncome = $codIncome->where('payment_method', 'cod')
                            ->whereIn('status', ['delivered', 'completed'])
                            ->sum('total_amount');
                            
        $onlineIncome = clone $query;
        $onlineIncome = $onlineIncome->where('payment_method', 'payhere')
                               ->whereIn('status', ['processing', 'shipped', 'delivered', 'completed'])
                               ->sum('total_amount');

        $refundedOnline = clone $query;
        $refundedOnline = $refundedOnline->where('payment_method', 'payhere')
                                 ->where('status', 'refunded')
                                 ->sum('total_amount');
                                 
        $returnedCod = clone $query;
        $returnedCod = $returnedCod->where('payment_method', 'cod')
                              ->whereIn('status', ['cancelled', 'refunded'])
                              ->sum('total_amount');

        $totalIncome = $codIncome + $onlineIncome;

        return view('admin.financial.index', compact(
            'orders', 'filter', 'method', 'codIncome', 'onlineIncome', 'refundedOnline', 'returnedCod', 'totalIncome'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = Order::query();
        list($query, $filter, $method) = $this->applyFilters($query, $request);

        $orders = $query->latest()->get();
        
        $codIncome = clone $query;
        $codIncome = $codIncome->where('payment_method', 'cod')->whereIn('status', ['delivered', 'completed'])->sum('total_amount');
        
        $onlineIncome = clone $query;
        $onlineIncome = $onlineIncome->where('payment_method', 'payhere')->whereIn('status', ['processing', 'shipped', 'delivered', 'completed'])->sum('total_amount');
        
        $totalIncome = $codIncome + $onlineIncome;
        
        $pdf = Pdf::loadView('admin.financial.pdf', compact('orders', 'filter', 'method', 'codIncome', 'onlineIncome', 'totalIncome'));
        return $pdf->download('financial_report_' . $filter . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Order::query();
        list($query, $filter, $method) = $this->applyFilters($query, $request);

        $orders = $query->latest()->get();

        $filename = "financial_report_{$filter}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Order ID', 'Date', 'Customer', 'Payment Method', 'Status', 'Total Amount'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $row = [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->user->name ?? 'N/A',
                    strtoupper($order->payment_method),
                    strtoupper($order->status),
                    $order->total_amount
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
