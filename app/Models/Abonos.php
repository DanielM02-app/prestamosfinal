<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonos extends Model
{
    use HasFactory;
    protected $table='abonos';
    protected $primarykey='id_abono';
    public $incrementing=true;
    protected $keytype='int';
    protected $num_abono;
    protected $fk_id_prestamo;
    protected $fecha;
    protected $monto_capital;
    protected $monto_interes;
    protected $monto_cobrado;
    protected $saldo_pendiente;
    protected $fillable=["num_abono","fk_id_prestamo", "fecha", "monto_capital", "monto_interes", "monto_cobrado", 
                        "saldo_pendiente"];
    public $timestamps=false;
}