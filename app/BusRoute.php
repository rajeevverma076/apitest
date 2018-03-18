<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as Eloquent ;

class BusRoute extends Eloquent
{
     protected $table = 'tbl_bus_route';
     protected $fillable = array('id','user_id','ucsi_num','client_table',
     'markutype','geozone','hotspot','geofence','route',
     'created_at','updated_at');
     
}