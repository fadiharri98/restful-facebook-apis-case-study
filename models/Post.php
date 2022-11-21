<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'content',
        'user_id'
    ];
}