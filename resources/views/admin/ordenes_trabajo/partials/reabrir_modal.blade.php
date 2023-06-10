<form class="modal-part" name="reabrirOT" id="modal-reabrir-part" method="POST">
    @csrf
    <div class="form-group row">
        <label for="fecha" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha<code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="datetime-local" readonly name="fecha" id="fecha" value="{{ Carbon\Carbon::now()->format('Y-m-d')."T".Carbon\Carbon::now()->format('H:i') }}" class="form-control{{ $errors->has('fecha') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('fecha') }}
            </div>
        </div>
        <label for="observaciones" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observaciones<code>*</code></label>
        <div class="col-sm-12 col-md-7 mt-3">
            <textarea name="observaciones" id="observaciones" required style="height: 75px" class="form-control{{ $errors->has('observaciones') ? ' is-invalid' : '' }}"></textarea>
            <div class="invalid-feedback">
                {{ $errors->first('observaciones') }}
            </div>
        </div>
        <div class="col-sm-12 col-md-7 offset-md-3 mt-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" name="reabrir_vale" id="reabrir_vale">
                <label class="form-check-label" for="reabrir_vale">
                    Reabrir vale
                </label>
            </div>
        </div>
    </div>
</form>
