<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\TransactionStatus;
use App\Traits\HasSensesTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\QueryBuilder\AllowedFilter;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'worker', 'status', 'message', 'stats', 'queued_at'];
    protected $casts = ['stats' => 'array', 'queued_at' => 'datetime', 'started_at' => 'datetime', 'finished_at' => 'datetime'];
    protected $appends = ['object'];

    public function getObjectAttribute()
    {
        return $this->getMorphClass();
    }

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['worker'])) {
            $attributes['worker'] = config('app.name');
        }
        parent::__construct($attributes);
    }

    public function allowedFilters()
    {
        return [
            AllowedFilter::exact('status')
        ];
    }

    public function transactionData(): HasOne
    {
        return $this->hasOne(TransactionData::class)->withDefault();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function addStat(string $stat, $value)
    {
        $stats = $this->stats ?? [];
        $stats[$stat] = $value;
        $this->stats = $stats;
        $this->save();
    }

    public function incrementStat(string $stat, int|float $value = 1)
    {
        $stats = $this->stats ?? [];
        if (!isset($stats[$stat])) {
            $stats[$stat] = 0;
        }
        $stats[$stat] += $value;
        $this->stats = $stats;
        $this->save();
    }

    public function decrementStat(string $stat, int|float $value = 1, bool $positive = false)
    {
        $stats = $this->stats ?? [];
        if (!isset($stats[$stat])) {
            $stats[$stat] = 0;
        }
        $stats[$stat] -= $value;
        if ($positive && $stats[$stat] < 0) {
            $stats[$stat] = 0;
        }
        $this->stats = $stats;
        $this->save();
    }

    public function pushStat(string $stat, $value)
    {
        $stats = $this->stats ?? [];
        if (!isset($stats[$stat])) {
            $stats[$stat] = [];
        }
        array_push($stats[$stat], $value);
        $this->stats = $stats;
        $this->save();
    }

    public function scopeTableSearch(Builder $query, string $search)
    {
        $query->where('id', 'like', $search . '%');
    }

    public function getSearchTitleAttribute()
    {
        return $this->attributes['message'];
    }

    public function getSearchUrlAttribute(): string
    {
        return '/tasks/' . $this->id;
    }

    public static function pending(string $type, Carbon $date = null, int $progressTotal = 0, string $message = null, ?array $data = null, User $user = null, Model $transactionable = null): Transaction
    {
        $transaction = new Transaction();
        $transaction->type = $type;
        $transaction->queued_at = $date ?? now();
        $transaction->status = TransactionStatus::PENDING;
        $transaction->progress_total = $progressTotal;
        $transaction->message = $message;
        $transaction->transactionable()->associate($transactionable);
        $transaction->user()->associate($user ?? getCurrentUserOrSystemUser());
        $transaction->save();

        if ($data) {
            $transaction->transactionData->data = $data;
            $transaction->transactionData->save();
        }

        return $transaction;
    }

    public function start()
    {
        if ($this->started_at) {
            return;
        }
        $this->started_at = now();
        $this->status = TransactionStatus::IN_PROGRESS;
        $this->save();
    }

    public function finish(string $message = null)
    {
        if ($this->finished_at) {
            return;
        }
        if ($message) {
            $this->message = $message;
        }
        $this->finished_at = now();
        $this->status = TransactionStatus::SUCCESS;
        $this->save();
    }

    public function failed(string $message = null, ?array $data = null)
    {
        //Throwable $e in and store?
        if ($this->finished_at) {
            return;
        }
        if ($message) {
            $this->message = $message;
        }

        if ($data) {
            $this->transactionData->data = array_merge($this?->transactionData?->data ?? [], $data);
            $this->transactionData->save();
        }

        $this->finished_at = now();
        $this->status = TransactionStatus::FAILURE;
        $this->save();
    }

    public function isInProgress()
    {
        return $this->status == TransactionStatus::IN_PROGRESS;
    }

    public function getLogsAttribute(): array
    {
        return $this->transactionData->logs ?? [];
    }

    public function getReasonsAttribute(): array
    {
        return $this->transactionData->reasons ?? [];
    }

    public function addLog(string $message, string $type = null, ?Carbon $logDate = null)
    {
        return $this->transactionData->addLog($message, $type, $logDate);
    }

    public function addReason(string $reason)
    {
        return $this->transactionData->addReason($reason);
    }

    public function pushData(string $key, $value)
    {
        return $this->transactionData->pushData($key, $value);
    }
    public function mergeData(string $key, $value, $unique = true)
    {
        return $this->transactionData->mergeData($key, $value, $unique);
    }

    public function setProgressTotal(int $progressTotal = 1, bool $save = true)
    {
        $this->progress_total = $progressTotal;
        if ($save) {
            $this->save();
        }
        return $this->progress_total;
    }

    public function incrementProgress(int $progress = 1, bool $save = true)
    {
        if ($save) {
            $this->increment('progress', $progress);
        } else {
            $this->progress += $progress;
        }
        return $this->progress;
    }

    public function incrementProgressTotal(int $progress = 1, bool $save = true)
    {
        if ($save) {
            $this->increment('progress_total', $progress);
        } else {
            $this->progress += $progress;
        }
        return $this->progress;
    }
}
