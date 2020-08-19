<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $comments_raw = DB::table('comments')->where('backlog_id', $id)->get();

        $comments = [];

        $utility = new Utility;

        foreach ($comments_raw as $comment) {
            $comment1 = [
                'id' => $comment->id,
                'author' => $comment->first_name . ' ' . $comment->last_name,
                'comment' => $comment->comment,
                'time_date' => $utility->time_elapsed($comment->created_at),
                'initials' => \substr($comment->first_name, 0, 1) . '' . \substr($comment->last_name, 0, 1),
            ];

            array_push($comments, $comment1);
        }

        return response()->json($comments, 200);
    }

    public function show($id)
    {
        # code...
    }

    public function create(Request $request, $id)
    {
        $user = Auth::user();
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $user_id = $user->id;
        // $id = $request->input('backlog_id');
        try {
            $comment = new Comment;

            $comment->backlog_id = $id;
            $comment->comment = $request->input('comment');
            $comment->first_name = $first_name;
            $comment->last_name = $last_name;
            $comment->user_id = $user_id;

            $comment->save();

            $comments = [];

            $comments_raw = DB::table('comments')->where('backlog_id', $id)->get();

            $utility = new Utility;

            foreach ($comments_raw as $comment) {
                $comment1 = [
                    'id' => $comment->id,
                    'author' => $comment->first_name . ' ' . $comment->last_name,
                    'comment' => $comment->comment,
                    'time_date' => $utility->time_elapsed($comment->created_at),
                    'initials' => \substr($comment->first_name, 0, 1) . '' . \substr($comment->last_name, 0, 1),
                ];

                array_push($comments, $comment1);
            }

            return response()->json($comments, 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error occured. Please try gain'], 409);
        }
    }

}
