<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\ReportExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view reports');
    }

    public function index(Request $request, ReportService $reportService): View
    {
        $vendorId = (int) (auth()->user()?->vendor?->id ?? 0);
        abort_if($vendorId <= 0, 403, 'Vendor account not found.');

        $type = $reportService->resolveType($request->query('type'));
        $filters = $reportService->normalizeFilters($request->query());

        $query = $reportService->query($type, $filters, $vendorId);
        $records = (clone $query)->paginate(20)->withQueryString();

        return view('admin.reports.index', [
            'panel' => 'vendor',
            'panelTitle' => 'Vendor Reports',
            'type' => $type,
            'filters' => $filters,
            'headers' => $reportService->headersFor($type),
            'rows' => $reportService->mapRows($type, $records->getCollection()),
            'records' => $records,
            'summary' => $reportService->summary($type, $filters, $vendorId),
            'title' => $reportService->titleFor($type),
            'vendors' => collect(),
            'indexRoute' => route('vendor.reports.index'),
            'exportRoute' => route('vendor.reports.export'),
        ]);
    }

    public function export(
        Request $request,
        ReportService $reportService,
        ReportExportService $reportExportService
    ): StreamedResponse|\Illuminate\Http\Response {
        $vendorId = (int) (auth()->user()?->vendor?->id ?? 0);
        abort_if($vendorId <= 0, 403, 'Vendor account not found.');

        $type = $reportService->resolveType($request->query('type'));
        $filters = $reportService->normalizeFilters($request->query());
        $format = strtolower(trim((string) $request->query('format', 'csv')));

        $query = $reportService->query($type, $filters, $vendorId);
        $headers = $reportService->headersFor($type);
        $rows = $reportService->mapRows($type, (clone $query)->get());
        $summary = $reportService->summary($type, $filters, $vendorId);

        $baseFileName = 'vendor-' . $type . '-report-' . now()->format('Ymd-His');

        if ($format === 'pdf') {
            return $reportExportService->downloadPdf(
                $baseFileName . '.pdf',
                'Vendor ' . $reportService->titleFor($type),
                $headers,
                $rows,
                $summary,
                $filters,
            );
        }

        return $reportExportService->downloadCsv($baseFileName . '.csv', $headers, $rows);
    }
}
