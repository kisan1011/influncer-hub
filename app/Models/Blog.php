<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blogs';

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $status = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
    ];

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'content',
        'featured_image',
        'author_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'published_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'published_at',
        'deleted_at'
    ];

    // Scope for published blogs
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('published_at', '<=', now());
    }

    // Scope for latest blogs
    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    // Get the route key for the model
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Boot method to handle slug generation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = static::generateUniqueSlug($blog->title);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && empty($blog->slug)) {
                $blog->slug = static::generateUniqueSlug($blog->title);
            }
        });
    }

    // Generate unique slug
    public static function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    // Get featured image URL with fallback
    public function featuredImage(): Attribute
    {
        return new Attribute(
            get: fn ($value) => ($value != null && file_exists(public_path($value))) ? url('/public/' . $value) : url('/public/default/default_blog.png')
        );
    }

    // Get excerpt from content
    public function getExcerptAttribute()
    {
        return $this->short_description ?: Str::limit(strip_tags($this->content), 150);
    }

    // Get reading time estimate
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $readingTime = ceil($wordCount / 200); // Average reading speed: 200 words per minute
        return $readingTime . ' min read';
    }

    // Creator relationship
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Updater relationship
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
