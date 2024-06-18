<?php

namespace App\Models;

use App\Casts\DateTime;
use App\Traits\SensesModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionLog extends Model
{
    use SensesModel;

    static $disableHiddenFields = true;
    protected $with = ['user'];

    public $uuids = false;
    public $actionLogsEnabled = false;
    protected $fillable = ['data', 'type'];
    protected $casts = [
        'data' => 'array',
        'logged_at' => DateTime::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function defaultSort()
    {
        return '-logged_at';
    }
}
