<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\MailerCustom;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
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

    public function index()
    {

        $users = DB::table('userview')->paginate(9);

        return response()->json(['data' => $users], 200);
    }

    public function showLoggedUser()
    {
        $user = Auth::user();

        $data = DB::table('userview')->where('id', $user->id)->first();
        return response()->json($data, 200);
    }

    public function show($id)
    {
        try {
            return new UserResource(User::findOrFail($id));
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Oops! User not found.'], 404);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $password = $request->input('password');

            $user = User::findOrFail($id);

            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->password = app('hash')->make($password);

            $user->save();

            // notify user

            $mailer = new MailerCustom;

            $to = array(
                $user->email,
            );

            $subject = 'YOUR ACCOUNT HAS BEEN UPDATED';

            $body = '
                Good day <br/>  <br/>

                Your account has been updated. <br/> Your new password is: <br/>
                <strong>' . $password . '</strong>
                <br/>  <br/>
                <strong>Regards,</strong>
                <br/>
                FlexiPMS
            ';

            $mailer->mailer($to, $subject, $body);

            $users = DB::table('userview')->paginate(9);

            return response()->json(['data' => $users], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error occured. Please try again'], 409);
        }
    }
}
