<?php

namespace App\Http\Controllers;

use App\Http\Resources\SprintResource;
use App\Sprint;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        return SprintResource::collection(Sprint::with('tasks')->where('status_id', 2)->paginate(15));
    }

    public function create(Request $request)
    {
        $sprint = new Sprint;
    }
}
