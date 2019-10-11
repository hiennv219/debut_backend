<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use App\User;
use App\Consts;

class Note extends Model
{
    use Searchable;

    protected $table = 'notes';

    protected $fillable = ['title', 'content', 'author', 'type', 'status'];

    public function searchableAs() {
        return 'notes_index';
    }

    public function toSearchableArray(){
        return $this->only('title', 'content', 'author', 'type', 'status');
    }

    public function scopeSocial($query) {
        return $query->where('type', Consts::TYPE_SOCIAL);
    }

    public function scopePublished($query) {
        return $query->where('status', Consts::STATUS_APPROVED);
    }

    public function getAuthorIdAttribute($value) {
        $user = User::find($value);
        if($user) {
            return $user->email;
        }
        return "Anonymous";
    }
}
