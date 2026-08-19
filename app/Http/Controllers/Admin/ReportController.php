<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Vendor;
use App\Services\ReportExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view reports')->only('index');
        $this->middleware('can:export reports')->only('export');
    }

    public function index(Request $request, ReportService $reportService): View
    {
        $type = $reportService->resolveType($request->query('type'));
        $filters = $reportService->normalizeFilters($request->query());
        $vendorFilter = $filters['vendor_id'] ?? null;

        $query = $reportService->query($type, $filters, null, $vendorFilter);
        $records = (clone $query)->paginate(20)->withQueryString();

        $headers = $reportService->headersFor($type);
        $rows = $reportService->mapRows($type, $records->getCollection());
        $summary = $reportService->summary($type, $filters, null, $vendorFilter);
        $vendors = Vendor::approved()->orderBy('shop_name')->get(['id', 'shop_name']);

        return view('admin.reports.index', [
            'panel' => 'admin',
            'panelTitle' => 'Admin Reports',
            'type' => $type,
            'filters' => $filters,
            'headers' => $headers,
            'rows' => $rows,
            'records' => $records,
            'summary' => $summary,
            'title' => $reportService->titleFor($type),
            'vendors' => $vendors,
            'indexRoute' => route('admin.reports.index'),
            'exportRoute' => route('admin.reports.export'),
        ]);
    }

    public function export(
        Request $request,
        ReportService $reportService,
        ReportExportService $reportExportService
    ): StreamedResponse|\Illuminate\Http\Response {
        $type = $reportService->resolveType($request->query('type'));
        $filters = $reportService->normalizeFilters($request->query());
        $vendorFilter = $filters['vendor_id'] ?? null;
        $format = strtolower(trim((string) $request->query('format', 'csv')));

        $query = $reportService->query($type, $filters, null, $vendorFilter);
        $headers = $reportService->headersFor($type);
        $rows = $reportService->mapRows($type, (clone $query)->get());
        $summary = $reportService->summary($type, $filters, null, $vendorFilter);

        $baseFileName = 'admin-' . $type . '-report-' . now()->format('Ymd-His');

        if ($format === 'pdf') {
            return $reportExportService->downloadPdf(
                $baseFileName . '.pdf',
                'Admin ' . $reportService->titleFor($type),
                $headers,
                $rows,
                $summary,
                $filters,
            );
        }

        return $reportExportService->downloadCsv($baseFileName . '.csv', $headers, $rows);
    }
}
