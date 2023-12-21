<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\File;

class LikeController extends Controller
{
    public function addLike(Request $request){
        $userId = Auth::user()->id;
        $id_file = $request->route('id_file');
        $file = File::findOrFail($id_file);

        $like = New Like();

        $like->fill([
            'id_file' => $id_file,
            'id_developer' => $userId,
            'likes' => true,
        ]);

        $like->save();

        return redirect()->to("/submissions/{$file->event->id}");
    }

    public function addDislike(Request $request){
        $userId = Auth::user()->id;
        $id_file = $request->route('id_file');
        $file = File::findOrFail($id_file);

        $like = New Like();

        $like->fill([
            'id_file' => $id_file,
            'id_developer' => $userId,
            'likes' => false,
        ]);

        $like->save();

        return redirect()->to("/submissions/{$file->event->id}");
    }

    public function deleteLike(Request $request)
    {
        $userId = Auth::user()->id;
        $id_file = $request->route('id_file');
        $file = File::findOrFail($id_file);

        Like::where('id_developer', $userId)
            ->where('id_file', $id_file)
            ->delete();

        return redirect()->to("/submissions/{$file->event->id}")
            ->withSuccess('Like deleted!');
    }
}
