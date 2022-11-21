<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'user_id',
        'post_id'
    ];
}