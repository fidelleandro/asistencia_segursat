@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Reporte de asistencias</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Reporte</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<section class="content">
  <div class="container-fluid">
    <div class="justify-content-center">
        <div class=" ">
            <div class="card">
              <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">
              <link href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" rel="stylesheet">

              <form class="clearfix" action="index.html" method="post">
                <div class="row">
                  <div class="col">
                    <label class="label" for="filtro_fecha_inicio">Fecha inicio</label>
                    <input class="form-control" id="filtro_fecha_inicio" type="date" name="fecha_inicio" value="">
                  </div>
                  <div class="col">
                    <label class="label" for="filtro_fecha_inicio">Fecha fin</label>
                    <input class="form-control" id="filtro_fecha_fin" type="date" name="fecha_fin" value="">
                  </div>
                  <div class="col">
                    <label class="label" for="filtro_area">Area</label>
                    <select class="form-control" id="filtro_area" class="" name="area" onchange="getPersonal(this)">
                        <option value="all">Todos</option>
                        @foreach ($departamentos as $node)
                            <option value="{{ $node->id }}">{{ $node->name }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="col">
                    <label class="label" for="filtro_personal">Personal</label>
                    <select class="form-control" id="filtro_personal" class="" name="persona">
                        <option value="all">Todos</option>
                        @foreach ($personal as $node)
                            <option value="{{ $node->id }}">{{ $node->name }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="col">
                    <br>
                    <button type="button" name="button" onclick="getSchedule()">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                      </svg>
                    </button>
                  </div>
                </div>
              </form>
              <hr>
              <table id="reporte" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Area</th>
                        <th>Hora de ingreso</th>
                        <th>Hora de salida</th>
                        <th>Fecha</th>
                        <th>Medio</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
                  <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
                  <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
                  <script src="{{ asset('adminlte/') }}plugins/moment/moment.min.js"></script>
                  <script src="{{ asset('adminlte/') }}plugins/summernote/summernote-bs4.min.js"></script>
                  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
                  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
                  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
                  <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
                  <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
                  <script type="text/javascript">
                    function getPersonal(THIS){
                      type = $(THIS).val();
                      $.ajax({
                          //headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                          type:"get",
                          dataType: "json",
                          url: 'get-personal',
                          data: {type:type},
                          beforeSend: function( xhr ) {
                            $("#filtro_personal").prop("disabled",true);
                          },
                          success: function(respuesta) {
                            $("#filtro_personal").html('<option value="all">Todos</option>');
                            for (var i = 0; i < respuesta.length; i++) {
                               $("#filtro_personal").append('<option value="'+respuesta[i].id+'">'+respuesta[i].name+'</option>');
                            }
                            $("#filtro_personal").prop("disabled",false);
                          }
                      });
                    }
                    function getSchedule(){
                      f_ini = $("#filtro_fecha_inicio").val();
                      f_fin = $("#filtro_fecha_fin").val();
                      area = $("#filtro_area").val();
                      people = $("#filtro_personal").val();
                      //$('#reporte tbody').html('');
                      $('#reporte').dataTable().fnClearTable();
                      $('#reporte').dataTable().fnDestroy();

                      $.ajax({
                          type:"get",
                          dataType: "json",
                          url: 'get-horarios',
                          data: {f_ini:f_ini, f_fin:f_fin, area:area, people:people},
                          beforeSend: function( xhr ) {
                          },
                          success: function(respuesta) {
                            for (var i = 0; i < respuesta.length; i++) {
                              h_entrada = respuesta[i].h_entrada != null ? respuesta[i].h_entrada.substring(0,respuesta[i].h_entrada.toLocaleString().indexOf('.')) : '';
                              h_salida  = respuesta[i].h_salida  != null ? respuesta[i].h_salida.substring(0,respuesta[i].h_salida.toLocaleString().indexOf('.')) : '';
                              fecha     = respuesta[i].fecha.substring(0,respuesta[i].fecha.toLocaleString().indexOf('.'))
                              op = '<tr>';
                              op  += '<td>'+respuesta[i].name+'</td>';
                              op  += '<td>'+respuesta[i].area+'</td>';
                              op  += '<td>'+h_entrada+'</td>';
                              op  += '<td>'+h_salida+'</td>';
                              op  += '<td>'+fecha+'</td>';
                              op  += '<td>'+respuesta[i].identificacion+'</td>';
                              op+= '</tr>';
                              $('#reporte tbody').append(op);
                            }

                            $('#reporte').DataTable({
                                dom: 'Bfrtip',
                                buttons: [
                                    'copy', 'csv', 'excel', 'pdf', 'print'
                                ],
                                "language": {
                                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                                }
                            });
                          }
                      });
                    }
                    $(document).ready(function() {
                        $('#reporte').DataTable({
                            dom: 'Bfrtip',
                            buttons: [
                                'copy', 'csv', 'excel', 'pdf', 'print'
                            ],
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                            }
                        });
                    });
                 </script>
            </div>
        </div>
    </div>
  </div>
</section>
@endsection
