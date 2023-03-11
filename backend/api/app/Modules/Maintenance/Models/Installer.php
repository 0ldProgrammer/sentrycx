<?php 

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Installer extends Model {
    protected $appends = [
        'filename',
        'url',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'application_id', 'base_filename', 'directory', 'disk', 'extension', 'mime_type'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'installers';

    /**
     * @return bool
     */
    protected function getFileExistsAttribute()
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    /**
     * @return string
     */
    protected function getFilenameAttribute()
    {
        return "{$this->base_filename}.{$this->extension}";
    }

    /**
     * @return int
     */
    protected function getFileSizeAttribute()
    {
        return $this->file_exists ? Storage::disk($this->disk)->size($this->path) : null;
    }

    /**
     * @return string
     */
    protected function getPathAttribute()
    {
        return "{$this->directory}/{$this->filename}";
    }

    /**
     * @return string
     */
    protected function getRealPathAttribute()
    {
        return $this->file_exists ? Storage::disk($this->disk)->path($this->path) : null;
    }

    /**
     * @return string
     */
    protected function getUrlAttribute()
    {
        return $this->file_exists ?  Storage::disk($this->disk)->url($this->path) : null;
    }
}
