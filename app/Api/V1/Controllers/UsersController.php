<?php

namespace App\Api\V1\Controllers;

use JWTAuth;
use App\User;
use App\BusInfo;
use App\BusRoute;
use DB;
use App\Http\Requests;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends Controller
{
    use Helpers;
        public function __construct(Request $request) 
        {
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

 

         //Get Bus Info
        public function get_bus_info(Request $request)
        {


            $currentUser = JWTAuth::parseToken()->authenticate();
            if(trim($request['ucsi_num']) && trim($request['client_table']) && trim($request['markutype']))
            {
              $busRequest = [
                'ucsi_num' => $request['ucsi_num'],
                'client_table' => $request['client_table'],
                'markutype' => $request['markutype'],];
                    $data =BusInfo::where($busRequest)->get();
            if($data->count() == 0) 
            {
                $result = collect(["status" => "2", "message" => 'Record not found', 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
                return $result;
            }
            else
            {
            $result = collect(["status" => "1", "message" => 'Bus List', 'errorCode' => '', 'errorDesc' => '',
             "data" => $data]);
            return $result;
       		 }
            }
            else
            {
             $result = collect(["status" => "0", "message" =>"Unknown request keyword", 'errorCode' => '400', 'errorDesc' =>"Request type invalid", "data" => array()]);
             return $result;    
            }
           
            
        }



 //Create Bus Route
 public function create_bus_route(Request $request)
 {


     $currentUser = JWTAuth::parseToken()->authenticate();


     if(trim($request['user_id']) && trim($request['ucsi_num']) && trim($request['client_table']) && trim($request['markutype']))
     {
       $busRoute = [
         'user_id' => $request['user_id'],
         'ucsi_num' => $request['ucsi_num'],
         'client_table' => $request['client_table'],
         'markutype' => $request['markutype'],
         'geozone' => $request['geozone'], 
         'hotspot' => $request['hotspot'], 
         'geofence' => $request['geofence'], 
         'route' => $request['route'],];
    // print_r($busRoute);die;
     $data = BusRoute::create($busRoute);
      $result = collect(["status" => "1", "message" => 'Route successfully created', 'errorCode' => '', 'errorDesc' => '', "data" => $data]);
     return $result;
         
     }
     else
     {
      $result = collect(["status" => "0", "message" =>"Unknown request keyword", 'errorCode' => '400', 'errorDesc' =>"Request type invalid", "data" => array()]);
      return $result;    
     }
    
     
 }











    private function currentUser() 
    {
        
        return JWTAuth::parseToken()->authenticate();
    }
}
