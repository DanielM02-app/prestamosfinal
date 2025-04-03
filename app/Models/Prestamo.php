<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;
    protected $table='prestamo';
    protected $primarykey='id_prestamo';
    public $incrementing=true;
    protected $keytype='int';
    protected $fk_id_empleado;
    protected $fecha_solicitud;
    protected $monto;
    protected $plazo;
    protected $fecha_aprob;
    protected $tasa_mensual;
    protected $pago_fijo_cap;
    protected $fecha_ini_desc;
    protected $fecha_fin_desc;
    protected $saldo_actual;
    protected $estado;
    protected $fillable=["fk_id_empleado","fecha_solicitud", "monto", "plazo", "fecha_aprob", "tasa_mensual", "pago_fijo_cap", "fecha_ini_desc", 
                        "fecha_fin_desc", "saldo_actual", "estado"];
    public $timestamps=false;
}
