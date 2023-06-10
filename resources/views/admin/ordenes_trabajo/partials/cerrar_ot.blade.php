<form class="modal-part" name="CerrarOT" id="cerrar-kilometraje-part" method="POST">
    @csrf
    @method('DELETE')
    <input type="hidden" name="unidad_id" id="unidad_id" class="form-control" value="" autocomplete="off">
    <input type="hidden" name="id" id="id" class="form-control" value="" autocomplete="off">
    <input type="hidden" name="status" class="form-control" value="Cerrada" autocomplete="off">
    <div class="form-group row km-1">
        <label for="kilometraje_1" id="kilometraje_1_label" class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
        <div class="col-sm-7 col-md-7">
            <input type="hidden" id="historial-1-id" name="historial_1_id">
            <input type="hidden" id="kilometraje-1-valor-comparar" name="kilometraje_1_valor_comparar">
            <input type="hidden" id="kilometraje-1-estado-modal" class="estados_modal" name="kilometraje_1_estado_modal" value="">
            <input type="number" name="kilometraje_1" id="kilometraje_1" class="form-control{{ $errors->has('kilometraje_1') ? ' is-invalid' : '' }}" value="" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('kilometraje_1') }}
            </div>
            <div class="kilometraje_1_fecha"></div>
            <a style="display: none;" href="#" id="historial-modal-warning-1" class="btn btn-icon btn-sm btn-warning" title="Supera el 30% del promedio"><i class="fas fa-exclamation-triangle"></i></a>
            <a style="display: none;" href="#" id="historial-modal-mark-1" class="btn btn-icon btn-sm btn-light">Marcar correcto</a>
            <a style="display: none;" href="#" id="historial-modal-check-1" class="btn btn-icon btn-sm btn-success"><i class="fas fa-check"></i></a>
            <a style="display: none;" href="#" id="historial-modal-danger-1" class="btn btn-icon btn-sm btn-danger" title="El kilometraje no puede ser menor al último ingresado"><i class="fas fa-times"></i></a>
            <div class="form-check carga-preventivas">
                <input class="form-check-input" name="cargar_correctivas" type="checkbox" value="" id="cargar_correctivas">
                <label class="form-check-label" for="cargar_correctivas">
                    Cargar tareas preventivas realizadas
                </label>
            </div>
        </div>
    </div>
</form>

