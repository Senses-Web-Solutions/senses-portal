<?php

namespace App\Jobs\Exports;

use Illuminate\Http\Request;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Traits\RunRouteFromUrl;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class TableExportExcelChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RunRouteFromUrl;

    protected $url;
    protected $page;
    protected $filePath;
    protected $limit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $url, int $page, string $filePath, int $limit = 100) //todo take in transaction to keep track
    {
        $this->url = $url;
        $this->page = $page;
        $this->filePath = $filePath;
        $this->limit = $limit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tempFilePath = $this->getTemporaryFileName();

        $writer = SimpleExcelWriter::create($tempFilePath);
        $this->addExistingRows($writer);
        $this->addNewRows($writer);
        $this->replaceFileWithTemporary($tempFilePath);
    }

    public function addExistingRows(SimpleExcelWriter &$writer) {
        $rows = SimpleExcelReader::create($this->filePath)->getRows();
        $rows->each(function(array $row) use(&$writer) {
            $writer->addRow($row);
        });
    }

    public function addNewRows(SimpleExcelWriter &$writer) {
        $data = $this->getApiRouteData($this->url, ['page' => $this->page, 'limit' => $this->limit]);
        $responseData = $data->toArray();
        $writer->addRows($responseData['data']);
    }

    public function replaceFileWithTemporary(string $tempFilePath) {
        rename($tempFilePath, $this->filePath);
    }

    public function getTemporaryFileName() {
        $uuid = $this->job->getJobId() ?? uniqid('job_', true);
        return storage_path('app\exports\temp-export-' . $uuid . '.csv');
    }
}
