<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CommentLikeButton extends Component
{
    public Comment $comment;

    public function toggleLike()
    {
        $like = $this->comment->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
        } else {
            $this->comment->likes()->create([
                'user_id' => Auth::id(),
            ]);
        }
    }

    public function getLikeCountProperty()
    {
        return $this->comment->likes()->count();
    }

    public function render()
    {
        return view('livewire.comment-like-button', [
            'liked' => $this->comment->isLikedBy(Auth::user()),
            'likeCount' => $this->comment->likes()->count(),
        ]);
    }
}
