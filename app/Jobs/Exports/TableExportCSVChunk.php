<?php

namespace App\Jobs\Exports;


use App\Models\User;
use App\Models\Transaction;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Traits\RunRouteFromUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class TableExportCSVChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RunRouteFromUrl;

    protected $user;
    protected $url;
    protected $page;
    protected $filePath;
    protected $limit;
    protected $transaction;
    /**
    * Create a new job instance.
    *
    * @return void
    */
    public function __construct(string $url, int $page, string $filePath, int $limit = 100, Transaction $transaction = null) //todo take in transaction to keep track
    {
        $this->url = $url;
        $this->page = $page;
        $this->filePath = $filePath;
        $this->limit = $limit;
        $this->transaction = $transaction;
    }

    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        if($this->transaction && !$this->transaction->isInProgress()) {
            $this->transaction->start('Export in progress');
        }

        $data = $this->getApiRouteData($this->url, ['page' => $this->page, 'limit' => $this->limit]);

        $rowData = [];
        if($data instanceof LengthAwarePaginator) {
            $responseData = $data->toArray();
            $rowData = isset($responseData['data']) ? $responseData['data'] : [];
        }
        else if($data instanceof JsonResponse) {
            $responseData = $data->getData(true);
            $rowData = isset($responseData['data']) ? $responseData['data'] : [];
        }

        $lines = [];

        if($this->page == 1 && isset($rowData[0])) {
            array_push($lines, implode(',',array_keys($rowData[0])));
        }

        foreach($rowData as $row) {
            $row = array_map(function($value) { return is_array($value) ? $this->recursive_implode(Arr::isAssoc($value) ? '|' : ", ", $value, Arr::isAssoc($value)) : $value; }, $row);
            $row = array_map(fn($col) => '"'. str_replace('"', '""', strip_tags($col)) . '"', $row); //wrap in quotes to avoid breaking row
            array_push($lines, implode(',', $row));
        }

        file_put_contents($this->filePath, implode(PHP_EOL, $lines), FILE_APPEND);

        optional($this->transaction)->incrementProgress();
    }

    //https://gist.github.com/jimmygle/2564610
    //based on
    /**
     * Recursively implodes an array with optional key inclusion
     *
     * Example of $include_keys output: key, value, key, value, key, value
     *
     * @access  public
     * @param   array   $array         multi-dimensional array to recursively implode
     * @param   string  $glue          value that glues elements together
     * @param   bool    $include_keys  include keys before their values
     * @param   bool    $trim_all      trim ALL whitespace from string
     * @return  string  imploded array
     */
    function recursive_implode($glue = ',', array $array, $include_keys = true, $trim_all = false)
    {
        $glued_string = '';

        // Recursively iterates array and adds key/value to glued string
        array_walk_recursive($array, function($value, $key) use ($glue, $include_keys, &$glued_string)
        {
            $include_keys and $glued_string .= $key.$glue;
            $glued_string .= $value.$glue;
        });

        // Removes last $glue from string
        strlen($glue) > 0 and $glued_string = substr($glued_string, 0, -strlen($glue));

        // Trim ALL whitespace
        $trim_all and $glued_string = preg_replace("/(\s)/ixsm", '', $glued_string);

        return (string) $glued_string;
    }
}
