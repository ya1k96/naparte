<form class="modal-part" id="modal-cerrar-ot" action="{{ route('admin.orden-transferencia.cerrar') }}" method="POST">
    @csrf
    <div class="form-group row">
        <div class="col-sm-12">
            <input type="hidden" name="id">
            <label for="observacion_cerrar" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observación</label>
            <textarea name="observacion_cerrar" class="form-control" style="height:115px; width:400px;"></textarea>
        </div>
    </div>
</form>
