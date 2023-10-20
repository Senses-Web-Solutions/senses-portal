<?php

namespace App\Models;

use App\Traits\SensesModel;
use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    use SensesModel;

    public $uuids = false;
    public $actionLogsEnabled = false;
    static $disableHiddenFields = true;
    protected $fillable = ['before', 'after'];
    protected $casts = ['before' => 'array', 'after' => 'array'];

    public function revisionable()
    {
        return $this->morphTo();
    }
}
