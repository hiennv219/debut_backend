<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AppBaseController;
use App\Http\Services\NoteService;
use App\Http\Requests\NoteRequest;

class NoteController extends AppBaseController
{

    public function __construct() {
        $this->noteService = new NoteService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $notes = $this->noteService->getNotes($request->all());
            return $this->sendResponse($notes);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NoteRequest $request)
    {
        try {
            $note = $this->noteService->createNote($request->all());
            return $this->sendResponse($note);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

}
