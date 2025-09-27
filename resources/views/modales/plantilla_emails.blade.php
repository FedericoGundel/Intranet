<!-- Modal: Agregar Plantilla -->
<div class="modal fade" id="addPlantillaEmail" tabindex="-1" aria-labelledby="agregarPlantillaEmailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formAddPlantillaEmail">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Plantilla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label p-0 col-form-label-sm">Nombre</label>
                        <input type="text" class="form-control form-control-sm " name="nombre" id=""
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="asunto" class="form-label p-0 col-form-label-sm">Asunto</label>
                        <input type="text" class="form-control form-control-sm " name="asunto" id=""
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="cuerpo" class="form-label p-0 col-form-label-sm">Cuerpo</label>
                        <textarea class="form-control form-control-sm  summernote" name="cuerpo" id="cuerpo_plantilla_email" rows="6"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal: Editar Plantilla -->
<div class="modal fade" id="editPlantillaEmail" tabindex="-1" aria-labelledby="editarPlantillaEmailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditPlantillaEmail">
            <input type="hidden" id="edit_id_plantilla_email" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Plantilla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre_editar" class="form-label p-0 col-form-label-sm">Nombre</label>
                        <input type="text" class="form-control form-control-sm " name="nombre" id=""
                            value="" required>
                    </div>
                    <div class="mb-3">
                        <label for="asunto_editar" class="form-label p-0 col-form-label-sm">Asunto</label>
                        <input type="text" class="form-control form-control-sm " name="asunto" id=""
                            value="" required>
                    </div>
                    <div class="mb-3">
                        <label for="cuerpo_editar" class="form-label p-0 col-form-label-sm">Cuerpo</label>
                        <textarea class="form-control form-control-sm " name="cuerpo" id="cuerpo_plantilla_email_editar" rows="6"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>
