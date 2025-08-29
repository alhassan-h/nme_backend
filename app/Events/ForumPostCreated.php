<?php

namespace App\Events;

use App\Models\ForumPost;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ForumPostCreated
{
    use Dispatchable, SerializesModels;

    public ForumPost $post;

    public function __construct(ForumPost $post)
    {
        $this->post = $post;
    }
}
