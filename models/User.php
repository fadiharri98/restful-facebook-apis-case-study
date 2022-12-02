<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Mixins\UserAuthorization;

class User extends Model
{
    use UserAuthorization;

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

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}