<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'friend_id'
    ];
}