<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use SoftDeletes;

    protected $table = "personal";

    protected $fillable = [
      'nombre',
      'especialidad_id'
    ];

    protected $hidden = [
      'show_orden_trabajo', 
    ];

    public function especialidad() {
      return $this->belongsTo(
        Especialidad::class
      );
    }

    public function tareas() {
      return $this->belongsToMany(Tarea::class);
    }

    public function getShowOrdenTrabajoAttribute() {
      $data = [];
      $arrOT = OrdenesTrabajo::All();
      foreach ($arrOT as $key => $value) {
        $arrPersonal = json_decode($value->personal);
        if($arrPersonal) {
          if(in_array($this->id, $arrPersonal)) {
            $data[] = $value;
          }
        }
      }
      return $data;      
    }    
}
