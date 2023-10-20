<?php

namespace App\Models;

use App\Casts\Date;
use App\Casts\Money;

use App\Enums\Format;
use App\Casts\DateTime;
use App\Traits\SensesModel;
use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use SensesModel, HasTags;


    protected $fillable = [
		'name',
		'stored_name',
		'path',
		'mime_type',
		'extension',
		'size',
		'disk',
		'folder',
		'preview_disk',
		'preview_path',
		'print_disk',
		'print_path'
	];

    protected $casts = [
		'locked_at' => DateTime::class,
		'created_at' => DateTime::class,
		'updated_at' => DateTime::class,
		'deleted_at' => DateTime::class,
		'hidden_at' => DateTime::class,
		'size' => 'integer'
	];

    public function scopeTableSearch($query, $search) {
        $table = $this->getTable();
        $query->where(function($q) use(&$search, $table) {
            $q->where( $table.'.id', 'like', '%'. $search .'%');
            $q->orWhere($table.'.title', 'ilike', '%'. $search.'%');
        });
    }

    public function allowedSorts()
    {
        return ['id', 'name', 'stored_name', 'path', 'mime_type', 'extension', 'size', 'disk', 'folder', 'preview_disk', 'preview_path', 'print_disk', 'print_path', 'created_at'];
    }

    public function allowedEmbeds()
    {
        return ['tags', 'fileable', 'url'];
    }

    public function allowedFields()
    {
        return ['id', 'name', 'stored_name', 'path', 'mime_type', 'extension', 'size', 'disk', 'folder', 'preview_disk', 'preview_path', 'print_disk', 'print_path'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'name' => 'text',
			'stored_name' => 'text',
			'path' => 'text',
			'mime_type' => 'text',
			'extension' => 'text',
			'size' => 'text',
			'disk' => 'text',
			'folder' => 'text',
			'preview_disk' => 'text',
			'preview_path' => 'text',
			'print_disk' => 'text',
			'print_path' => 'text'
        ]);
    }

	public function allowedAppends() {
		return ['url'];
	}

	public function events()
	{
		abort(501, 'File: Events Not implemented');
		//return $this->morphedByMany(Event::class, 'fileable');
	}

	public function services()
	{
		abort(501, 'File: Services Not implemented');
		//return $this->morphedByMany(Service::class, 'fileable');
	}

	public function getUrlAttribute() {
		return Storage::disk($this->disk)->url($this->path);
	}

}

//Generated 09-10-2023 13:46:51
