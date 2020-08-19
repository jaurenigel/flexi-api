<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\MailerCustom;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller{

     /**
     * @var Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
         $this->jwt = $jwt;
    }

    /**
     * register user
     */
    public function register(Request $request)
    {
        /**
         * validate date
         */
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        /**
         * create a new user
         */
        try {
            $user = new User;
            $password = $request->input('password');

            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->password = app('hash')->make($password);


            if($request->input('role_id') != null){
                $user->role_id = $request->input('role_id');
            }

            /**
             * save user and response with 201 success message
             */
            $user->save();

            /**
             * notify user
             */

            $mailer = new MailerCustom;

            $to = array(
                $user->email
            );

            $subject = 'YOUR ACCOUNT HAS BEEN CREATED';

            $body = '
                Good day '.$user->first_name.'
                <br/> <br/>
                Your FlexiPMS has been created. <br/>
                Please find your credentials:  <br/> <br/>
                Email: <strong> '.$user->email .' </strong> <br/>
                Password: <strong>'.$password.' </strong>
                <br/> <br/>

                <strong>Regards,</strong><br/>
                FlexiPMS Team
            ';

            $mailer->mailer($to, $subject, $body);

            $user->roles()->attach(Role::where('id', $request->input('role_id'))->first());

            $users = DB::table('userview')->paginate(10);

            return response()->json($users, 201);
        } catch (\Throwable $th) {
            /**
             * throw error
             */
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }

    /**
     * login user
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'email or password is incorrect!'], 409);
        }

        return $this->respondWithToken($token);
    }

    public function logout(Request $request)
    {
        $token =  $request->header('Authorization');
        $this->jwt->parseToken()->invalidate();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

}
