<?php

namespace App\Actions\Exports;

use Throwable;
use App\Models\User;

use Illuminate\Bus\Batch;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\RunRouteFromUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Bus;
use App\Jobs\Exports\TableExportChunk;
use App\Jobs\Exports\TableExportCSVChunk;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Jobs\Exports\TableExportExcelChunk;
use App\Notifications\ExportDownloadReady;
use App\Notifications\ExportFailed;
use Spatie\QueueableAction\QueueableAction;
use Illuminate\Pagination\LengthAwarePaginator;

class TableExportExcel
{
    use QueueableAction, RunRouteFromUrl;

    protected $transaction;

    public function execute(string $url, string $name, int $limit = 100, int $lastPage = null, string|null $tableName = null)
    {
        $name = Str::slug($name) . '.csv';
        $lastPage = $lastPage ?? $this->resolveLastPage($url, $limit);
        $user = $user ?? getCurrentUserOrSystemUser();
        $transaction = Transaction::pending(type:'table-export', message:'Export queued', progressTotal: $lastPage, data:compact('url'), user:$user);
        $filePath = $this->getFilePath($name);
        $this->chunkExport($url, $filePath, $name, $user, $limit, $transaction, $lastPage, $tableName);
    }

    public function resolveLastPage(string $url, int $limit = 100) {

        $data = $this->getApiRouteData($url, ['page' => 1, 'limit' => $limit]);

        if($data instanceof LengthAwarePaginator) {
            return $data->lastPage();
        }
        else if($data instanceof JsonResponse) {
            return isset($data->getData()->last_page) ? $data->getData()->last_page : 0;
        }

        return 0;
    }

    public function getFilePath(string $name) {
        return storage_path('app/exports/'. $name);
    }

    public function chunkExport(string $url, string $filePath, string $fileName, User $user, int $limit = 100, Transaction $transaction = null, int $lastPage = null, string|null $tableName = null) {

        //initialise file for jobs
        file_put_contents($filePath, '');

        //fire jobs
        $jobs = [];

        for($page = 1; $page <= $lastPage; ++$page) {
            array_push($jobs, new TableExportCSVChunk($url, $page, $filePath, $limit, $transaction));
        }

        array_push($jobs, function() use(&$transaction, &$user, &$fileName, $tableName) {
            optional($transaction)->finish('Export is ready to download');
            $user->notify(new ExportDownloadReady($fileName, $transaction, $tableName));
        });

        Bus::chain($jobs)
        ->catch(function (Throwable $e) use(&$transaction, &$user) {
            optional($transaction)->failed('Export failed', ['exception' => $e->getMessage()]);
            $user->notify(new ExportFailed($transaction));
        })
        ->dispatch();

        // Bus::batch($jobs)
        // ->name('table-export')
        // ->then(function(Batch $batch) use(&$transaction) {
        //     optional($transaction)->markSuccessful('Export is ready to download'); //todo probably should just be a job/notification?
        // })
        // ->name('table-export')
        // ->dispatch();
    }
}
