<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['imageable_id', 'imageable_type', 'path', 'type', 'order'];

    public function getUrlAttribute(): string
    {
        return resolve_image_path($this->path) ?? asset('assets/img/placeholder.png');
    }

    public function imageable()
    {
        return $this->morphTo();
    }
}
