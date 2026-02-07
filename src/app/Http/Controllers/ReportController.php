<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        //siapkan data wal dan akhir
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d')); 
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        //tarik data transaksi
        $transactions = Transaction::whereBetween('created_at', [$startDate . ' 00:00:00' , $endDate . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->with('details')
            ->get();

        //siapkan tottal pendapatan / revenue dan banyak transaksi
        $totalRevenue = $transactions->sum('total_price');
        $totalTransactions = $transactions->count();
        $totalItems = $transactions->flatMap->details->sum('quantity');

        return view('reports.index', [
            'transactions' => $transactions,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'totalItems' => $totalItems,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    public function print(Request $request)
    {
        //siapkan data wal dan akhir
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d')); 
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        //tarik data transaksi
        $transactions = Transaction::whereBetween('created_at', [$startDate . ' 00:00:00' , $endDate . ' 23:59:59'])
            ->orderBy('created_at')
            ->with('details')
            ->get();

        $totalRevenue = $transactions->sum('total_price');
        $totalItems = $transactions->flatMap->details->sum('quantity');

        $finalStartDate = Carbon::parse($startDate)->format('l, d F Y');
        $finalEndDate = Carbon::parse($endDate)->format('l, d F Y');

        $pdf = Pdf::loadView('reports.print', [
            'transactions' => $transactions,
            'totalRevenue' => $totalRevenue,
            'totalItems' => $totalItems,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'finalStartDate' => $finalStartDate,
            'finalEndDate' => $finalEndDate,
        ]);
        return $pdf->stream('laporan-penjualan-' . $startDate . '-to-' . $endDate . '.pdf');
        
    }
}
