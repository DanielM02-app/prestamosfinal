<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Abonos;
use App\Models\Prestamo;
use DateTime;
use Francerz\PowerData\Index;
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
        $fecha = Carbon::now()->format("Y-m-d"); // Fecha actual en formato de texto
        $fecha = $request->query("fecha", $fecha);

        $prestamos = Prestamo::join('empleados', 'empleados.id_empleado', '=', 'prestamo.fk_id_empleado')
        ->leftJoin('abonos', 'abonos.fk_id_prestamo', '=', 'prestamo.id_prestamo')
        ->select('prestamo.id_prestamo', 'empleados.nombre', 'prestamo.monto')
        ->selectRaw('SUM(abonos.monto_capital) AS total_capital')
        ->selectRaw('SUM(abonos.monto_interes) AS total_interes')
        ->selectRaw('SUM(abonos.monto_cobrado) AS total_cobrado')
        ->groupBy('prestamo.id_prestamo', 'empleados.nombre', 'prestamo.monto')
        ->where('prestamo.fecha_ini_desc', '<=', $fecha)
        ->where('prestamo.fecha_fin_desc', '>=', $fecha)
        ->get();
    

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
        $fecha_inicio = Carbon::now()->format("Y-01-01"); // *Carbon Fecha actual en formato de texto
        $fecha_inicio = $request->query("fecha_inicio", $fecha_inicio);
        $fecha_fin = Carbon::now()->format("Y-12-31"); // *Carbon Fecha actual en formato de texto
        $fecha_fin = $request->query("fecha_fin", $fecha_fin);

        $query = Abonos::join("prestamo", "prestamo.id_prestamo", "=", "abonos.fk_id_prestamo")
            ->join("empleados", "empleados.id_empleado", "=", "prestamo.fk_id_empleado")
            ->select("prestamo.id_prestamo", "empleados.nombre", "abonos.monto_cobrado", "abonos.fecha")
            ->orderBy("abonos.fecha");

        $query->where("abonos.fecha", ">=", $fecha_inicio);
        $query->where("abonos.fecha", "<=", $fecha_fin);
        
        $abonos = $query->get()->toArray();

        foreach($abonos as &$abono)
        {
            $abono["fecha"] = (new DateTime($abono["fecha"]))->format("Y-m");
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
