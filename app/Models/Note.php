<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Note extends Model
{
    use Searchable;

    protected $table = 'notes';

    protected $fillable = ['title', 'content', 'private', 'author_id'];

    public function searchableAs() {
        return 'notes_index';
    }

    public function toSearchableArray(){
        return $this->only('title', 'content', 'author_id');
    }
}
