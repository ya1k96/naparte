<form class="modal-part" id="editar-kilometraje-part" action="{{ route('admin.historial.editarHistoriales') }}" method="POST">
    @csrf
    <input type="hidden" name="unidad_id" id="unidad_id" class="form-control" value="" autocomplete="off">
    <div class="form-group row km-anterior">
        <div class="col-sm-12 col-md-7">
            <p class="kilometraje_anterior"></p>
        </div>
    </div>
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
        </div>
    </div>
    <div class="form-group row km-2">
        <label for="kilometraje_2" id="kilometraje_2_label" class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
        <div class="col-sm-12 col-md-7">
            <input type="hidden" id="historial-2-id" name="historial_2_id">
            <input type="hidden" id="kilometraje-2-valor-comparar" name="kilometraje_2_valor_comparar">
            <input type="hidden" id="kilometraje-2-estado-modal" class="estados_modal" name="kilometraje_2_estado_modal" value="">
            <input type="number" name="kilometraje_2" id="kilometraje_2" class="form-control{{ $errors->has('kilometraje_2') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('kilometraje_2') }}
            </div>
            <div class="kilometraje_2_fecha"></div>
            <a style="display: none;" href="#" id="historial-modal-warning-2" class="btn btn-icon btn-sm btn-warning" title="Supera el 30% del promedio"><i class="fas fa-exclamation-triangle"></i></a>
            <a style="display: none;" href="#" id="historial-modal-mark-2" class="btn btn-icon btn-sm btn-light">Marcar correcto</a>
            <a style="display: none;" href="#" id="historial-modal-check-2" class="btn btn-icon btn-sm btn-success"><i class="fas fa-check"></i></a>
            <a style="display: none;" href="#" id="historial-modal-danger-2" class="btn btn-icon btn-sm btn-danger" title="El kilometraje no puede ser menor al último ingresado"><i class="fas fa-times"></i></a>
        </div>
    </div>
    <div class="form-group row km-3">
        <label for="kilometraje_3" id="kilometraje_3_label" class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
        <div class="col-sm-12 col-md-7">
            <input type="hidden" id="historial-3-id" name="historial_3_id">
            <input type="hidden" id="kilometraje-3-valor-comparar" name="kilometraje_3_valor_comparar">
            <input type="hidden" id="kilometraje-3-estado-modal" class="estados_modal" name="kilometraje_3_estado_modal" value="">
            <input type="number" name="kilometraje_3" id="kilometraje_3" class="form-control{{ $errors->has('kilometraje_3') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('kilometraje_3') }}
            </div>
            <div class="kilometraje_3_fecha"></div>
            <a style="display: none;" href="#" id="historial-modal-warning-3" class="btn btn-icon btn-sm btn-warning" title="Supera el 30% del promedio"><i class="fas fa-exclamation-triangle"></i></a>
            <a style="display: none;" href="#" id="historial-modal-mark-3" class="btn btn-icon btn-sm btn-light">Marcar correcto</a>
            <a style="display: none;" href="#" id="historial-modal-check-3" class="btn btn-icon btn-sm btn-success"><i class="fas fa-check"></i></a>
            <a style="display: none;" href="#" id="historial-modal-danger-3" class="btn btn-icon btn-sm btn-danger" title="El kilometraje no puede ser menor al último ingresado"><i class="fas fa-times"></i></a>
        </div>
    </div>
</form>

