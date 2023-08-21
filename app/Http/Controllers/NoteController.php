<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller

{
    //function for create new notes.....
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'index' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $note = new Note([
            'title' => $request->title,
            'content' => $request->content,
            'index' => $request->index,
        ]);
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $note->user_id = Auth::user()->id;
        if ($note->save()) {
            return response()->json([
                'message' => 'Note created successfully'
            ], 201);
        } else {
            return response()->json([
                'message' => 'Failed to create note'
            ]);
        }
    }

    // function for get notes 
    public function getNotes(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['messgae' => 'Unauthorized user'], 401);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'user not found'], 404);
        }
        $notes = $user->note;
        return response()->json(['Notes' => $notes]);
    }


    //function for edit note 
    public function editNote(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthrized'], 401);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'user not found'], 404);
        }
        $note = Note::find($id);
        if (!$note) {
            return response()->json(['message' => 'Note not fond']);
        }
        if ($note->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized to edit this note'], 403);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $note->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);
        return response()->json(['message' => 'Note updated successfully']);
    }

    //function for delete note 
    public function deleteNote($id)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized user'], 401);
        }
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $note = Note::find($id);
        if (!$note) {
            return response()->json(['message' => 'Note not found'], 401);
        }
        $note->delete();
        return response()->json(['Message' => 'Note deleted successfully'], 202);
    }

    //FUNCTION FOR IS_ARCHIVED
    public function is_archived($id){
        $note = Note::find($id);
        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }
    
        $note->update(['archived' => !$note->archived]);
        
        return response()->json(['message' => 'Note archived successfully'], 200);
    }
    //function for pinned notes
    public function pinnedNote(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        $note->update(['pinned' => !$note->pinned]);

        return response()->json(['message' => 'Note pinned/unpinned successfully'], 200);
    }
}
