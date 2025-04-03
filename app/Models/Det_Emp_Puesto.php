<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Det_Emp_Puesto extends Model
{
    use HasFactory;
    protected $table='det_emp_puesto';
    protected $primarykey='id_det_emp_puesto';
    public $incrementing=true;
    protected $keytype='int';
    protected $fk_id_empleado;
    protected $fk_id_puesto;
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $fillable=["fk_id_empleado","fk_id_puesto", "fecha_inicio", "fecha_fin"];
    public $timestamps=false;
}
