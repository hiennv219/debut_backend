<?php

namespace App\Http\Services;

use App\Models\Note;
use App\Events\NoteUpdated;
use Illuminate\Support\Facades\Auth;

class NoteService {

    public function getNotes($params) {
      $notes = Note::orderBy('id', 'desc');
      if(array_key_exists('keywords', $params) && strlen($params['keywords']) >= 2) {
          $notes = Note::search($params['keywords']);
      }
      return $notes->get();
    }

    public function createNote($params) {
      $userId = Auth::id();
      $note = Note::create([
        'title' => $params['title'],
        'content' => $params['content'],
        'private' => $params['private'],
        'author_id' => $userId,
      ]);
      event(new NoteUpdated($note));
      return $note;
    }

}
