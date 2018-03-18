<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as Eloquent ;

class BusInfo extends Eloquent
{
     protected $table = 'tbl_bus_info';
     protected $fillable = array('id','user_id','ucsi_num','client_table',
     'markutype','plate_num','gps_num','location','date','time','lat','long','engine','remark',
     'created_at','updated_at');
     
}