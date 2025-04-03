@extends("components.layout")
@section("content")
@component("components.breadcrumbs", ["breadcrumbs"=>$breadcrumbs])
@endcomponent
<div class="row">
    <div class="form-group my-3">
        <h1>Cambiar puesto del Empleado</h1>
    </div>
</div>
<div class="card p-4">
    <div class="row">
        <div class="col-2">Empleado: </div>
        <div class="col">{{$empleado->nombre}}</div>
    </div>
</div>
<form class="card p-4 mt-2" method="post" action='{{url("/empleados/{$empleado->id_empleado}/puestos/cambiar")}}'>
    @csrf
    <div class="row">
        <div class="form-group col-3">
            <label for="fecha_inicio">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
        </div>
        
        <div class="form-group col-6">
            <label for="puesto">Puesto</label>
            <select name="puesto" id="puesto" class="form-select" required>
                @foreach($puestos as $puesto)
                <option value="{{$puesto->id_puesto}}">{{$puesto->nombre}}</option>
                @endforeach
            </select>
        </div>
        <div class="from-group col-2">
            <br>
            <button type="submit" class="btn btn-primary">Cambiar</button>
        </div>
    </div>
</form>
@endsection