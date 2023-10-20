<?php

namespace App\Traits;

use App\Actions\Users\CreateContractorUser;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasSensesTimestamps
{
    //TODO: do we need to track actual current user as well as impersonated user, it's extra columns on every model and wouldn't be useful 99% of the time.
    use SoftDeletes;

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by')->withHidden();
    }

    public function updater()
    {
        return $this->belongsTo('App\Models\User', 'updated_by')->withHidden();
    }

    public function deleter()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by')->withHidden();
    }

    public function save(array $options = [], User $user = null)
    {
        $user = getCurrentUserOrSystemUser();

        if(!$this->exists) {
            $this->creator()->associate($user?->id); //use ids, otherwise events triggered may cause memory issues, found commonly on logout
        }
        else {
            $this->updater()->associate($user?->id); //use ids, otherwise events triggered may cause memory issues, found commonly on logout
        }

        return parent::save($options);
    }

    public function delete() {
        $user = getCurrentUserOrSystemUser();

        $this->deleter()->associate($user->id);
        if(method_exists($this, 'saveQuietlyWithRevisions')) {
            $this->saveQuietlyWithRevisions();
        }
        else {
            $this->saveQuietly();
        }

        return parent::delete();
    }

}
