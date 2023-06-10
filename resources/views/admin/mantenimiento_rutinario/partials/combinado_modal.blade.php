<form class="modal-part" id="modal-combinado-part">
    <div class="form-group row">
        <label for="fecha_combinado" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha<code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="date" name="fecha_combinado" class="form-control{{ $errors->has('fecha_combinado') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('fecha_combinado') }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="kilometros_combinado" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Kilómetros</label>
        <div class="col-sm-12 col-md-7">
            <input type="number" name="kilometros_combinado" class="form-control {{ $errors->has('kilometros_combinado') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('kilometros_combinado') }}
            </div>
        </div>
    </div>
</form>
