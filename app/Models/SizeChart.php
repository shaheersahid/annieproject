<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeChart extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'measurements'];

    protected $casts = [
        'measurements' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
