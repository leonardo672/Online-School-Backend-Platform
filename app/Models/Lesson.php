<?php
// app/Models/Lesson.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'content',
        'video_url',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });

        static::updating(function (Lesson $lesson) {
            // Only auto-generate slug if title changed and slug wasn't manually set
            if ($lesson->isDirty('title') && !$lesson->isDirty('slug')) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }

    /**
     * Get the course that owns the lesson.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the formatted video URL (YouTube, Vimeo, etc.)
     */
    public function getEmbeddedVideoUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        // Convert YouTube URLs to embed format
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        // Convert Vimeo URLs to embed format
        if (preg_match('/(?:vimeo\.com\/(?:video\/)?|player\.vimeo\.com\/video\/)(\d+)/', $this->video_url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }

        return $this->video_url;
    }

    /**
     * Get the previous lesson in the same course.
     */
    public function previous(): ?Lesson
    {
        return Lesson::where('course_id', $this->course_id)
            ->where('position', '<', $this->position)
            ->orderBy('position', 'desc')
            ->first();
    }

    /**
     * Get the next lesson in the same course.
     */
    public function next(): ?Lesson
    {
        return Lesson::where('course_id', $this->course_id)
            ->where('position', '>', $this->position)
            ->orderBy('position', 'asc')
            ->first();
    }

    /**
     * Scope a query to order by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Scope a query to get lessons by course.
     */
    public function scopeByCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }
}