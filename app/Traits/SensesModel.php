<?php

namespace App\Traits;

use App\Enums\Format;
use App\Traits\Lockable;
use Illuminate\Support\Str;
use App\Traits\HasRevisions;
use App\Traits\HasModelCache;
use App\Traits\HasSensesTable;
use App\Traits\SensesHidden;
use App\Traits\HasSensesTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

trait SensesModel
{
    use HasFactory, Lockable, HasModelCache, HasSensesTable, HasSensesTimestamps, SensesHidden, HasRevisions; //removed SensesUUID, HasActionLogs

    public function __construct(array $attributes = array())
    {
        $this->fillable = array_merge($this->fillable, config('client.fillable.' . Str::snake(class_basename($this)), []));
        parent::__construct($attributes); // Eloquent
    }

    public function getMorphClass()
    {
        return strtolower(Str::kebab(class_basename(get_class())));
    }

    public static function morphClass()
    {
        return (new static)->getMorphClass();
    }

    public function getObjectAttribute()
    {
        return $this->getMorphClass();
    }

    public function emitSaved()
    {
        event('eloquent.saved: ' . get_class($this), [$this]);
    }


    //useful for delaying the events
    public function emitCreated()
    {
        event('eloquent.created: ' . get_class($this), [$this]);
    }

    public function emitUpdated(...$args)
    {
        event('eloquent.updated: ' . get_class($this), [$this, ...$args]);
    }

    public function getDifferences($previousModel)
    {
        $changes = [];

        $ignored = [
            "updated_by",
            "updated_at",
        ];

        foreach ($this->getChanges() as $key => $value) {
            if (!in_array($key, $ignored)) {
                $previousValue = $previousModel[$key];

                if(is_array($previousValue)) {
                    $previousValue = json_encode(($previousValue));
                }

                if (class_basename($previousValue) == "Carbon") {
                    $previousValue = $previousValue->format('Y-m-d H:i:s');
                }

                $changes[$key] = [
                    "before" => $previousValue,
                    "after" => $value,
                ];
            }
        }

        return $changes;
    }

    //stop defaults being utc and use our normal format.
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format(Format::DATETIME->value);
    }
}
