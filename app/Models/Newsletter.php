<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'content',
        'html_content',
        'status',
        'sent_at',
        'scheduled_for',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'scheduled_for' => 'datetime',
    ];

    public $timestamps = ['created_at'];
    const UPDATED_AT = null;

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENT = 'sent';

    public function recipients()
    {
        return $this->hasMany(NewsletterRecipient::class);
    }

    public function getRecipientsCountAttribute()
    {
        return $this->recipients()->count();
    }

    public function getOpenRateAttribute()
    {
        $total = $this->recipients()->count();
        if ($total === 0) return 0;

        $opened = $this->recipients()->whereNotNull('opened_at')->count();
        return round(($opened / $total) * 100, 2);
    }

    public function getClickRateAttribute()
    {
        $total = $this->recipients()->count();
        if ($total === 0) return 0;

        $clicked = $this->recipients()->whereNotNull('clicked_at')->count();
        return round(($clicked / $total) * 100, 2);
    }
}
