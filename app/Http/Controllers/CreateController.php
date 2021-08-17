<?php
namespace App\Http\Controllers;
// use Illuminate\Support\Facades\Auth;
use Auth;
use Illuminate\Http\Request;
use App\Create;
//use App\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateController extends Controller
{   /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        $this->create = new Create;
    }
    public function getTypeDataTab() {
      $rs = $this->create->getTypeDataTab($_GET['tabla'],$_GET['field']);
      $resp['status'] = false;
      if (count($rs)) {
        $resp['status'] = true;
        $resp['data'] = $rs[0];
      }
      echo json_encode($resp); exit;
    }
    public function foreignFieldTable() {
      $table = $_GET['t'];
      $field = $_GET['f'];
      $list = array();
      $row = $this->create->foreignFieldTable($table,$field);
      if (count($row)) {
        $name = $row[0]->foreign_table_name;
        $name_id = $row[0]->foreign_column_name;
        $rows = $this->create->show_column_table($name);
        $list['tabla']    = $name;
        $list['tabla_id'] = $name_id;
        $list['rows']     = $rows;
      }
      echo json_encode($list); exit;
    }
    public function Create_roleGroup()
    {
        $this->create->setTable('role_groups');
        $this->create->setPrimaryKey('id');
        $this->create->id = $create->getNextStatementId();
        $this->create->name = "Administradores";
        $this->create->description = "Administradores del sistema";
        $this->create->is_active = 1;
        $this->create->save();
        echo "insert rolegroup success";
    }
    public function Create_role()
    {
        $this->create = new Create;
        $this->create->setTable('roles');
        $this->create->setPrimaryKey('id');
        $this->create->id = $this->create->getNextStatementId();
        $this->create->name = "Usuario";
        $this->create->description = "Usuario estandar";
        $this->create->status = '1';
        $this->create->save();
        echo "insert role success";
    }
    public function Create_roleUser()
    {
        $this->create->setTable('roles_users');
        $this->create->setPrimaryKey('id');
        $this->create->id = $this->create->getNextStatementId();
        $this->create->status = 'A';
        $this->create->role_id = 2;
        $this->create->user_id = 2;
        $this->create->save();
        echo "insert roleuser success";
    }
    public function Create_people()
    {
        $this->create->setTable('personas');
        $this->create->setPrimaryKey('id');
        $this->create->id = $create->getNextStatementId();
        $this->create->nombres = 'Fidel';
        $this->create->apepat = 'C R';
        $this->create->genero = 'M';
        $id = $this->create->save();
        echo "insert people success";
    }
    public function Create_peopleuser()
    {
        $this->create->setTable('user_people');
        $this->create->setPrimaryKey('iduser_people');
        $this->create->iduser_people = $create->getNextStatementId();
        $this->create->people_idpeople = 1;
        $this->create->user_iduser = 1;
        $id = $this->create->save();
        echo "insert peopleuser success ".$id;
    }
    public function Create_staff()
    {
        $this->create->setTable('staff');
        $this->create->setPrimaryKey('idstaff');
        $this->create->idstaff = $create->getNextStatementId();
        $this->create->name = 'Developer';
        $this->create->is_active = 1;
        $id = $this->create->save();
        echo "insert staff success ".$id;
    }
    public function Create_user()
    {
        //$this->create = new Create;
        $this->create->setTable('users');
        $this->create->setPrimaryKey('id');
        $this->create->id = $this->create->getNextStatementId();
        $this->create->email = 'sclip@msn.com';
        $this->create->username = 'sclip';
        $this->create->password = Hash::make('1234');
        $this->create->is_active = 1;
        $this->create->personas_id = 1;
        $id = $this->create->save();
        echo "insert user success";
    }
    public function Create_privilege()
    {
        $this->create->setTable('privileges');
        $this->create->setPrimaryKey('id');
        $this->create->id = $this->create->getNextStatementId();
        $this->create->name = 'Crear privilegios';
        $this->create->url = $this->create->slug('crear privilegios');
        $this->create->permission = $this->create->permisos('r,w');
        $this->create->is_active = 1;
        $this->create->parent = 0;
        $id = $this->create->save();
        echo "insert privileges success ".$id;
    }
    public function Create_role_privilege()
    {
        $this->create->setTable('role_privs');
        $this->create->setPrimaryKey('id');
        $this->create->id = $this->create->getNextStatementId();
        $this->create->roles_id = 1;
        $this->create->privileges_id = 1;
        $this->create->is_active = 1;
        $id = $this->create->save();
        echo "insert privileges success ".$id;
    }
    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Crear_privilegio_user(Request $request)
    {
        $response["status"] = false;
        try {
              $data = $request->all();
              if ($data["grupo_rol"] == "" || $data["rol"] == "") {
                throw new \Exception("Selecciona Grupo de rol", 1);
              }
              if ($data["rol_name"] == "") {
                throw new \Exception("Escribe un nombre", 1);
              }

              // $userId = Auth::user()?Auth::user()->id:$userId;
              $userId = Auth::guard('admin')->user()->id;

              $roles = $this->create->show_rol_of_user($userId);
              if (!isset($roles[0]["id"])) {
                throw new \Exception("No existe el rol", 1);
              }
              $create = new Create;
              $create->setTable('privileges');
              $create->setPrimaryKey('id');
              $create->id = $create->getNextStatementId();
              $create->is_report = $data["is_report"];
              $create->report = "{}";
              $create->is_files = $data["is_multimedia"];
              $create->name = $data["rol_name"];
              $create->url = $data["url"];
              $create->description = "";//$data["description"];
              $create->privilegio = $data["privilegio"];
              $create->parent = $data["rol_parent"] == "" ? 0 : $data["rol_parent"];
              $create->is_active = 1;
              $id = $create->save();
              $create2 = new Create;
              $create2->setTable('role_privs');
              $create2->setPrimaryKey('id');
              $create2->id = $create2->getNextStatementId();
              $create2->roles_id = $roles[0]["id"];
              $create2->privileges_id = $create2->id;
              $create2->is_active = 1;
              $create2->save();
              $response["status"] = true;
        } catch (\Exception $e) {
          $response["message"] = $e->GetMessage();
        }

        var_dump($response); exit;
        echo "insert privileges success ".$id;
    }
}
