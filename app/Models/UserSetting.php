<?php

namespace App\Models;

use App\Traits\HasOwners;
use App\Traits\SensesModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use SensesModel, HasOwners;

    static $disableHiddenFields = true;


    protected $fillable = ['setting', 'data'];
    protected $casts = ['data' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCacheKeyAttribute()
    {
        return 'user-settings-' . $this->user_id . '-' . $this->setting;
    }
}
