<?php

namespace App\Livewire;

use App\Models\News;
use App\Models\Comment;
use Livewire\Component;

class CommentsSection extends Component
{
    public $news;
    public $body;
    public $comments;

    protected $rules = [
        'body' => 'required|string|max:500',
    ];

    public function mount(News $news)
    {
        $this->news = $news;
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->comments = $this->news->comments()
            ->with('user')
            ->latest()
            ->get();
    }

    public function addComment()
    {
        $this->validate();

        Comment::create([
            'user_id' => auth()->id(),
            'commentable_id' => $this->news->id,
            'commentable_type' => News::class,
            'body' => $this->body,
        ]);

        $this->body = '';
        $this->loadComments();
    }

    public function render()
    {
        return view('livewire.comments-section');
    }
}
