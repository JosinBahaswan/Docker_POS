<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        //siapkan data wal dan akhir
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d')); 
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        //tarik data transaksi
        $transactions = Transaction::whereBetween('created_at', [$startDate . ' 00:00:00 ' , $endDate . ' 23:59:59 '])->newers()->get();

        //siapkan tottal pendapatan / revenue dan banyak transaksi
        $totalRevenue = $transactions->sum('total_price');
        $totalTransactions = $transactions->count();

        return view('index', [
            'transactions' => $transactions,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    public function print()
    {
        //siapkan data wal dan akhir
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d')); 
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        //tarik data transaksi
        $transactions = Transaction::whereBetween('created_at', [$startDate . ' 00:00:00 ' , $endDate . ' 23:59:59 '])->oldest()->get();

        $totalRevenue = $transactions->sum('total_price');

        $finalStartDate = Carbon::parse($startDate)->format('l, d F Y');
        $finalEndDate = Carbon::parse($endDate)->format('l, d F Y');

        $pdf = PDF::loadView('reports.print', [
            'transactions' => $transactions,
            'totalRevenue' => $totalRevenue,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        return $pdf->download('Laporan penjualan' . $startDate . '_to_' . $endDate . '.pdf');
        
    }
}
