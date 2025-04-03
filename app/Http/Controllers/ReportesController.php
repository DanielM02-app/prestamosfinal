<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Prestamo;
use DateTime;
use Francerz\PowerData\Index;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportesController extends Controller
{
    public function indexGet(Request $request)
    {
        return view("reportes.indexGet", [
            "breadcrumbs" => [
                "Inicio" => url("/"),
                "Reportes" => url("/reportes/prestamos-activos")
            ]
        ]);
    }

    public function prestamosActivosGet(Request $request)
{
    $fecha = Carbon::now()->format("Y-m-d"); // Carbon Fecha actual en formato de texto
    $fecha = $request->query("fecha", $fecha);
    $prestamos = Prestamo::join("empleados", "empleados.id_empleado", "=", "prestamos.id_empleado")
        ->leftJoin("abonos", "abonos.id_prestamo", "=", "prestamos.id_prestamo")
        ->select("prestamos.id_prestamo", "empleados.nombre", "prestamos.monto")
        ->selectRaw("SUM(abonos.monto_capital) AS total_capital")
        ->selectRaw("SUM(abonos.monto_interes) AS total_interes")
        ->selectRaw("SUM(abonos.monto_cobrado) AS total_cobrado")
        ->groupBy("prestamos.id_prestamo", "empleados.nombre", "prestamos.monto")
        ->where("prestamos.fecha_inicio_descuento", "<=", $fecha)
        ->where("prestamos.fecha_fin_descuento", ">=", $fecha)
        ->get()->all();

    // var_dump($prestamos);
    
    return view("/reportes/prestamosActivosGet", [
        "fecha" => $fecha,
        "prestamos" => $prestamos,
        "breadcrumbs" => [
            "Inicio" => url("/"),
            "Reportes" => url("/reportes/prestamos-activos"),
        ]
    ]);
}

public function matrizAbonosGet(Request $request)
{
    $fecha_inicio = Carbon::now()->format("Y-01-01"); // Carbon Fecha actual en formato de texto
    $fecha_inicio = $request->query("fecha_inicio", $fecha_inicio);
    $fecha_fin = Carbon::now()->format("Y-12-31"); // Carbon Fecha actual en formato de texto
    $fecha_fin = $request->query("fecha_fin", $fecha_fin);
    
    $queryAbono = Abono::join("prestamos", "prestamos.id_prestamo", "=", "abonos.id_prestamo")
        ->join("empleados", "empleados.id_empleado", "=", "prestamos.id_empleado")
        ->select("prestamos.id_prestamo", "empleados.nombre", "abonos.monto_cobrado", "abonos.fecha")
        ->orderBy("abonos.fecha");
    
    $queryAbono->where("abonos.fecha", ">=", $fecha_inicio);
    $queryAbono->where("abonos.fecha", "<=", $fecha_fin);
    
    $abonos = $queryAbono->get()->toArray();
    
    foreach ($abonos as &$abonos) {
        $abonos["fecha"] = (new DateTime($abonos["fecha"]))->format("Y-m");
    }
    
    // var_dump($abonos);
    
    $abonosIndex = new Index($abonos, ["id_prestamo", "fecha"]); // soportado por el complemento power-data
    
    return view("reportes.matrizAbonosGet", [
        "abonosIndex" => $abonosIndex,
        "fecha_inicio" => $fecha_inicio,
        "fecha_fin" => $fecha_fin,
        "breadcrumbs" => []
    ]);
}
}