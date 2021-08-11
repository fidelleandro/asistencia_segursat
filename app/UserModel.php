<?php
namespace App;
use Auth;
Use DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
  public function getDepartaments() {
    $rows = DB::select(DB::raw("EXEC getDepartament"));
    return $rows;
  }
  public function getPeople($type = 'all') {
    $rows = DB::select(DB::raw("EXEC getPeople @type='$type'"));
    return $rows;
  }
  public function getSchedule($f_ini,$f_fin,$area,$user) {
    $sql = "EXEC getschedule @fecha_ini='$f_ini',@fecha_fin='$f_fin',@area='$area',@userid ='$user'";
    //echo $sql;
    $rows = DB::select(DB::raw($sql));
    return $rows;
  }
}
