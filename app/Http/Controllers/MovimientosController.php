<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleados;
use App\Models\Puestos;
use App\Models\Prestamo;
use Datetime;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Abonos;

class MovimientosController extends Controller
{
    public function prestamosGet(): View
    {
        $prestamos = Prestamo::join("empleados","prestamo.fk_id_empleado","=","empleados.id_empleado")->get();
        return view("movimientos/prestamosGet", [
            "prestamos" => $prestamos,
            "breadcrumbs" => [
                "Inicio" => url("/"),
                "Prestamos" => url('/movimientos/prestamos')
            ]
        ]);
    }

    public function prestamosAgregarGet(): View
    {
        $haceunanno = (new DateTime("-1 year"))->format("Y-m-d");
        $empleados = Empleados::where("fecha_ingreso","<",$haceunanno)->get()->all();
        $fecha_actual = SupportCarbon::now();
        $prestamosvigentes = Prestamo::where("fecha_ini_desc","<=",$fecha_actual)->where("fecha_fin_desc",">=",$fecha_actual)->get()->all();
        $empleados = array_column($empleados, null,"id_empleado");
        $prestamosvigentes = array_column($prestamosvigentes, null,"id_empleado");
        $empleados=array_diff_key($empleados,$prestamosvigentes);
        return view("movimientos/prestamosAgregarGet", [
            "empleados" => $empleados,
            "breadcrumbs" => [
                "Inicio" => url("/"),
                "Prestamos" => url('/movimientos/prestamos'),
                "Agregar" => url('/movimientos/prestamos/agregar')
            ]
        ]);
    }

    public function prestamosAgregarPost(Request $request)
    {
        $id_empleado=$request->input("id_empleado");
        $monto=$request->input("monto");
        $puesto=Puestos::join("det_emp_puesto", "puestos.id_puesto", "=", "det_emp_puesto.fk_id_puesto")
            ->where("det_emp_puesto.fk_id_empleado","=",$id_empleado)
            ->whereNull("det_emp_puesto.fecha_fin")->first();
        $sueldox6=$puesto->sueldo*6;
        if ($monto>$sueldox6){
            return view("/error",["error"=>"La solicitud excede el monto permitido"]);
        }
        $fecha_solicitud=$request->input("fecha_solicitud");
        $plazo=$request->input("plazo");
        $fecha_aprobacion=$request->input("fecha_aprobacion");
        $tasa_mensual=$request->input("tasa_mensual");
        $pago_fijo=$request->input("pago_fijo");
        $fecha_inicio_descuento=$request->input("fecha_inicio_descuento");
        $fecha_fin_descuento=$request->input("fecha_fin_descuento");
        $saldo=$request->input("saldo");
        $estado=$request->input("estado");
        $prestamo=new Prestamo([
            "fk_id_empleado"=>$id_empleado,
            "fecha_solicitud"=>$fecha_solicitud,
            "monto"=>$monto,
            "plazo"=>$plazo,
            "fecha_aprob"=>$fecha_aprobacion,
            "tasa_mensual"=>$tasa_mensual,
            "pago_fijo_cap"=>$pago_fijo,
            "fecha_ini_desc"=>$fecha_inicio_descuento,
            "fecha_fin_desc"=>$fecha_fin_descuento,
            "saldo_actual"=>$saldo,
            "estado"=>$estado,
        ]);
        $prestamo->save();
        return redirect("/movimientos/prestamos"); // redirige al listado de prestamos
    }


    public function abonosGet($id_prestamo): View
    {
    $abonos = Abonos::where("fk_id_prestamo", $id_prestamo)->get()->all();
    $prestamo = Prestamo::join("empleados", "empleados.id_empleado", "=", "prestamo.fk_id_empleado")
        ->where("id_prestamo", $id_prestamo)->first();

    return view('movimientos/abonosGet', [
        'abonos' => $abonos,
        'prestamo' => $prestamo,
        "breadcrumbs" => [
            "Inicio" => url("/"),
            "Prestamos" => url("/movimientos/prestamos"),
            "Abonos" => url("/movimientos/prestamos/abonos"),
        ]
    ]);
    }

    public function abonosAgregarGet($id_prestamo): View
    {
        $prestamo = Prestamo::join("empleados", "empleados.id_empleado", "=", "prestamo.fk_id_empleado")
            ->where("id_prestamo", $id_prestamo)->first();
    
        $abonos = Abonos::where("abonos.fk_id_prestamo", $id_prestamo)->get();
        $num_abono = count($abonos) + 1;
    
        // Obtener el último abono registrado
        $ultimo_abono = Abonos::where("abonos.fk_id_prestamo", $id_prestamo)
            ->orderBy("fecha", "desc")
            ->first();
    
        // Si hay un abono previo, tomamos su saldo actual, si no, usamos el saldo del préstamo
        $saldo_actual = $ultimo_abono ? $ultimo_abono->saldo_actual : $prestamo->saldo_actual;
        
        // Cálculo basado en el saldo actual correcto
        $monto_interes = $saldo_actual * ($prestamo->tasa_mensual / 100);
        $monto_cobrado = $prestamo->pago_fijo_cap + $monto_interes;
        $saldo_pendiente = $saldo_actual - $prestamo->pago_fijo_cap;
    
        if ($saldo_pendiente < 0) {
            $pago_fijo_cap = $prestamo->pago_fijo_cap + $saldo_pendiente;
            $saldo_pendiente = 0;
        } else {
            $pago_fijo_cap = $prestamo->pago_fijo_cap;
        }
    
        return view('movimientos/abonosAgregarGet', [
            'prestamo' => $prestamo,
            'num_abono' => $num_abono,
            'pago_fijo_cap' => $pago_fijo_cap,
            'monto_interes' => $monto_interes,
            'monto_cobrado' => $monto_cobrado,
            'saldo_pendiente' => $saldo_pendiente,
            'breadcrumbs' => [
                "Inicio" => url("/"),
                "Prestamos" => url("/movimientos/prestamos"),
                "Abonos" => url("/prestamos/{$prestamo->id_prestamo}/abonos"),
                "Agregar" => "",
            ]
        ]);
    }
}