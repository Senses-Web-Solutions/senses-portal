<?php
namespace App\Support;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ProcessGroup {
    protected $processes;

    public function __construct()
    {
        $this->processes = collect();
    }

    public function run(Process $process) {
        $this->processes->put((string)Str::uuid(), $process);
        $process->start();
    }


    public function chunk(int $chunks, Callable $createProcess,  $workers = 1) {

        $chunkCount = 0;

        //initial spinup
        if($chunks < $workers) {
            $workers = $chunks;
        }

        for($i = 0; $i < $workers; ++$i) {
            $this->run($createProcess($i));
            ++$chunkCount;
        }

        $chunkCount = 0;
        while($this->processes->isNotEmpty()) {
            foreach($this->processes as $id => $process) {
                if(!$process->isRunning()) {
                    $this->processes->forget($id);
                    echo $process->getOutput();
                    
                    $nextChunk = $chunkCount + 1;

                    if($nextChunk <= $chunks) {                       
                        $this->run($createProcess($nextChunk));
                        ++$chunkCount;
                    }
                }
            }
        }
    }


    public function multiChunk(int $chunks, Callable $createProcess,  $workers = 16) {
        //give X processes several chunks

        //initial spinup
        if($chunks < $workers) {
            $workers = $chunks;
        }

        if($workers < 1) {
            $workers = 1;
        }

        $chunksPerProcess = ceil($chunks/$workers);

        for($i = 0; $i < $workers; ++$i) {
            $this->run($createProcess($i*$chunksPerProcess, $chunksPerProcess));
        }

        while($this->processes->isNotEmpty()) {
            foreach($this->processes as $id => $process) {
                if(!$process->isRunning()) {
                    echo $process->getOutput();
                    $this->processes->forget($id);
                }
            }
        }
    }
}