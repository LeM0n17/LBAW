<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class LikeController extends Controller
{
    public function addLike(Request $request){
        $userId = Auth::user()->id;
        $id_comment = $request->route('id_comment');
        $comment = Comment::findOrFail($id_comment);

        $like = New Like();

        $like->fill([
            'id_comment' => $id_comment,
            'id_developer' => $userId,
            'likes' => true,
        ]);

        $like->save();

        return redirect()->to("/events/{$comment->event->id}");
    }

    public function addDislike(Request $request){
        $userId = Auth::user()->id;
        $id_comment = $request->route('id_comment');
        $comment = Comment::findOrFail($id_comment);

        $like = New Like();

        $like->fill([
            'id_comment' => $id_comment,
            'id_developer' => $userId,
            'likes' => false,
        ]);

        $like->save();

        return redirect()->to("/events/{$comment->event->id}");
    }

    public function deleteLike(Request $request)
    {
        $userId = Auth::user()->id;
        $id_comment = $request->route('id_comment');
        $comment = Comment::findOrFail($id_comment);

        Like::where('id_developer', $userId)
            ->where('id_comment', $id_comment)
            ->delete();

        return redirect()->to("/events/{$comment->event->id}")
            ->withSuccess('Like deleted!');
    }
}
