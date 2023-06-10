<form class="modal-part" id="modal-habilitar-part">
    <p>Está por habilitar una unidad antes de la fecha establecida. ¿Cuál es el kilometraje de inicio?</p>
    <div class="form-group row">
        <label for="habilitar" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Kilometraje <code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="number" name="habilitar" id="habilitar" class="form-control{{ $errors->has('habilitar') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('habilitar') }}
            </div>
        </div>
    </div>
</form>
