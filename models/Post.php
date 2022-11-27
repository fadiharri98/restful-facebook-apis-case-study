<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Collection;
use Serializers\CommentSerializer;

class Post extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'content',
        'user_id'
    ];

    protected $appends = ['likes_count', 'comments_count', 'recent_user_likes', 'recent_comments'];

    public function publisher_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments->count();
    }

    public function getRecentUserLikesAttribute()
    {
        /**
         * @var Collection $likes
         */
        $likes = $this->likes;

        $recentUserLikes = [];

        foreach ($likes->slice(0, 2) as $like) {
            /**
             * @var Like $like
             */
            $recentUserLikes[] = $like->user->username;
        }

        return $recentUserLikes;
    }

    public function getRecentCommentsAttribute()
    {
        $serializer =
            new CommentSerializer(
                $this->comments->slice(0, 5)
            );

        return $serializer->serializeMany();
    }
}