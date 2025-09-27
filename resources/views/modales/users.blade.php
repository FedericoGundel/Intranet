<div class="modal fade" id="cambiarFondo" tabindex="-1" aria-labelledby="addArticuloLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addArticuloLabel">Cambiar fondo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formCambiarFondo" action="" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" id="edit_id_user">
                      <input type="hidden" name="tipo" value="fondo" >
                    <div class="row g-2">
                      
                        <!-- Columna 3: Imagen -->
                        <div class="col-12">


                            <div class="d-grid">
                                <label for="imagen" class="form-label p-0 col-form-label-sm">Imagen:</label>
                                <div style="height: 142px;" id="preview_fondo_perfil" class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-2"></div>

                                <input type="file" name="imagen" class="input-imagen" id="imagen" data-preview-target="preview_fondo_perfil" hidden accept="image/*">

                                <div class="d-flex gap-2 mt-2">
                                    <label class="btn-upload btn btn-sm btn-primary mb-0" for="imagen">Subir nueva imagen</label>
                                    <button type="button" class="btn btn-sm btn-danger mb-0 btn-eliminar-imagen">Eliminar imagen</button>
                                </div>

                                <input type="hidden" name="eliminar_imagen" id="eliminar_imagen" value="0">
                            </div>
                        </div>
                    </div> <!-- .row -->
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formCambiarFondo" class="btn btn-sm btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="cambiarFoto" tabindex="-1" aria-labelledby="addArticuloLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addArticuloLabel">Cambiar foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formCambiarFoto" action="" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="id" id="edit_id_user_foto">
                                            <input type="hidden" name="tipo" value="foto" >
                    <div class="row g-2">
                      
                        <!-- Columna 3: Imagen -->
                        <div class="col-12">


                            <div class="d-grid">
                                <label for="imagen" class="form-label p-0 col-form-label-sm">Imagen:</label>
                                <div style="height: 142px;" id="preview_foto_perfil" class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-2"></div>

                                <input type="file" name="imagen" class="input-imagen" id="imagen_fondo" data-preview-target="preview_foto_perfil" hidden accept="image/*">

                                <div class="d-flex gap-2 mt-2">
                                    <label class="btn-upload btn btn-sm btn-primary mb-0" for="imagen_fondo">Subir nueva imagen</label>
                                    <button type="button" class="btn btn-sm btn-danger mb-0 btn-eliminar-imagen">Eliminar imagen</button>
                                </div>

                                <input type="hidden" name="eliminar_imagen" id="eliminar_imagen" value="0">
                            </div>
                        </div>
                    </div> <!-- .row -->
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formCambiarFoto" class="btn btn-sm btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>