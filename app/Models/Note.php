<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'User_id', 'remainder', 'pinned', 'archived', 'deleted', 'index'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function labels()
    {
        return $this->belongsToMany(Label::class, 'labels_notes', 'note_id', 'label_id')->withTimestamps();
       
    }
}
