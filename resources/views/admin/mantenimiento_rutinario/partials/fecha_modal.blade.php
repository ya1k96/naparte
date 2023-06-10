<form class="modal-part" id="modal-fecha-part">
    <div class="form-group row">
        <label for="fecha" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha<code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="date" name="fecha" class="form-control{{ $errors->has('fecha') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('fecha') }}
            </div>
        </div>
    </div>
</form>
