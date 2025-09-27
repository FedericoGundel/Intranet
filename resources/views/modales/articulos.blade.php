<div class="modal fade" id="addArticulo" tabindex="-1" aria-labelledby="addArticuloLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addArticuloLabel">Agregar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>


            <div class="modal-body">
                <form id="formAddArticulo" action="" method="POST" enctype="multipart/form-data">
                    <div class="row g-2">
                        <!-- Columna 1 -->
                        <div class="col-md-4">

                            <div class="mb-2">
                                <label for="nombre" class="form-label p-0 col-form-label-sm">Nombre*</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-2">
                                <label for="stock" class="form-label p-0 col-form-label-sm">Stock*</label>
                                <input type="text" class="form-control" id="stock" name="stock" value=0
                                    required min="0">
                            </div>

                            <div class="mb-2">
                                <label for="codigo" class="form-label p-0 col-form-label-sm">Código</label>
                                <input type="text" class="form-control" id="codigo" name="codigo">
                            </div>

                            <div class="mb-2">
                                <label for="precio" class="form-label p-0 col-form-label-sm">Precio*</label>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio"
                                    required>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="descripcion" class="form-label p-0 col-form-label-sm">Descripción</label>
                                <textarea style="height: 193px;" class="form-control" id="descripcion" name="descripcion" rows="6"></textarea>
                            </div>
                            <div class="mb-2">
                                <label for="descuento" class="form-label p-0 col-form-label-sm">Descuento*</label>
                                <input type="number" step="0.01" class="form-control" id="descuento"
                                    name="descuento" required>
                            </div>
                        </div>

                        <!-- Columna 3: Imagen -->
                        <div class="col-md-4">


                            <div class="d-grid">
                                <label for="imagen" class="form-label p-0 col-form-label-sm">Imagen:</label>
                                <div style="height: 142px;" id="preview_articulo"
                                    class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-2">
                                </div>

                                <input type="file" name="imagen" class="input-imagen" id="imagen"
                                    data-preview-target="preview_articulo" hidden accept="image/*">

                                <div class="d-flex gap-2 mt-2">
                                    <label class="btn-upload btn btn-sm btn-primary mb-0" for="imagen">Subir nueva
                                        imagen</label>
                                    <button type="button"
                                        class="btn btn-sm btn-danger mb-0 btn-eliminar-imagen">Eliminar
                                        imagen</button>
                                </div>

                                <input type="hidden" name="eliminar_imagen" id="eliminar_imagen" value="0">
                            </div>
                        </div>
                    </div> <!-- .row -->
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formAddArticulo" class="btn btn-sm btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editArticulo" tabindex="-1" aria-labelledby="editArticuloLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editArticuloLabel">Editar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formEditArticulo" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_id_articulo">

                    <div class="row g-2">
                        <!-- Columna 1 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="edit_nombre" class="form-label p-0 col-form-label-sm">Nombre*</label>
                                <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                            </div>
                            <div class="mb-2">
                                <label for="stock" class="form-label p-0 col-form-label-sm">Stock*</label>
                                <input type="text" class="form-control" name="stock" value=0 required
                                    min="0">
                            </div>
                            <div class="mb-2">
                                <label for="edit_codigo" class="form-label p-0 col-form-label-sm">Código</label>
                                <input type="text" class="form-control" id="edit_codigo" name="codigo">
                            </div>

                            <div class="mb-2">
                                <label for="edit_precio" class="form-label p-0 col-form-label-sm">Precio*</label>
                                <input type="number" step="0.01" class="form-control" id="edit_precio"
                                    name="precio" required>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="edit_descripcion"
                                    class="form-label p-0 col-form-label-sm">Descripción</label>
                                <textarea class="form-control" style="height: 193px;" id="edit_descripcion" name="descripcion"></textarea>
                            </div>
                            <div class="mb-2">
                                <label for="descuento" class="form-label p-0 col-form-label-sm">Descuento*</label>
                                <input type="number" step="0.01" class="form-control" id="descuento"
                                    name="descuento" required>
                            </div>
                        </div>

                        <!-- Columna 3: Imagen -->
                        <div class="col-md-4">
                            <div class="d-grid">
                                <label for="edit_imagen" class="form-label p-0 col-form-label-sm">Imagen
                                    actual:</label>
                                <div style="height: 142px;" id="preview_articulo_edit"
                                    class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-3">
                                </div>

                                <input type="file" name="imagen" class="input-imagen" id="edit_imagen"
                                    data-preview-target="preview_articulo_edit" hidden accept="image/*">

                                <div class="d-flex gap-2 mt-2">
                                    <label class="btn-upload btn btn-sm btn-primary mb-0" for="edit_imagen">Subir
                                        nueva
                                        imagen</label>
                                    <button type="button"
                                        class="btn btn-sm btn-danger mb-0 btn-eliminar-imagen">Eliminar
                                        imagen</button>
                                </div>

                                <input type="hidden" name="eliminar_imagen" id="eliminar_imagen" value="0">
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formEditArticulo" class="btn btn-sm btn-success">Actualizar</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="verIngresosArticulo" tabindex="-1" aria-labelledby="agregarPlantillaEmailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">


        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ingresos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 text-end">
                    <button type="button" class=" btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#modalAgregarIngreso">
                        <i class="las la-plus"></i> Agregar Ingreso
                    </button>

                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="ingresos_articulo_table">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Cantidad</th>

                                <th class="text-start">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llena dinámicamente con DataTables o JS -->
                        </tbody>
                    </table>



                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>

            </div>
        </div>

    </div>
</div>





<div class="modal fade" id="modalAgregarIngreso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Agregar ingreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formAgregarIngreso">
                <div class="modal-body">



                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Fecha</label>
                        <input type="date" class="form-control" name="fecha" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Cantidad</label>
                        <input type="number" class="form-control" id="" name="cantidad" required>
                    </div>


                </div>

                <div class="modal-footer">
                    <input type="hidden" id="ingreso_articulo_id" name="articulo_id">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-sm btn-primary" type="sumbit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
