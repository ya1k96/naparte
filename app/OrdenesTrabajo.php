<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenesTrabajo extends Model
{
    use SoftDeletes;
    
    protected $table = "ordenes_trabajo";

    public static $status_ordenes = [
        'Abierta' => 'Abierta',
        'Cerrada' => 'Cerrada',
        'Anulada' => 'Anulada',
    ];

    public static $tipos_ordenes = [
        'Correctiva' => 'Correctiva',
        'Preventiva' => 'Preventiva',
    ];

    public static $dias = [
        '1' => '1 día',
        '5' => '5 días',
        '7' => '7 días',
        '10' => '10 días',
        '15' => '15 días',
        '30' => '30 días',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'unidad_id',
        'tarea_a_realizar',
        'personal_id',
        'personal', //PROVISORIO HASTA TENER ABM PERSONAL
        'especialidad_id',
        'tipo_orden',
        'base_operacion_id',
        'comentario_mecanico',
        'fecha_hora_recepcion',
        'fecha_hora_devolucion',
        'hora_inicio_trabajo',
        'fecha_hora_inicio',
        'hora_fin_trabajo',
        'fecha_hora_fin',
        'kilometraje',
        'status',
        'revisado_por',
        'observaciones',
        'fecha_cierre',
        'usuario_reabierta_id',
        'fecha_hora_reabierta',
        'fecha_hora_anulada',
        'usuario_anulada_id',
        'url',
        'impresa',
        'numeracion',
        'fecha_inicio_periodo',
        'fecha_fin_periodo'
    ];

    protected $appends = [
      'show_personal', 
    ];

    public function unidad() {
        return $this->belongsTo(
          Unidad::class
        );
    }
    public function user() {
        return $this->belongsTo(
            User::class
        );
    }
    public function base_operacion() {
        return $this->belongsTo(
            BaseOperacion::class
        );
    }
    public function historiales() {
        return $this->hasMany(
            HistorialOrdenesTrabajo::class
        );
    }
    public function tareas() {
        return $this->belongsToMany(Tarea::class)->withPivot('comentario', 'fecha_realizacion', 'fecha_estimada');
    }
    public function especialidad() {
        return $this->belongsToMany(
            Especialidad::class,
            'ordenes_trabajo_especialidad'
        );
    }

    public function mantenimiento() {
        return $this->hasOne(
            MantenimientoRutinario::class
        );
    }

    public function vale() {
        return $this->hasOne(
            Vale::class
        );
    }

    public function movimiento() {
        return $this->hasOne(Movimiento::class, 'orden_trabajo_id');
    }

    public function getShowPersonalAttribute() {
      $data = [];      
      $arrPersonal =  json_decode($this->personal);
      if($arrPersonal) {
        foreach ($arrPersonal as $key => $value) {
            $data[] = Personal::find($value);
        }      
      }
      return $data;
    }
}
