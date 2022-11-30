<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Foo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'dummy',
    ];

    public function bars(): HasMany
    {
        return $this->hasMany(Bar::class);
    }
}