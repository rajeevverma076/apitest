<?php
	
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
                //print_r(Request::json()->all());die;
	$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');
	$api->post('auth/registration', 'App\Api\V1\Controllers\AuthController@signup');
        
	$api->group(['middleware' => 'api.auth'], function ($api)
        {
            $api->get('users', 'App\Api\V1\Controllers\UsersController@index');
            $api->post('users/GetBusInfo', 'App\Api\V1\Controllers\UsersController@get_bus_info');
            $api->post('users/CreateBusRoute', 'App\Api\V1\Controllers\UsersController@create_bus_route');
            
            
    	});
    
 
});