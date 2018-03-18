<?php

namespace App\Api\V1\Controllers;

use JWTAuth;
use Validator;
use Config;
use DB;
use App\User;
use Illuminate\Http\Request;
//use Request;
use Illuminate\Mail\Message;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\ValidationHttpException;

class AuthController extends Controller
{
        use Helpers;
        public function __construct(Request $request) 
        {
       //date_default_timezone_set('Asia/Calcutta');
            $collection = [];
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json'))
            {
            return $request->all();
            }
            else
            {
            return $this->response->error('Request type should be Content-Type:application/json', 403);  
            }
        }
      
        public function index() {


        $result = collect(["status" => "1", "message" => "Welcome to Rymindr Rajeev"]);
        return $result;
    }
    
    
        //Login Api 
        public function login(Request $request)
        {
    
        $credentials = $request->only(['user_name', 'password']);
        $validator = Validator::make($credentials, [
            'user_name' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) 
            {
                
                return $this->response->errorUnauthorized();
            }
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token', 500);
        }
            $matchThese = ['user_name' => $request['user_name']];
            $match2 = ['is_verify' =>1];
            $user = User::Where($matchThese)->Where($match2)->take(1)->get();
            //print_r($user);die;
            if(count($user) > 0)
            {
            $token= response()->json(compact('token'));
            $user[0]['token']=$token->getData()->token;
            
            $result = collect(["status" => "1", "message" => 'Bravo! you made it. Login Successful', 'errorCode' => '', 'errorDesc' => '', "data" => $user[0]]);
            return $result;
            }
            else
            {
             $matchThese = ['user_name' => $request['user_name']];
            $user = User::Where($matchThese)->take(1)->get();   
            $token= response()->json(compact('token'));
            $user[0]['token']=$token->getData()->token;
            $result = collect(["status" => "2", "message" => 'you are not authorized to user.Please verify your mobile no.', 'errorCode' => '', 'errorDesc' => '', "data" =>$user[0]]);
            return $result;          
            }
        
    }
    
    //Get Token    
    public function get_token(Request $request)
    {
    
        $credentials = $request->only(['user_name', 'password']);
        $validator = Validator::make($credentials, [
            'user_name' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response->errorUnauthorized();
            }
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token', 500);
        }
            
        return response()->json(compact('token'));
    }



	//Registration Process
    public function signup(Request $request)
    {
        //$data = $request->all();
        //echo $request['email'];die;
        if($request['user_name'] && $request['password'] && $request['markutype']) 
        {
         
        $signupFields = Config::get('boilerplate.signup_fields');
        $hasToReleaseToken = Config::get('boilerplate.signup_token_release');
        $userData = $request->only($signupFields);   
        $validator = Validator::make($userData, Config::get('boilerplate.signup_fields_rules'));
        if($validator->fails()) 
        {
            throw new ValidationHttpException($validator->errors()->all());
        }
            $matchThese = ['user_name' => $request['user_name']];
            //$match2 = ['user_name' => $request['user_name']];
            $user = User::Where($matchThese)->take(1)->get();
           
        if($user->count()!=0)
        {
          $token=$this->get_token($request);
          $user[0]['token']=$token->getData()->token;
	  $result = collect(["status" => "2", "message" => 'User already exit', 'errorCode' => '', 'errorDesc' => '',"data" => $user[0]]);
         return $result;   
        }
        else
        {
        User::unguard();
        $user = User::create($userData); 
        User::reguard();
        //print_r($user);die;
        if(!$user->user_id) 
        {
           return $this->response->error('could_not_create_user', 500);
           
        }

        if($hasToReleaseToken)
        {
            $matchThese = ['user_name' => $request['user_name']];
           // $match2 = ['user_name' => $request['user_name']];
            $user = User::Where($matchThese)->take(1)->get();
            $token=$this->get_token($request);
            $user[0]['token']=$token->getData()->token;
           echo $bussinessId='MARK'.$this->random_num(6);
          
            $rest=User::where('user_name',$request['user_name'])->update(['first_name' =>"Rajeev",'last_name' =>"Verma",
            'ucsi_num' =>$bussinessId,'client_table'=>'CRM_ACTIVE','last_login'=>date('Y-m-d H:i:s')]);
            //print_r($rest);
            //die('Rajeev');
            $result = collect(["status" => "1", "message" => 'User successfully register', 'errorCode' => '', 'errorDesc' => '', "data" => $user[0]]);
                return $result;
            //return $this->login($request);
        }
        
        //return $this->response->created();
        }
        }
        else
        {
        $result = collect(["status" => "0", "message" => \Config::get('constants.results.400'), 'errorCode' => '400', 'errorDesc' => \Config::get('constants.results.400'), "data" => array()]);
            return $result;    
        }
    }

    	//Access token for fb login and google plus login


 //Get Random Number for bussiness 
 function random_num($size) {
    $alpha_key = '';
    $keys = range('A', 'Z');

    for ($i = 0; $i < 2; $i++) {
        $alpha_key .= $keys[array_rand($keys)];
    }

    $length = $size - 2;

    $key = '';
    $keys = range(0, 9);

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $alpha_key . $key;
}
      
        

    

    public function recovery(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required'
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(Config::get('boilerplate.recovery_email_subject'));
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->response->noContent();
            case Password::INVALID_USER:
                return $this->response->errorNotFound();
        }
    }

    public function reset(Request $request)
    {
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $validator = Validator::make($credentials, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        
        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                if(Config::get('boilerplate.reset_token_release')) {
                    return $this->login($request);
                }
                return $this->response->noContent();

            default:
                return $this->response->error('could_not_reset_password', 500);
        }
    }
    
    //AuthController
public function token(){
    $token = JWTAuth::getToken();
    if(!$token){
        throw new BadRequestHtttpException('Token not provided');
    }
    try{
        $token = JWTAuth::refresh($token);
    }catch(TokenInvalidException $e){
        throw new AccessDeniedHttpException('The token is invalid');
    }
    return $this->response->withArray(['token'=>$token]);
}





 






//End of controller
}