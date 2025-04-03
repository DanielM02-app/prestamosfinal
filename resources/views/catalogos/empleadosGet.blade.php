@extends("components.layout")
@section("content")
@component("components.breadcrumbs", ["breadcrumbs"=>$breadcrumbs])
@endcomponent
<div class="row my-4">
    <div class="col">
        <h1>Empleados</h1>
    </div>
    <div class="col-auto titlebar-commands">
        <a class="btn btn-primary" href="{{url('/empleados/agregar')}}">Agregar</a>
    </div>
</div>

<table class="table" id="maintable">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">NOMBRE</th>
            <th scope="col">FECHA INGRESO</th>
            <th scope="col">ACTIVO</th>
            <th scope="col-1">ACCIONES</th>
        </tr>
    </thead>
<tbody>
@foreach($empleados as $empleado)
    <tr>
        <td class="text-center">{{$empleado->id_empleado}}</td>
        <td class="text-center">{{$empleado->nombre}}</td>
        <td class="text-center">{{$empleado->fecha_ingreso}}</td>
        <td class="text-center">{{$empleado->activo}}</td>
        <td class="text-center">
            <a class="btn btn-primary" href="{{ url('/empleados/'.$empleado->id_empleado.'/puestos') }}">Puestos</a>
            <a class="btn btn-primary" href="">Prestamos</a>
        </td>
    </tr>
@endforeach
</tbody></table>
<script>
/**
Se crea la instancia de datatable con esos usos paginación y buscador
let table=new DataTable("#maintable",{paging:true,searching:true})
*/
</script>
@endsection