<form class="modal-part" id="modal-continuar-part">
    <div class="form-group row">
        <label for="categoria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha<code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="date" name="continuar" id="continuar" class="form-control{{ $errors->has('continuar') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('continuar') }}
            </div>
        </div>
    </div>
</form>
