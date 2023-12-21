<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\File;
use Exception;

class FilesController extends Controller
{
    public function createFile(Request $request)
    {
        $file = new File();
        $eventid = $request->route('id');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: ' . $validator->errors());
            return redirect()->to("/submissions/{$eventid}")
                ->withErrors($validator)
                ->withInput();
        }

        if (is_null($request->file))
        {
            return redirect()->to("/submissions/{$eventid}")
                ->withErrors(['error' => 'Not file uploaded!']);
        }

        $path = Storage::put("files", $request->file('file'));

        $file->fill([
            'id_developer' => Auth::id(),
            'id_event' => $eventid,
            'name' => $request->input('name'),
            'path' => $path,
        ]);

        try {
            $file->save();
            return redirect()->to("/submissions/{$eventid}");
        } catch (Exception $e) {
            return redirect()->to("/submissions/{$eventid}")
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function deleteFile(Request $request)
    {
        $commentId = $request->route('id');
        $comment = File::findOrFail($commentId);

        //$this->authorize('delete', $comment);

        $comment->delete();

        return redirect()->to("/events/{$comment->event->id}");
    }

    public function downloadFile(string $id)
    {
        $file = File::findOrFail($id);
        return Storage::download($file->path);
    }
}
