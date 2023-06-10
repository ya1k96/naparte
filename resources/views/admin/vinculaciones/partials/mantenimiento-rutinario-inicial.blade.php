<form action="{{ route('admin.vinculaciones.store') }}" method="post" class="modal-part" id="mantenimiento-rutinario-inicial" style="overflow-y: scroll; height: 400px; overflow-x: hidden;">
    @csrf
    <input type="hidden" name="modifica" value="si">
    <div class="form-group row">        
        <div class="col-sm-12" id="container-mantenimiento-rutinario-inicial">
        
            
        </div>
    </div>
</form>