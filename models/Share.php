<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'post_id'
    ];
}