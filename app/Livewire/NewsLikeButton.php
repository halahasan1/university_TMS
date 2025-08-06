<?php

namespace App\Livewire;

use App\Models\Like;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NewsLikeButton extends Component
{
    public News $news;

    public function toggleLike()
    {
        $user = Auth::user();

        if ($this->news->isLikedBy($user)) {
            $this->news->likes()->where('user_id', $user->id)->delete();
        } else {
            $this->news->likes()->create(['user_id' => $user->id]);
        }

        $this->news->refresh();
    }

    public function render()
    {
        return view('livewire.news-like-button');
    }
}

