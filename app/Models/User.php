<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Overtrue\LaravelLike\Traits\Liker;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, Liker;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    protected $with = [
        'likes',
    ];

    public function prompts()
    {
        return $this->hasMany(Prompt::class);
    }

    public function getRouteKeyName() : string
    {
        return 'name';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->useDisk('s3');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150);

        $this->addMediaConversion('small')
            ->width(300)
            ->height(300);
    }

    public function avatarUrl(string $size = 'thumb'): string
    {
        return $this->getFirstMediaUrl('avatars', $size);
    }

    public function initial(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    public function archiveUrl(): string
    {
        return route('prompts.index', $this->name);
    }

    public function likesUrl(): string
    {
        return route('prompts.likes.index', $this->name);
    }
}
