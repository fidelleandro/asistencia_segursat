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
                    <button type="button" name="button" onclick="getScheduleDetailed()">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                      </svg>
                    </button>
                  </div>
                </div>
              </form>
              <hr>
              <table id="reporte_detallado2" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Area</th>
                        <th>Fecha entrada</th>
                        <th>Fecha salida</th>
                        <th>Tiempo trabajado</th>
                        <th>Fecha</th>
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
                          url: 'get-personal-detailed',
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
                    function getScheduleDetailed(){
                      f_ini   = $("#filtro_fecha_inicio").val();
                      f_fin   = $("#filtro_fecha_fin").val();
                      area    = $("#filtro_area").val();
                      people  = $("#filtro_personal").val();
                      $('#reporte_detallado2 tbody').html('');
                      $('#reporte_detallado2').dataTable().fnClearTable();
                      $('#reporte_detallado2').dataTable().fnDestroy();

                      $.ajax({
                          type:"get",
                          dataType: "json",
                          url: 'get-horarios-detail',
                          data: {f_ini:f_ini, f_fin:f_fin, area:area, people:people},
                          beforeSend: function( xhr ) {
                          },
                          success: function(respuesta2) {
                            console.log(respuesta2);
                            var op2 = '';
                            for (i = 0; i < respuesta2.length; i++) {
                              h_entrada = respuesta2[i].fecha_entrada != null ? respuesta2[i].fecha_entrada.substring(0,respuesta2[i].fecha_entrada.toLocaleString().indexOf('.')) : '';
                              h_salida  = respuesta2[i].fecha_salida  != null ? respuesta2[i].fecha_salida.substring(0,respuesta2[i].fecha_salida.toLocaleString().indexOf('.')) : '';
                              tiempo    = respuesta2[i].Tiempo_trabajado;
                              op2 = '<tr>';
                              op2  += '<td>'+respuesta2[i].name+'</td>';
                              op2  += '<td>'+respuesta2[i].area+'</td>';
                              op2  += '<td>'+h_entrada+'</td>';
                              op2  += '<td>'+h_salida+'</td>';
                              op2  += '<td>'+tiempo+'</td>';
                              op2  += '<td>...</td>';
                              op2+= '</tr>';
                              $('#reporte_detallado2 tbody').append(op2);
                            }

                            $('#reporte_detallado2').DataTable({
                                dom: 'Bfrtip',
                                buttons: [
                                    'copy', 'csv', 'excel', 'pdf', 'print'
                                ],
                                "language": {
                                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                                },
                                responsive: true
                            });
                          }
                      });
                    }
                    $(document).ready(function() {
                        $('#reporte_detallado2').DataTable({
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
