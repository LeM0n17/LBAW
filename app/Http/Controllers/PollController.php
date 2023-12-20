<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Votes;
use Illuminate\Support\Facades\Auth;
use App\Models\Poll;
use App\Models\Events;
use App\Models\PollOption;
use Illuminate\Support\Facades\Validator;

class PollController extends Controller
{
    public function showPolls($id_event)
    {
        $event = Events::where('id', $id_event)->first();

        return view('pages.polls', [
            'event' => $event,
        ]);
    }
    public function createPoll($id_event, Request $request)
    {
        $poll = new Poll();
        $validator = Validator::make($request->all(), [
            'poll_title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $poll->fill([
            'id_event' => $id_event,
            'title' => $request->input('poll_title'),
        ]);
        $poll->save();

        return redirect()->back();
    }
    public function AddVote($id_option)
    {
        $vote = new Votes();
        $vote->fill([
            'id_option' => $id_option,
            'id_developer' => Auth::user()->id
        ]);
        $vote->save();

        return redirect()->back();
    }

    public function addOption(Request $request, $id_poll)
    {
        $option = new PollOption();
        $validator = Validator::make($request->all(), [
            'option_name' => 'required',
        ]);

        $option->fill([
            'id_poll' => $id_poll,
            'name' => $request->input('option_name'),
        ]);
        $option->save();

        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    public function removeOption($id_option){
        $option = PollOption::where('id', $id_option)->first();
        $option->delete();

        return redirect()->back();
    }

    public function removePoll($id_poll){
        $poll = Poll::where('id', $id_poll)->first();
        $poll->delete();

        return redirect()->back();
    }

    public function removeVote($id_option){
        Votes::where('id_option', $id_option)->where('id_developer', Auth::user()->id)->delete();

        return redirect()->back();
    }
}
