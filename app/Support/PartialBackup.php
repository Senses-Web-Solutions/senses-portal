<?php 

namespace App\Support;

use App\Support\Backup;
use Illuminate\Console\Command;

class PartialBackup  extends Backup{
    public function __construct(Command $command = null) {
        parent::__construct($command, partial:true);
    }
}