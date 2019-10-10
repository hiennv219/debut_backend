<?php

namespace App\Http\Services;

use App\Models\Note;
use App\Events\NoteUpdated;
use Illuminate\Support\Facades\Auth;

class NoteService {

    public function getNotes($params) {
      if(array_key_exists('keywords', $params) && strlen($params['keywords'])) {
          $notes = Note::search($params['keywords'])->get();
      }else {
          $notes = Note::get();
      }
      return $notes;
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
