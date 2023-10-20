<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SchedulerResource {
    public function getSchedulerIdAttribute() {
        return Str::kebab(class_basename($this)) . '-' . $this->id;
    }

    abstract public function getSchedulerTitleAttribute();
}