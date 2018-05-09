<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class PassportController extends Controller
{
    //
    public $successStatus = 200; //ok

    public function register(Request $request){
        //Validation


        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required',
            'c_password'=>'required|same:password'
        ]);

        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 401);
        }
        //success
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('Laravel')->accessToken;
        $success['name'] = $user->name;
        $success['email'] = $user->email;

        return response()->json(['data'=>$success, 'status_code'=>$this->successStatus, 
        'status_message'=>'success'], $this->successStatus);

    }

    public function login(Request $request){

        //try logging in the user
        if(Auth::attempt(['email'=>$request->email, 'password'=> $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('Laravel')->accessToken;
            $success['email'] = $user->email;
    
            return response()->json(['data'=>$success, 'status_code'=>$this->successStatus, 
            'status_message'=>'success'], $this->successStatus);
        }

        //return error
        return response()->json(['error'=>'request unauthorized'], 401);
    }

    public function getProfile(){
        $success = Auth::user();
        $success['token'] = $success->createToken('Laravel')->accessToken;

         return response()->json(['data'=>$success, 'status_code'=>$this->successStatus, 
         'status_message'=>'success'], $this->successStatus);

    }

    

}
