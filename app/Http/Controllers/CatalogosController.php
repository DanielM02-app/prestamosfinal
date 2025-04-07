<?php

namespace App\Http\Controllers;
use App\Models\Puestos;
use App\Models\Empleados;
use App\Models\Det_Emp_Puesto;
use App\Models\Prestamo;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogosController extends Controller
{
    public function home():View
    {
        return view('home',["breadcrumbs"=>[]]);
    }

    public function puestosGet(): View
    {
        $puestos = Puestos::all();
        return view('catalogos/puestosGet', [
            'puestos' => $puestos,
            "breadcrumbs"=>[
                "Inicio"=>url("/"),
                "Puestos"=>url("/catalogos/puestos")
            ]
            ]);
    }

    public function puestosAgregarGet(): View
    {
        return view('catalogos/puestosAgregarGet', [
            "breadcrumbs"=>[
                "Inicio"=>url("/"),
                "Puestos"=>url("/catalogos/puestos"),
                "Agregar"=>url("/catalogos/puestos/agregar")
            ]
            ]);
    }

    public function puestosAgregarPost(Request $request)
    {
        $nombre=$request->input("nombre");
        $sueldo=$request->input("sueldo");
        $puestos=new Puestos([
            "nombre"=>strtoupper($nombre),
            "sueldo"=>$sueldo
        ]);
        $puestos->save();
        return redirect("/catalogos/puestos"); // redirige al listado de puestos
    }

    public function empleadosGet(): View
    {
        $empleados = Empleados::all();
        return view('catalogos/empleadosGet', [
            'empleados' =>$empleados,
            "breadcrumbs"=>[
                "Inicio"=>url("/"),
                "Empleados"=>url("/catalogos/empleados")
            ]
            ]);
    }

    public function empleadosAgregarGet(): View
    {
        $puestos=Puestos::all();
        return view('catalogos/empleadosAgregarGet', [
            "puestos"=>$puestos,
            "breadcrumbs"=>[
                "Inicio"=>url("/"),
                "Empleados"=>url("/empleados"),
                "Agregar"=>url("/empleados/agregar")
            ]
            ]);
    }

    public function empleadosAgregarPost(Request $request)
    {
        $nombre=$request->input("nombre");
        $fecha_ingreso=$request->input("fecha_ingreso");
        $activo=$request->input("activo");
        $empleado=new Empleados([
            "nombre"=>strtoupper($nombre),
            "fecha_ingreso"=>$fecha_ingreso,
            "activo"=>$activo
        ]);
        $empleado->save();
        $puestos=new Det_Emp_Puesto([
            "fk_id_empleado" => $empleado->id_empleado,
            "fk_id_puesto" =>$request->input("puesto"),
            "fecha_inicio" => $fecha_ingreso
        ]);
        $puestos->save();
        return redirect("/empleados");
    }

    public function empleadosPuestosGet(Request $request, $id_empleado)
    {
        $puestos = Det_Emp_Puesto::join("puestos", "puestos.id_puesto", "=", "det_emp_puesto.fk_id_puesto")
        ->select("det_emp_puesto.*", "puestos.nombre as puesto", "puestos.sueldo")
        ->where("det_emp_puesto.fk_id_empleado", "=", $id_empleado)
        ->get();
    
        $empleado=Empleados::find($id_empleado);
        return view("catalogos/empleadosPuestosGet",[
            "puestos"=>$puestos, "empleado"=>$empleado,
            "breadcrumbs"=>[
                "Inicio"=>url("/"),
                "Empleados"=>url("/empleados"),
                "Puestos"=>url("/empleados/{id}/puestos")
            ]
            ]);
    }
    public function empleadosPuestosCambiarGet($id)
    {
        $empleado = Empleados::where("id_empleado", "=", $id)->first();
        $puestos = Puestos::all();
    
        return view("catalogos/empleadosPuestosCambiarGet", [
            "empleado" => $empleado,
            "puestos" => $puestos,
            "breadcrumbs" => [
                "Inicio" => url("/"),
                "Empleados" => url("/empleados"),
                "Puestos" => url("/empleados/{$id}/puestos"),
                "Cambiar" => url("/empleados/{$id}/puestos/cambiar")
            ]
        ]);
    }

    public function empleadosPuestosCambiarPost(Request $request, $id_empleado)
    {
        $fecha_inicio = $request->input("fecha_inicio");
        $fecha_fin = (new DateTime($fecha_inicio))->modify("-1 day");
    
        /* Buscar todos los puestos del empleado cuya fecha fin sean NULL
           para dar el dato preciso de la fecha de término, que sería menos un día de cuando inicia
           el nuevo puesto.
        */
        $anterior = Det_Emp_Puesto::where("fk_id_empleado", $id_empleado)
            ->whereNull("fecha_fin")
            ->update(["fecha_fin" => $fecha_fin->format("Y-m-d")]);
    
        /** Agregar el nuevo puesto */
        $puesto = new Det_Emp_Puesto([
            "fk_id_empleado" => $id_empleado,
            "fk_id_puesto" => $request->input("puesto"), /** viene el dato del formulario */
            "fecha_inicio" => $fecha_inicio,
        ]);
        $puesto->save();
        return redirect("/empleados/{$id_empleado}/puestos");
    }
}
