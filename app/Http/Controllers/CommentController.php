<?php

namespace App\Http\Controllers;

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

    public function showAllComments(){
        return view('comments');
    }

    //store a new comment
    public function store(Request $request)
    {
        $request->validate(
            [
                'comment' => 'required'
            ]
        );

        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->customer_id = $request->customer_id;
        $comment->staff_id = auth()->user()->staff_id;
        $comment->save();

        return back()->with(['success' => 'Comment added successfully.']);
    }

    //get all comments
    public function getComments()
    {
        $logged_user = 1;
        $staff_id = 1106;
        //get all comments with customer
        $comments = $logged_user==1?Comment::with('customer')->get():Comment::where('staff_id', $staff_id)->with('customer')->get();
        return response()->json(['comments' => $comments], 200);
    }
}
