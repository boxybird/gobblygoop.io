<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use Symfony\Component\Uid\Ulid;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Prompt extends Model implements HasMedia
{
    use HasFactory, HasTags, InteractsWithMedia, Likeable;

    protected $guarded = [];

    protected $casts = [
        'is_private' => 'boolean',
        'is_blocked' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->ulid = Ulid::generate(now());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class);
    }

    protected function additionalFields(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $array = json_decode($value, true);
                
                return collect($array)
                    ->map(function ($value, $key) {
                        return [
                            'key'   => ucwords(str_replace('_', ' ', $key)),
                            'value' => $value,
                        ];
                    })
                    ->values();
            },
            set: function ($value) {
                return json_encode($value);
            }
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('s3');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150);

        $this->addMediaConversion('medium')
            ->width(650);

        $this->addMediaConversion('large')
            ->width(1200);
    }

    public function url(): string
    {
        return route('prompts.show', ['user' => $this->user, 'prompt' => $this->ulid]);
    }

    public function imageUrl(string $size = ''): string
    {
        return $this->getFirstMediaUrl('images', $size);
    }

    public function imageUrls(): Collection
    {
        return $this->getMedia('images')->map(function ($media) {
            return [
                'thumb'  => $media->getAvailableUrl(['thumb']),
                'medium' => $media->getAvailableUrl(['medium']),
                'large'  => $media->getAvailableUrl(['large']),
                'full'   => $media->getUrl(),
            ];
        });
    }
}
