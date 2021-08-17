<?php
namespace App\Helpers;
use App\Create;
use App\UserModel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Helper
{
  public static function privilegesMenu($array,$parent_id = 0) {
    $data = array();
    foreach ($array as $k => $v){
        if($v['parent'] == $parent_id){
            $child = $this->privilegesMenu($array,$v['parent']);            // Únete a la matriz
            $v['child'] = $child?:array();
            $data[] = $v;// Agregar a la matriz
        }
    }    return $data;
  }
  public static function html_tables($tables) {
    $op = '';
    if (count($tables)) {
      foreach($tables as $table)
      {
        $t = $table->tablename;
        $op.= '<option value="'.$t.'">'.$t.'</option>';
      }
    }
    return $op;
  }
  public static function html_column_table($tables) {
    $op = '';
    if (count($tables)) {
      foreach($tables as $table)
      {
        $t = $table->column_name;
        $op.= '<option value="'.$t.'">'.$t.'</option>';
      }
    }
    return $op;
  }
  public static function build_op_sel($table,$f1,$f2,$valuefx) {
    $op = "";
    $create = new Create;
    $rows = $create->rows_table_field2($table,$f1,$f2);
    if (count($rows)) {
      foreach ($rows as $key => $value) {
        $select = !is_array($valuefx) && $valuefx != '' && $valuefx == $value->{$f1}  ? 'selected' : '';
        $text_value = $f2 != '' ? $value->{$f2} : '';
        $op.= '<option '.$select.' value="'.$value->{$f1}.'">'.$text_value.'</option>';
      }
    }
    return $op;
  }

  public static function build_priv($element) {
    //echo "mary";
    $op = '';$i = 0;
    foreach ($element as $key => $value) {
        if (isset($value['childs']) && is_array($value['childs'])) {
                $op.= '<div class="">';
                $op.= Helper::build_priv($value['childs']);
                $op.= '</div>';
                $i++;
        }else {
                $op .= Helper::bloque_design($value,$i); $i++;
        }
    }
    return $op;
  }



  public static function buildConfigMenu($mod,$config = true,$url) {
    $menu = '<li class="app-sidebar__heading"><i class="fas fa-tachometer-alt" style="margin-right: 3%;"></i>Dashboards</li>';
    $menu.=       '<li>';
    $active = $url == 'dashboard' ? 'mm-active' : '';
    $menu.=           '<a style="padding: 0 1.5rem 0 34px;" href="/dashboard" class="'.$active.'">';
    $menu.=              'Dashboard';
    $menu.=              '</a>';
    $menu.= $mod;
    return $menu.'</li>';
  }


  public static function buildMenuFromObject2(&$out,$id,$parent = 0,$menu,$link) {
           if(is_object($menu)) return;
           $arr = Helper::arrayMakeBreadcrumb($link);
           $arr2 = array_reverse($arr);
           $out.=  $out == '' ? '' : '<ul class="">';
           if (is_array($menu)) {
             foreach ($menu as $item) {
               $newtab = ' target="_parent"';
               if ( $item['parent'] == $parent) {
                   $icon = $item['icon'] == '' ? 'fa fa-dashboard fa-fw mr-3' : $item['icon'];
                   $active2 = isset($arr[count($arr)-1]['url']) && $arr[count($arr)-1]['url'] == $item['url'] ? 'active' : '';
                     $class_li = $item['parent'] == 0 ? 'app-sidebar__heading' : '';
                     $out .= '<li class="'.$class_li.' '.$active2.'">';
                          if ($item['parent'] == 0) {
                            $out .=   '<a class="'.$active2.'" href="'.$item['url'].'"'.$newtab.'>';
                            $out .= '<i class="'.$icon.'" style="margin-right: 3%;"></i>';
                            $out .=     $item['name'];
                            $out .=   '</a>';
                          }else {
                            $out .=   '<a class="'.$active2.'" href="'.$item['url'].'"'.$newtab.'>';
                            $out .=     $item['name'];
                            $out .=   '</a>';
                          }

                   if ($item['count'] != null ) {
                     $out .= Helper::buildMenuFromObject($out,$id,$item[$id],$menu,$link);
                   }
                   $out .= "</li>";
               }
             }
           }
           $out.= '</ul>';
  }

  public static function buildMenuFromObject(&$out,$id,$parent = 0,$menu,$link) {
    $arr = Helper::arrayMakeBreadcrumb($link);
    foreach($menu as $cat) {
        if($cat['parent'] == $parent){
          $icon = $cat['icon'] == '' ? 'fa fa-dashboard fa-fw mr-3' : $cat['icon'];
          $out.= '<li class="app-sidebar__heading"><i class="'.$icon.'" style="margin-right: 3%;"></i>'.$cat['name'].'</li>';
          foreach($menu as $cat2){
            $active2 = isset($arr[count($arr)-1]['url']) && $arr[count($arr)-1]['url'] == $cat2['url'] ? 'mm-active' : '';
            if($cat2['parent'] == $cat['id']){
              $out .= '<ul>';
              $out .= '<li>';
              $out .= '<a href="'.$cat2['url'].'" class="'.$active2.'">';
              $out .=  ''.$cat2['name'].'';
              $out .=  '</a>';
              $out .= '</li>';
              $out .= '</ul>';
            }
          }
        }
    }
  }

  public static function autoCompleteSelect($sql) {
    $create = new Create;
    $menu = Helper::object_to_array($create->query_string_statement($sql));
    return Helper::buildParentAutoComplete($menu);
  }
  public static function buildParentAutoComplete($menu,$parent = 0) {
    $out= '<ul class="list_autocomplete2">';
    foreach($menu as $cat) {
        if($cat['parent'] == $parent) {
          $cod = isset($cat['codigo']) ? $cat['codigo'].' ' : '';
          $out.= '<li class="">';
             $value2 = isset($cat['id2']) ? $cat['id2'] : '';
             $out.= '<div class="action_cp" onClick="app_fx_autoco2(this)" value="'.$cat['id'].'" value2="'.$value2.'">'.$cod.$cat['name'].'</div>';
          if ($cat['count'] != null ) {
            $out .= '<ul>';
            $out .= Helper::buildParentAutoComplete($menu,$cat['id']);
            $out .= '</ul>';
          }
          $out .= '</li>';
        }
    }
    $out .= '</ul>';
    return $out;
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
