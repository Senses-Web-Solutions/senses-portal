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
use Illuminate\Support\Facades\URL;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

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

	protected $appends = [
		'preview_url',
		'original_url',
		'print_url',
		'download_url'
	];

	protected $observables = [
		'attachingFileable',
		'moved',
		'previewGenerated',
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
			'print_path' => 'text',
			'pending' => 'boolean',
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

	public function messages()
	{
		return $this->morphedByMany(Message::class, 'fileable')->withTimestamps();
	}

	// public function getUrlAttribute() {
	// 	return Storage::disk($this->disk)->url($this->path);
	// }

	public function getUrlAttribute()
	{
		if ($this->preview_disk && $this->preview_path) {
			return $this->preview_url;
		} else {
			return $this->original_url;
		}
	}

	public function getPreviewUrlAttribute(): string|null
	{
		if (!($this->preview_disk && $this->preview_path)) {
			return null;
		}

		if (!$this->id) {
			return null;
		}

		return static::generateUrl($this->preview_disk, $this->preview_path, true, $this->id, true);
	}

	public function getPrintUrlAttribute(): string|null
	{
		if (!($this->print_disk && $this->print_path)) {
			return null;
		}

		if (!$this->id) {
			return null;
		}

		return static::generateUrl($this->print_disk, $this->print_path, true, $this->id, true);
	}

	public function getOriginalUrlAttribute(): string|null
	{
		if (!($this->disk && $this->path)) {
			return null;
		}

		if (!$this->id) {
			return null;
		}

		return static::generateUrl($this->disk, $this->path, true, $this->id, false);
	}

	public function getDownloadUrlAttribute(): string|null
	{
		return static::getOriginalUrlAttribute();
	}

	static public function generateUrl(string $disk, string $path, bool $public = true, int $fileID, bool $preview = false): string
	{

		if ($public) {
			return Storage::disk($disk)->url($path);
		}

		$adapter = Storage::disk($disk)->getAdapter();

		if (method_exists($adapter, 'temporaryUrl') || $adapter instanceof AwsS3V3Adapter) { //see laravel github for how temporaryUrl works
			return Storage::disk($disk)->temporaryUrl($path, now()->addDays(6));
		} else { //local disk urls aren't useful, so provide a temporary url
			$route = $preview ? 'api.files.previews.download' : 'api.files.download';
			return URL::temporarySignedRoute(
				$route,
				now()->addDays(7),
				['file' => $fileID]
			);
		}
	}

	public function emitPreviewGenerated()
	{
		event('eloquent.previewGenerated: ' . get_class($this), [$this]);
	}
}

//Generated 09-10-2023 13:46:51
