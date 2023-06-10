<form class="modal-part" id="modal-aprobar-oc" action="{{ route('admin.orden-compra.aprobar') }}" method="POST">
    @csrf
    <div class="form-group row">
        <div class="col-sm-12">
            <input type="hidden" name="id">
            <label for="observacion_aprobar" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observación</label>
            <textarea name="observacion_aprobar" class="form-control" style="height:115px; width:400px;"></textarea>
        </div>
    </div>
</form>
