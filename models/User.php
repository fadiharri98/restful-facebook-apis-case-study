<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'profile_img'
    ];

    protected $hidden = [
        'password'
    ];
}