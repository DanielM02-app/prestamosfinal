<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puestos extends Model
{
    use HasFactory;
    protected $table='puestos';
    protected $primarykey='id_puesto';
    public $incrementing=true;
    protected $keytype='int';
    protected $nombre;
    protected $sueldo;
    protected $fillable=["nombre","sueldo"];
    public $timestamps=false;
}
