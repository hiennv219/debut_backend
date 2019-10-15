<?php

namespace App\Http\Services;

use App\Models\Note;
use App\Events\NoteUpdated;
use Illuminate\Support\Facades\Auth;
use App\Consts;

class NoteService {

    public function getNotes($params) {
        $query = Note::orderBy('id', 'desc');
        if(array_key_exists('keywords', $params) && strlen($params['keywords']) >= 2) {
            $query = Note::search($params['keywords']);
        }

        if($params['type'] == 'private') {
            $query = $query->where('author', Auth::id());
        }else {
            $query = $query->social()->published();
        }

        return $query->get();
    }

    public function getNote($id) {
        return Note::where('id', $id)
            ->where(function($query) {
                $query->where('type', 0);
                $query->orWhere('author', Auth::id());
            })
            ->first();
    }

    public function createNote($params) {
        $data = [
            'title' => $params['title'],
            'content' => $params['content'],
            'author' => Auth::id(),
            'type' => $params['type'],
            'status' => $params['type'] ? null : Consts::STATUS_PENDING,
        ];
        $note = Note::create($data);
        event(new NoteUpdated($note));
        return $note;
    }
}
