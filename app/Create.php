<?php
namespace App;
use Helper;
// use Illuminate\Support\Facades\Auth;
use Auth;
Use DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Create extends Model
{
    protected $primaryKey;
    public $timestamps = true;

    public function getTableWhereCustom($fields,$tabla,$where = array()) {
      $w = '';
      $i = 0;
      foreach ($where as $key => $value) {
        if ($i != 0) {
          $w.=' AND ';
        }
        $v = is_numeric($value) ? $value : "'".$value."'";
        $w.= $key.'='.$v.' ';
        $i++;
      }
      $sql = 'SELECT '.$fields.' FROM '.$tabla.' WHERE '.$w;
      //echo $sql;// exit;
      $rows = DB::select(DB::raw($sql));
      return $rows;
    }
    public function getTableWhere($tabla,$where) {
      $rows = DB::table($tabla)->where($where);
      return $rows;
    }
    public function insertFieldDigit($table,$id_tab,$field,$digit,$simb,$ini = 0) {
      $digit = $this->generarDigitos($digit);
      $sql = 'SELECT max('.$field.') as cod FROM '.$table;
      $rows = DB::select(DB::raw($sql));
      $id = $rows[0]->cod != NULL ? $rows[0]->cod+1 : $digit;
      $id = is_numeric($id) ? $id : "'".$id."'";
      $sql = "INSERT INTO ".$table." ($field) VALUES (".$id.") returning ".$id_tab;
      $rows = DB::select(DB::raw($sql));
      if (isset($rows[0]->$id_tab)) {
            return array('id' => $rows[0]->$id_tab,'digit' => $id);
      }else{
            return array();
      }
    }
    public function generarDigitos($digit,$ini = 0) {
      $num_dig = '';
      if (1 < $digit) {
        $num_dig.= 1;
        for ($i = 0; $i < $digit; $i++) {
          $num_dig.= 0;
        }
      }
      $digit = $digit == 1 ? 1 : $num_dig;
      return $digit;
    }
    public function getGenerateId($table,$id,$digit = 1) {
      $digit = $this->generarDigitos($digit);
      $sql = 'select (CASE WHEN (max(id)+1 IS NULL) THEN
                        '.$digit.'
                      ELSE
                      max(id)+1
                      END) AS '.$id.'
              from '.$table;
      $rows = DB::select(DB::raw($sql));
      return $rows;
    }
    public function query_string_statement($sql) {
      $rows = DB::select(DB::raw($sql));
      return $rows;
    }
    public function query_string($sql) {
      $response['status'] = false;
      try {
            $rows = \DB::insert($sql);
            //var_dump($rows);
            //$response['id'] = \DB::getPdo()->lastInsertId();;
            $response['status'] = $rows;
      } catch (\Exception $e) {
        $response['message'] = $e->getMessage();
      }
      return $response;
    }
    public function setPrimaryKey($id) {
       $this->primaryKey = $id;
    }
    public function getNextStatementId() {
        return \DB::table($this->getTable())->max($this->getKeyName()) +1;
    }
    public function slug($text) {
        return Str::slug($text,'-');
    }
    public function permisos($text) {
      $rs = explode(',',$text);
      return json_encode($rs);
    }
    public function show_privileges() {
      $rows = \DB::select("SELECT id, name AS text FROM privileges");
      return $rows;
    }
    public function show_groupRole() {
      $rows = \DB::select("SELECT id, name AS text FROM role_groups");
      return $rows;
    }
    public function show_role() {
      $rows = \DB::select("SELECT id, name AS text FROM roles");
      return $rows;
    }
    public function rows_table_field2($table,$f1,$f2 = '') {
      $f2 = $f2 == '' ? '' : ','.$f2;
      $rows = \DB::select("SELECT ".$f1.$f2." FROM ".$table);
      return $rows;
    }
    public function rows_table_field3($table,$f1,$f2,$field_eti,$field_edti_val) {
      $vv = is_numeric($field_edti_val) ? $field_edti_val : "'".$field_edti_val."'";
      $sql = "SELECT $f1,$f2 FROM $table WHERE $field_eti = ".$vv;
      $rows = \DB::select($sql);
      return $rows;
    }
    public function deleteBatchJustify($table,$id,$dat,$datos_update) {
      $ids = '';
      $i = 0;
      foreach ($dat as $key => $value) {
        if ($i != 0) {
          $ids.= ',';
        }
        $ids.= $value;
        $i++;
      }
      $datos = ''; $i = 0;
      foreach ($datos_update as $key => $value) {
        if ($i != 0) {
          $datos.= ',';
        }
        $vv = is_numeric($value) ? $value : "'".$value."'";
        $datos.= $key.'='.$vv;
        $i++;
      }
      $sql = 'UPDATE '.$table.' SET '.$datos.' WHERE '.$id.' IN ('.$ids.')';
      //echo $sql;
      $rows = \DB::select($sql);
    //  $sql = "DELETE FROM $table WHERE ".$id." IN (".$ids.")";
      //echo $sql; exit;
      //$rows = \DB::select($sql);
      return $rows;
    }
    public function get_dinamic_sel($table,$f1,$f2,$f3,$v_id) {
      if ($f3 != "") {
        $sql = "SELECT $f1 as id,$f2 as text FROM $table WHERE $f3=".$v_id;
      }else {
        $sql = "SELECT $f1 as id,$f2 as text FROM $table WHERE $f1=".$v_id;
      }
      //echo $sql; exit;
      $rows = \DB::select($sql);
      return $rows;
    }
    public function get_dinamic_auto($table,$f1,$f2,$f3,$v_id,$f4 = '') {
      $f3 = $f3 != "" ? ','.$f3.' as id2 ' : '';
      $v_id = strtolower($v_id);
      $f4 = $f4 == '' ? '' : $f4.' AS codigo,';
      $sql = "SELECT $f1 as id,".$f4.$f2." as text ".$f3." FROM $table WHERE LOWER(".$f2.") LIKE '%".$v_id."%'";
      $rows = \DB::select($sql);
      return $rows;
    }
    public function buildReport($sql) {
      $rows = \DB::select($sql);
      return $rows;
    }
    public function foreignFieldTable($table,$field) {
      $sql = "SELECT  tc.table_schema,
                      tc.constraint_name,
                      tc.table_name,
                      kcu.column_name,
                      ccu.table_schema AS foreign_table,
                      ccu.table_name AS foreign_table_name,
                      ccu.column_name AS foreign_column_name
              FROM
                  information_schema.table_constraints AS tc
                  JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                  JOIN information_schema.constraint_column_usage AS ccu
                  ON ccu.constraint_name = tc.constraint_name
                  AND ccu.table_schema = tc.table_schema
              WHERE tc.table_name='".$table."' AND kcu.column_name ='".$field."'";
      $rows = \DB::select($sql);
      return $rows;
    }
    public function foreignFieldTable2($table,$table2) {
      if ($table == $table2) {
        $rows = array();
      }else{
        $sql = "SELECT  tc.table_schema,
                        tc.constraint_name,
                        tc.table_name,
                        kcu.column_name,
                        ccu.table_schema AS foreign_table,
                        ccu.table_name AS foreign_table_name,
                        ccu.column_name AS foreign_column_name
                FROM
                    information_schema.table_constraints AS tc
                    JOIN information_schema.key_column_usage AS kcu
                    ON tc.constraint_name = kcu.constraint_name
                    AND tc.table_schema = kcu.table_schema
                    JOIN information_schema.constraint_column_usage AS ccu
                    ON ccu.constraint_name = tc.constraint_name
                    AND ccu.table_schema = tc.table_schema
                WHERE tc.table_name='".$table."' AND ccu.table_name ='".$table2."'";
        $rows = \DB::select($sql);
      }

      return $rows;
    }
    public function getTypeDataTab($tabla,$field) {
      $sql = "SELECT column_name,
                     data_type,
                     (case when (data_type = 'integer') then
                     		true
                     	else
                     		case when (data_type = 'character') then
                     			true
                     		else
                     			case when (data_type = 'real') then
                     			    true
                     			else
                     			    case when (data_type = 'smallint') then
              	       			    true
              	       			else
              	       				false
              	       			end
                     			end
                     		end
                     	end
                     )
                     is_numeric
              from information_schema.columns
              where table_name = '".$tabla."' and column_name = '".$field."'";

      $rows = \DB::select($sql);
      return $rows;
    }
    public function show_priv_id($id) {
      $rows = \DB::select("SELECT id,name AS text FROM privileges WHERE id='".$id."' AND parent = 0");
      return $rows;
    }
    public function show_subpriv_id($id) {
      $rows = \DB::select("SELECT id,name AS text FROM privileges WHERE parent='".$id."'");
      return $rows;
    }
    public function show_tables() {
      $tables = \DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname='public' order by tablename ASC");
      return Helper::html_tables($tables);
    }
    public function show_rol_of_user($id) {
      $rows = \DB::select("SELECT roles_id AS id FROM role_users WHERE users_id='".$id."'");
      return Helper::object_to_array($rows);;
    }
    public function show_column_table($tabla) {
      $tables = \DB::select("SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name='".$tabla."' order by column_name ASC");
      return Helper::object_to_array($tables);;
    }

    public function getPrivileges() {
      $userId = Auth::guard('admin')->user()->id;
      // $userId = Auth::user() ? Auth::user()->id : NULL;
      if ($userId != NULL) {
        $sql = "SELECT  p.id as id,
                                   p.name,
                                   p.parent,
                                   p.url,
                                   p.icon,
                                   Deriv1.Count
                          FROM
                          privileges p
                          LEFT OUTER JOIN (SELECT parent,
                                                    COUNT(*) AS Count
                                             FROM privileges
                                             GROUP BY parent
                                             ) Deriv1 ON
                          p.id = Deriv1.parent
                          inner join users u2 on
                          u2.id = ".$userId."
                          inner join role_users ru ON
                          ru.users_id = u2.id
                          inner join role_privs rp on
                          rp.roles_id = ru.roles_id AND
                          rp.privileges_id = p.id
                          LEFT join user_privs pu on
                          pu.id = p.id
                          WHERE rp.is_active = '1'";
        //echo $sql; exit;
        $rs = \DB::select($sql);
        return $rs;
      }
      return null;
    }
    public function getUserInfo($url) {
        $rs = $this->getPrivileges();
        $rs = Helper::object_to_array($rs);
        $men = '';
        Helper::buildMenuFromObject($men,'id',0,$rs,$url);
        $men = Helper::buildConfigMenu($men,1,$url);
        return $men;
    }
}
