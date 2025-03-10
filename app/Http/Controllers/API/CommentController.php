<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //get all comments
    public function index()
    {
        //get comments where request customer_id
        $comments = Comment::where('customer_id', request()->customer_id)->get();

        return response()->json(['comments' => $comments], 200);
    }
    //store a new comment
    public function store(Request $request)
    {
        $request->validate(
            [
                'comment' => 'required',
            ]
        );

        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->customer_id = $request->customer_id;
        $comment->staff_id = auth()->user()->staff_id;
        $comment->number_of_days_late = $request->number_of_days_late;
        $comment->save();

        return response()->json(['comment' => $comment], 201);
    }

    //get all comments
    public function getComments()
    {
        //get all comments with customer
        $comments = Comment::with('customer')->get();
        return response()->json(['comments' => $comments], 200);
    }
}
