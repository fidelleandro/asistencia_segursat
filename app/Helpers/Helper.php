<?php
namespace App\Helpers;
use App\Create;
use App\UserModel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Helper
{
  public static function privilegesMenu(&$data,$array,$parent_id = 0) {
      $array = Helper::object_to_array($array);
      foreach ($array as $v) {
        if($v['parent'] == $parent_id) {
          if($v['parent'] == 0) {
            $open = (trim($v['url']) == Helper::getUrl()) ? 'menu-open' : '';
            $link = (trim($v['url']) == Helper::getUrl()) ? 'active' : '';
            $data.= '<li class="nav-item '.$open.'">';
            $data.=   '<a href="'.trim($v['url']).'" class="nav-link '.$link.'">';
            $data.=     '<i class="fas fa-circle nav-icon"></i>';
            $data.=     '<p>Reporte</p>';
            $data.=   '</a>';
            $data.= '</li>';
          }
          else {
            $open = ($v['url'] == Helper::getUrl()) ? 'menu-open' : '';
            $link = ($v['url'] == Helper::getUrl()) ? 'active' : '';
            $data.= '<li class="nav-item '.$open.'">';
            $data.=   '<a href="reporte" class="nav-link '.$link.'">';
            $data.=     '<i class="fas fa-circle nav-icon"></i>';
            $data.=     '<p>Reporte</p>';
            $data.=   '</a>';
            $data.= '</li>';
          }
          $data.= Helper::privilegesMenu($data,$array,$v['id']);
        }
      }
    //return $data;
  }
  public static function searchPrivileges($data) {
    if (is_array($data)) {
      foreach ($data as $key => $value) {
        if(trim($value['url']) === Helper::getUrl()){
          return true;
        }
      }
    }
    return false;
  }
  public static function getUrl(){
    return substr(url()->current(),strlen(url('/'))+1);
  }
  public static function makeMenu($menu,$parent,&$arr) {
    foreach ($menu as $item) {
      if ($item['id'] == $parent) {
          $arr[] = $item;
          Helper::makeMenu($menu,$item['parent'],$arr);
      }
    }
  }
  public static function isJson($string) {
    $arr = json_decode(utf8_decode($string),true);
    if(is_array($arr)) {
      return true;
    }
    else {
      $string2 = is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
      if ($string2 == true) {
        if(!is_array(json_decode($string))) {
          return false;
        }
      }
      return $string2;
    }
  }
  public static function arrayMakeBreadcrumb($url) {
    $userModel = new UserModel;
    $rows = Helper::object_to_array($userModel->getPrivileges());
    $id_priv = 0;
    $parent = 0;
    $title = 'Dashboard';
    $arr = array();
    if (is_array($rows)) {
      foreach ($rows as $key => $value) {
        if ($value['url'] == $url) {
           $arr[]   = $value;
           $id_priv = $value['id'];
           $parent  = $value['parent'];
           $title   = $value['title'];
           break;
        }
      }
      if ($parent != 0) {
          Helper::makeMenu($rows,$parent,$arr);
      }
    }
    $arr = array_reverse($arr);
    return $arr;
  }
  public static function makeBreadcrumb($url) {
      $arr = Helper::arrayMakeBreadcrumb($url);
      $arr2 = array_reverse($arr);
      $html = '';
      $parent = count($arr)-1;
      foreach ($arr as $key => $value) {
        $active = $parent == $key ? 'active' : '';
        $link = $parent == $key ? $value['name'] : '<a href="/'.$value['url'].'">'.$value['name'].'</a>';
        $html.= '<li class="breadcrumb-item '.$active.'">'.$link.'</li>';
      }
      return array("title" => $arr2[0]['name'],"breadcrumb" => $html);
  }
  public static function object_to_array($obj) {
    if(is_object($obj) || is_array($obj)) {
        $ret = (array) $obj;
        foreach($ret as &$item) {
            $item = Helper::object_to_array($item);
        }
        return $ret;
    }
    else {
        return $obj;
    }
  }
}
