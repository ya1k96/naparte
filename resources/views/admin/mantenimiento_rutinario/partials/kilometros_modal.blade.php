<form class="modal-part" id="modal-kilometros-part">
    <div class="form-group row">
        <label for="kilometros" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Kilómetros</label>
        <div class="col-sm-12 col-md-7">
            <input type="number" name="kilometros" class="form-control {{ $errors->has('kilometros') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('kilometros') }}
            </div>
        </div>
    </div>
</form>
