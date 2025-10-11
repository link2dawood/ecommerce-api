<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'user_id',
        'is_read',
        'replied_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['status_badge', 'time_ago'];

    /**
     * Relationship: Contact belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Read messages
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope: Recent messages (last 7 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    /**
     * Scope: Today's messages
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope: Search messages
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('subject', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor: Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_read ? 'success' : 'warning';
    }

    /**
     * Accessor: Get human readable time
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Accessor: Get short message preview
     */
    public function getMessagePreviewAttribute()
    {
        return \Str::limit($this->message, 100);
    }

    /**
     * Check if message has been replied to
     */
    public function hasBeenReplied()
    {
        return !is_null($this->replied_at);
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Mark message as unread
     */
    public function markAsUnread()
    {
        return $this->update(['is_read' => false]);
    }

    /**
     * Mark as replied
     */
    public function markAsReplied()
    {
        return $this->update([
            'is_read' => true,
            'replied_at' => now()
        ]);
    }
}