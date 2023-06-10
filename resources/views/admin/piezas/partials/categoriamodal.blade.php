<form class="modal-part" id="modal-categoria-part">
    <div class="form-group row">
        <label for="categoria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre <code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="text" name="categoria" id="categoria" class="form-control{{ $errors->has('categoria') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('categoria') }}
            </div>
        </div>
    </div>
</form>
