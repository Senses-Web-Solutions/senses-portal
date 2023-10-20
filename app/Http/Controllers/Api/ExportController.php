<?php

namespace App\Http\Controllers\Api;

use App\Actions\Exports\ExportInteractions;
use Illuminate\Http\Request;
use App\Actions\Exports\TableExport;
use App\Http\Controllers\Api\Controller;
use App\Actions\Exports\TableExportExcel;
use App\Traits\ApiResponse;

/**
 * @group Export
 *
 * APIs for managing exports
 */
class ExportController extends Controller
{
    use ApiResponse;

    /**
     * tableExport()
     *
     * Exports a table
     */
    //todo add auth
    public function tableExport(Request $request, TableExportExcel $tableExport) {
        $name = $request->input('name', 'export');
        $tableName = $request->input('tableName', 'table');
        $tableExport->execute($request->tableUrl, $name . '_' . now()->format('YmdHis'), 2000, null, $tableName);
        return $this->respond('exporting'); //todo better response
    }

    /**
     * download()
     *
     * Downloads an export
     */
    public function download(Request $request, $file) {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        return response()->download(storage_path('app/exports/' . $file));
    }

    /**
     * downloadReport()
     *
     * Downloads an export
     */
    public function downloadReport(Request $request, $file) {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        return response()->download(storage_path('app/reports/' . $file));
    }
}
