<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Comment;
use Exception;

class CommentsController extends Controller
{
    public function createComment(Request $request)
    {
        $comment = new Comment();
        $eventid = $request->route('id');

        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: ' . $validator->errors());
            return redirect()->to("/events/{$eventid}")
                ->withErrors($validator)
                ->withInput();
        }

        $comment->fill([
            'id_writer' => Auth::id(),
            'id_event' => $eventid,
            'content' => $request->input('content'),
        ]);

        try {
            $comment->save();
            return redirect()->to("/events/{$eventid}")
                ->withSuccess('Comment created!');
        } catch (Exception $e) {
            return redirect()->to("/events/{$eventid}")
                ->withErrors(['error' => 'COMMENT NOT FROM PARTICIPANT!']);
        }
    }

    public function deleteComment(Request $request)
    {
        $commentId = $request->route('id');
        $comment = Comment::findOrFail($commentId);

        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()->to("/events/{$comment->envent->id}");
    }

    public function editComment(Request $request)
    {
        $commentId = $request->route('id');
        $comment = Comment::findOrFail($commentId);

        $this->authorize('edit', $comment);

        $comment->content = $request->input('content');
        $comment->save();

        return redirect()->to("/events/{$comment->event->id}");
    }
}
