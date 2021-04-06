<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Facade\FlareClient\Http\Response;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        // $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user) {
        
        // check if the url is valid for account verification
        if(! URL::hasValidSignature($request)) {
            return response()->json(["errors" => [
            "message" =>  "Invalid link or signature"
            ]], 422);
        }
        // check if user verified account
        if($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" =>  "Email already verified"
                ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json( [
            "message" =>  "Email successfuly verified"
            ], 422);
    }

    public function resend(Request $request) {
        
        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if(! $user) {
            return response()->json(['errors' =>[
                'errors' => 'No user found'
            ]], 422);
        }

        if($user->hasVerifiedEmail()) {
            return response()->json(["errors" => [
                "message" =>  "Email already verified"
                ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification link sent']);
    }
}
