<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_img'
    ];

    protected $hidden = [
        'password'
    ];
}