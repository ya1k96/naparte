<form class="modal-part" name="anularOT" id="modal-anular-part" method="POST">
    @csrf
    <p>Se anulará esta orden de compra. ¿Continuar?</p>
    <div class="form-group row">
        <label for="fecha" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha<code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="datetime-local" readonly name="fecha" id="fecha" value="{{ Carbon\Carbon::now()->format('Y-m-d')."T".Carbon\Carbon::now()->format('H:i') }}" class="form-control{{ $errors->has('fecha') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('fecha') }}
            </div>
        </div>
        <label for="comentario" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Comentario<code>*</code></label>
        <div class="col-sm-12 col-md-7 mt-3">
            <textarea name="comentario" id="comentario" required style="height: 75px" class="form-control{{ $errors->has('comentario') ? ' is-invalid' : '' }}"></textarea>
            <div class="invalid-feedback">
                {{ $errors->first('comentario') }}
            </div>
        </div>
    </div>
</form>