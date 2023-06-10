<form class="modal-part" id="modal-modelo-part">
    <div class="form-group row mb-4">
        <label for="modelo" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre <code>*</code></label>
        <div class="col-sm-12 col-md-7">
            <input type="text" name="modelo" class="form-control{{ $errors->has('modelo') ? ' is-invalid' : '' }}" autocomplete="off">
            <div class="invalid-feedback">
                {{ $errors->first('nombre') }}
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="marca" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Marca*</label>
        <div class="col-sm-12 col-md-7">
            <select name="marca" id="model_marca_id" class="form-control select2 {{ $errors->has('marca') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una marca" autocomplete="off">
                <option label="Selecciona una marca" value="">Selecciona una marca</option>
                @foreach ($marcas as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">
                {{ $errors->first('marca') }}
            </div>
        </div>
    </div>
</form>
