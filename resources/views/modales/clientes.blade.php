<div class="modal fade" id="addClient" tabindex="-1" aria-labelledby="addClientLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientLabel">Agregar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formAddCliente">

                    <div class="row g-2">

                        <!-- Columna 1 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="nombre" class="form-label p-0 col-form-label-sm">Nombre*</label>
                                <input type="text" class="form-control form-control-sm " id="nombre"
                                    name="nombre">
                            </div>

                            <div class="mb-2">
                                <label for="apellido1" class="form-label p-0 col-form-label-sm">Primer Apellido</label>
                                <input type="text" class="form-control form-control-sm " id="apellido1"
                                    name="apellido1">
                            </div>

                            <div class="mb-2">
                                <label for="apellido2" class="form-label p-0 col-form-label-sm">Segundo Apellido</label>
                                <input type="text" class="form-control form-control-sm " id="apellido2"
                                    name="apellido2">
                            </div>

                            <div class="mb-2">
                                <label for="tipo_cliente" class="form-label p-0 col-form-label-sm">Tipo de Cliente</label>
                                <select class="form-select form-select-sm" id="tipo_cliente" name="tipo_cliente">
                                    <option value="particular">Particular</option>
                                    <option value="autonomo">Autónomo</option>
                                    <option value="empresa">Empresa</option>


                                </select>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="nif" class="form-label p-0 col-form-label-sm">NIF*</label>
                                <input type="text" class="form-control form-control-sm " id="nif"
                                    name="nif">
                            </div>

                            <div class="mb-2">
                                <label for="direccion" class="form-label p-0 col-form-label-sm">Dirección</label>
                                <input type="text" class="form-control form-control-sm " id="direccion"
                                    name="direccion">
                            </div>

                            <div class="mb-2">
                                <label for="codigo_postal" class="form-label p-0 col-form-label-sm">Código Postal</label>
                                <input type="text" class="form-control form-control-sm " id="codigo_postal"
                                    name="codigo_postal">
                            </div>

                            <div class="mb-2">
                                <label for="localidad" class="form-label p-0 col-form-label-sm">Localidad</label>
                                <input type="text" class="form-control form-control-sm " id="localidad"
                                    name="localidad">
                            </div>
                        </div>

                        <!-- Columna 3 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="provincia" class="form-label p-0 col-form-label-sm">Provincia</label>
                                <input type="text" class="form-control form-control-sm " id="provincia"
                                    name="provincia">
                            </div>

                            <div class="mb-2">
                                <label for="pais" class="form-label p-0 col-form-label-sm">País</label>
                                <input type="text" class="form-control form-control-sm " id="pais"
                                    name="pais">
                            </div>

                            <div class="mb-2">
                                <label for="email" class="form-label p-0 col-form-label-sm">Email</label>
                                <input type="email" class="form-control form-control-sm " id="email"
                                    name="email">
                            </div>

                            <div class="mb-2">
                                <label for="telefono" class="form-label p-0 col-form-label-sm">Teléfono</label>
                                <input type="text" class="form-control form-control-sm " id="telefono"
                                    name="telefono">
                            </div>
                        </div>

                    </div> <!-- .row -->
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formAddCliente" class="btn btn-sm btn-primary ">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editClient" tabindex="-1" aria-labelledby="editClientLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editClientLabel">Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditCliente">
                    <!-- Campo oculto para el ID -->
                    <input type="hidden" id="edit_id_cliente" name="id">

                    <div class="row g-2">
                        <!-- Columna 1 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="edit_nombre" class="form-label p-0 col-form-label-sm">Nombre*</label>
                                <input type="text" class="form-control form-control-sm " id="edit_nombre"
                                    name="nombre">
                            </div>
                            <div class="mb-2">
                                <label for="edit_apellido1" class="form-label p-0 col-form-label-sm">Primer Apellido</label>
                                <input type="text" class="form-control form-control-sm " id="edit_apellido1"
                                    name="apellido1">
                            </div>
                            <div class="mb-2">
                                <label for="edit_apellido2" class="form-label p-0 col-form-label-sm">Segundo Apellido</label>
                                <input type="text" class="form-control form-control-sm " id="edit_apellido2"
                                    name="apellido2">
                            </div>
                            <div class="mb-2">
                                <label for="edit_tipo_cliente" class="form-label p-0 col-form-label-sm">Tipo de Cliente</label>
                                <select class="form-select form-select-sm" id="edit_tipo_cliente" name="tipo_cliente">
                                    <option value="particular">Particular</option>
                                    <option value="autonomo">Autónomo</option>
                                    <option value="empresa">Empresa</option>
                                </select>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="edit_nif" class="form-label p-0 col-form-label-sm">NIF*</label>
                                <input type="text" class="form-control form-control-sm " id="edit_nif"
                                    name="nif">
                            </div>
                            <div class="mb-2">
                                <label for="edit_direccion" class="form-label p-0 col-form-label-sm">Dirección</label>
                                <input type="text" class="form-control form-control-sm " id="edit_direccion"
                                    name="direccion">
                            </div>
                            <div class="mb-2">
                                <label for="edit_codigo_postal" class="form-label p-0 col-form-label-sm">Código Postal</label>
                                <input type="text" class="form-control form-control-sm " id="edit_codigo_postal"
                                    name="codigo_postal">
                            </div>
                            <div class="mb-2">
                                <label for="edit_localidad" class="form-label p-0 col-form-label-sm">Localidad</label>
                                <input type="text" class="form-control form-control-sm " id="edit_localidad"
                                    name="localidad">
                            </div>
                        </div>

                        <!-- Columna 3 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="edit_provincia" class="form-label p-0 col-form-label-sm">Provincia</label>
                                <input type="text" class="form-control form-control-sm " id="edit_provincia"
                                    name="provincia">
                            </div>
                            <div class="mb-2">
                                <label for="edit_pais" class="form-label p-0 col-form-label-sm">País</label>
                                <input type="text" class="form-control form-control-sm " id="edit_pais"
                                    name="pais">
                            </div>
                            <div class="mb-2">
                                <label for="edit_email" class="form-label p-0 col-form-label-sm">Email</label>
                                <input type="email" class="form-control form-control-sm " id="edit_email"
                                    name="email">
                            </div>
                            <div class="mb-2">
                                <label for="edit_telefono" class="form-label p-0 col-form-label-sm">Teléfono</label>
                                <input type="text" class="form-control form-control-sm " id="edit_telefono"
                                    name="telefono">
                            </div>
                        </div>
                    </div> <!-- .row -->
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formEditCliente" class="btn btn-sm btn-success">Actualizar</button>
            </div>
        </div>
    </div>
</div>
