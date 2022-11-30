<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bar extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'foo_id',
        'value'
    ];

    public function posts(): BelongsTo
    {
        return $this->belongsTo(Foo::class);
    }
}