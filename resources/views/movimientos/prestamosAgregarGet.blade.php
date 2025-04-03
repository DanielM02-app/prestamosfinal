@extends("components.layout")
@section("content")
@component("components.breadcrumbs",["breadcrumbs"=>$breadcrumbs])
@endcomponent

<h1>Agregar préstamo</h1>

<form method="post" action="{{ url('/movimientos/prestamos/agregar') }}">
    @csrf
    <!-- sirve para validar que la petición de los datos enviados provengan del formulario actual peticionado -->

    <div class="form-group mb-3">
            <label for="nombre">Empleado:</label>
            <select class="form-select" name="id_empleado" required autofocus>
                <!-- muestra el nombre del empleado pero se guarda el id_empleado -->
                @foreach($empleados as $empleado)
                    <option value="{{ $empleado->id_empleado }}">
                        {{ $empleado->nombre }}
                    </option>
                @endforeach
            </select>
    </div>
    <div class="row">
    <div class="form-group mb-3 col-2">
            <label for="fecha solicitud">Fecha de solicitud</label>
            <input type="date" name="fecha_solicitud" id="fecha_solicitud" class="form-control" required>
    </div>
    <div class="form-group mb-3 col-2">
            <label for="monto">Monto</label>
            <input type="number" name="monto" id="monto" class="form-control" required>
        </div>

        <div class="form-group mb-3 col-2">
            <label for="plazo">Plazo (meses)</label>
            <input type="number" min="1" max="24" name="plazo" id="plazo" class="form-control" required>
            <div class="invalid-feedback">
                Ingresa un número válido
            </div>
        </div>

        <div class="form-group mb-3 col-2">
            <label for="tasa_mensual">Tasa mensual</label>
            <input value="1"type="number" name="tasa_mensual" id="tasa_mensual" class="form-control" required>
        </div>

        <div class="form-group mb-3 col-2">
            <label for="fecha_inicio_descuento">Fecha inicio descuento</label>
            <input type="date" name="fecha_inicio_descuento" id="fecha_inicio_descuento" class="form-control" required>
        </div>

        <div class="form-group mb-3 col-2">
            <label for="fecha_fin_descuento">Fecha fin descuento</label>
            <input readonly type="date" name="fecha_fin_descuento" id="fecha_fin_descuento" class="form-control" required>
        </div>
    </div>


    <div class="row">
        <div class="form-group mb-3 col-2">
            <label for="pago_fijo">Pago fijo capital</label>
            <input readonly type="number" step="0.01" name="pago_fijo" id="pago_fijo" class="form-control" required>
        </div>

        <div class="form-group mb-3 col-2">
            <label for="fecha aprobacion">Fecha de aprobación</label>
            <input type="date" name="fecha_aprobacion" id="fecha_aprobacion" class="form-control" required>
        </div>

        <div class="form-group mb-3 col-2">
            <label for="saldo">Saldo actual</label>
            <input readonly type="number" name="saldo" id="saldo" class="form-control" required>
        </div>

        <div class="form-group mb-3 col-2">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" class="form-control" required>
                <option>Solicitado</option>
                <option>Aprobado</option>
                <option>Concluido</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col"></div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</form>

<script>
(function(){
    let inpmonto=document.getElementById("monto");
    let inpplazo=document.getElementById("plazo");
    let inppagofijocap=document.getElementById("pago_fijo");
    let inpsaldoactual=document.getElementById("saldo");
    let inpfechainidesc=document.getElementById("fecha_inicio_descuento");
    let inpfechafindesc=document.getElementById("fecha_fin_descuento");
    inpmonto.addEventListener("input",function(){
        inpsaldoactual.value=inpmonto.value;
    })
    function calculopagofijocapital(){
        if (!inpmonto.value || !inpplazo.value) {
            return
        }
        inppagofijocap.value=inpmonto.value/inpplazo.value;
    }
    inpmonto.addEventListener("input",calculopagofijocapital);
    inpplazo.addEventListener("input",calculopagofijocapital);
    function calculofechafinplazo(){
        if (!inpplazo.value || !inpfechainidesc.value) {
            return
        }
        let fechainicio=new Date(inpfechainidesc.value);
        let meses=parseInt(inpplazo.value);
        let fechafin=new Date(fechainicio);
        fechafin.setMonth(fechainicio.getMonth()+meses-1);
        inpfechafindesc.value=fechafin.toISOString().slice(0,10);
    }
    inpplazo.addEventListener("input",calculofechafinplazo);
    inpfechainidesc.addEventListener("input",calculofechafinplazo);
})()
</script>

@endsection
