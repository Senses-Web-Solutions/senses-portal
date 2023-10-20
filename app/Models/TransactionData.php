<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasSensesTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionData extends Model
{
    use HasFactory, HasSensesTimestamps;

    protected $fillable = ['data', 'logs', 'reasons'];
    protected $casts = [
        'data' => 'array',
        'logs' => 'array',
        'reasons' => 'array',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }


    public function addLog($message, ?string $type = null, ?Carbon $logDate = null): array
    {
        $logs = $this->logs ?? [];
        array_push($logs, static::formatLog($message, $type, $logDate));
        $this->logs = $logs;
        $this->save();

        return $this->logs;
    }

    public static function formatLog(string $message, ?string $type = 'info', ?Carbon $logDate = null): array
    {
        if (!$logDate) {
            $logDate = now();
        }
        return ['type' => $type, 'message' => $message, 'log_date' => $logDate->toJson()];
    }

    public function addReason(string $reason): array
    {
        $reasons = $this->reasons ?? [];
        array_push($reasons, $reason);
        $this->reasons = $reasons;
        $this->save();

        return $this->reasons;
    }

    public function pushData(string $key, $value)
    {
        $data = $this->data ?? [];
        if (isset($data[$key]) && !is_array($data[$key])) {
            abort(400, "cannot push " . $key . ' into transaction data as its not an array.');
        }

        if (!isset($data[$key])) {
            $data[$key] = [];
        }

        array_push($data[$key], $value);
        $this->data = $data;
        $this->save();
        return $this->data;
    }

    public function mergeData(string $key, $value, $unique = true)
    {
        $data = $this->data ?? [];
        if (isset($data[$key]) && !is_array($data[$key])) {
            abort(400, "cannot push " . $key . ' into transaction data as its not an array.');
        }

        if (!isset($data[$key])) {
            $data[$key] = [];
        }
        $data[$key] = array_merge($data[$key], $value);
        if($unique) {
            $data[$key] = array_unique($data[$key]);
        }
        $this->data = $data;
        $this->save();
        return $this->data;
    }
}
