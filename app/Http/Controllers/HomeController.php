<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserModel;
use Session;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $menu_privs;
    public $menu_privs_html;

    public function __construct()
    {
        $this->usermodel = new UserModel;
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
          $data = $request->session()->all();
          $this->menu_privs = $this->usermodel->getUserPrivileges($data['user_data']['id']);
          $this->menu_privs_html = $this->privilegesMenu($this->menu_privs);
          return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function reporte()
    {   $departamentos = $this->usermodel->getDepartaments();
        $personal = $this->usermodel->getPeople();
        return view('reporte')->with(compact('departamentos','personal'));
    }
    public function personReport()
    {
        return view('reporte2');
    }
    public function getSchedule(){
      $f_ini  = $_GET['f_ini'];
      $f_fin  = $_GET['f_fin'];
      $area   = $_GET['area'];
      $user   = $_GET['people'];

      $schedules = $this->usermodel->getSchedule($f_ini,$f_fin,$area,$user);
      echo json_encode($schedules); exit;
    }
    public function getPeople() {
      $peoples = $this->usermodel->getPeople($_GET['type']);
      echo json_encode($peoples); exit;
    }
}
