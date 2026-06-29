<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\QuotationResource;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Enums\InvoiceStatus;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;

/**
 * Class FinanceController
 * 
 * Handles finance API endpoints (invoices and quotations) for the mobile app.
 * 
 * @package App\Http\Controllers\Api
 */
class FinanceController extends Controller
{
    use ApiResponder;

    /**
     * Display a listing of invoices with optional status, month, and year filters.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function invoices(Request $request)
    {
        $query = Invoice::with('client');

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('month') && $request->input('month') !== '') {
            $query->whereMonth('issue_date', $request->input('month'));
        }

        if ($request->has('year') && $request->input('year') !== '') {
            $query->whereYear('issue_date', $request->input('year'));
        }

        $perPage = $request->input('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->successResponse(
            InvoiceResource::collection($paginator->items()),
            'Daftar invoice berhasil diambil.',
            200,
            [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        );
    }

    /**
     * Display the specified invoice with items.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function invoiceDetails($id)
    {
        $invoice = Invoice::with(['client', 'creator', 'items.inventory'])->findOrFail($id);

        return $this->successResponse(new InvoiceResource($invoice), 'Detail invoice berhasil diambil.');
    }

    /**
     * Display statistics of invoices for the current month.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function invoiceStatistics()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Query all invoices this month except cancelled
        $invoices = Invoice::whereBetween('issue_date', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', InvoiceStatus::Cancelled)
            ->get();

        $totalBilling = $invoices->sum('total');
        $totalPaid = $invoices->where('status', InvoiceStatus::Paid)->sum('total');
        $totalOutstanding = $invoices->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::Overdue, InvoiceStatus::Draft])->sum('total');

        return $this->successResponse([
            'billing_month' => now()->format('F Y'),
            'total_billing' => (float) $totalBilling,
            'total_paid' => (float) $totalPaid,
            'total_outstanding' => (float) $totalOutstanding,
            'count_total' => $invoices->count(),
            'count_paid' => $invoices->where('status', InvoiceStatus::Paid)->count(),
            'count_outstanding' => $invoices->whereIn('status', [InvoiceStatus::Sent, InvoiceStatus::Overdue, InvoiceStatus::Draft])->count(),
        ], 'Statistik invoice bulan ini berhasil diambil.');
    }

    /**
     * Display a listing of quotations.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quotations(Request $request)
    {
        $query = Quotation::with('client');

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        $perPage = $request->input('per_page', 15);
        $paginator = $query->paginate($perPage);

        return $this->successResponse(
            QuotationResource::collection($paginator->items()),
            'Daftar quotation berhasil diambil.',
            200,
            [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        );
    }

    /**
     * Display the specified quotation with items.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function quotationDetails($id)
    {
        $quotation = Quotation::with(['client', 'creator', 'items.inventory'])->findOrFail($id);

        return $this->successResponse(new QuotationResource($quotation), 'Detail quotation berhasil diambil.');
    }
}
