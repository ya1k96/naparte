<form class="modal-part" id="modal-especialidad-part">
    <div class="form-group row">
        <label for="nombre" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre <code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="text" name="nombre" class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('nombre') }}
            </div>
        </div>
    </div>
</form>
