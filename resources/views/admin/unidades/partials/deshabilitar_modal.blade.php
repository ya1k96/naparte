<form class="modal-part" id="modal-deshabilitar-part">
    <p>Ingrese la fecha a desactivar de la unidad:</p>
    <div class="form-group row">
        <label for="deshabilitar" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha<code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="date" name="deshabilitar" id="deshabilitar" class="form-control{{ $errors->has('deshabilitar') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('deshabilitar') }}
            </div>
        </div>
    </div>
</form>
