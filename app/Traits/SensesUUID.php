<?php
namespace App\Traits;

use App\Models\Revision;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use PDO;
use App\Support\SensesUUID as SensesUUIDGenerator;
trait SensesUUID
{
    //todo make test that takes models as dataset and factory()->make and checks uuid creates and also allows incoming uuids to be set on each one
    protected static function bootSensesUUID()
    {

        static::creating(function ($model) {
            $model->setUUID();
        });

        static::replicating(function($model) {
            $model->setUUID();
        });
    }

    public function hasUUID() {
        return $this->uuids ?? true;
    }



    public function setUUID(bool $force = false) {
        if($this->uuids !== false && ($force ||  !$this->uuid)) {
            $this->uuid = SensesUUIDGenerator::generate();
        }
    }
}
