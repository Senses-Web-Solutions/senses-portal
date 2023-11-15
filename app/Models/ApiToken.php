<?php

namespace App\Models;

use App\Casts\DateTime;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $fillable = [
        'title'
	];

    protected $casts = [
		'created_at' => DateTime::class,
		'updated_at' => DateTime::class,
		'deleted_at' => DateTime::class,
		'expires_at' => DateTime::class,
	];

	public function server()
	{
		return $this->belongsTo(Server::class);
	}
}

//Generated 04-11-2023 16:09:50
