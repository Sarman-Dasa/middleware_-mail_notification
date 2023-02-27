<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\Response;
use App\Jobs\SendtextJob;
use App\Mail\welcomeMail;
use App\Models\User;
use App\Notifications\alertNotification;
use GrahamCampbell\ResultType\Success;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use IntlChar;

class AuthController extends Controller
{
    use Response;
    public function register(Request $request)
    {
        $validation  = validator($request->all(),[
            'name'      => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required|confirmed'
        ]);
       if($validation->fails())
       {
            return $this->sendErrorMessage($validation);
       }
       $request['password'] = Hash::make($request->password);
       $user = User::create($request->all());
      // Mail::to($request->email)->send(new welcomeMail($user));
       SendtextJob::dispatch($user)->delay(now()->addSeconds(5));
       return $this->sendSuccessResponse("User Registration Succesfully");

    }

    public function login(Request $request)
    { 
        $validation = validator($request->all(),[
            'email'     => 'required|email',
            'password'  => 'required'
        ]);
        $count = 1;
        if($validation->fails())
        {
             return $this->sendErrorMessage($validation);
        }

        if(Auth::attempt($request->only(['email','password']))){
            return $this->sendSuccessResnsoe('user Login Successfully');
        }
        else{
           
            if($request->session()->has('attempt')){
               $count++;
               session()->put('attempt',$count);
               return $this->sendFailerResponse( "hello");
           
            }else{
                session('attempt',1);
                return $this->sendFailerResponse( $count);
            }
            $user = User::where('email','=',$request->email)->first();
           // session()->put('attempt',2);
            if($count==2){
                $user->notify(new alertNotification($user));
            }
           //return $this->sendFailerResponse( $count);
           
        }

    }

  
}
