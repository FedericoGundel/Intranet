<div class="modal fade" id="addGasto" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formAddGasto" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class=" row g-2">
                    <div class="col-md-4">
                        <label for="fecha" class="form-label p-0 col-form-label-sm">Fecha</label>
                        <input type="date" class="form-control form-control-sm " name="fecha" required>
                    </div>
                    <div class="col-md-4 ">
                        <label for="categoria" class="form-label p-0 col-form-label-sm">Categoría</label>
                        <input type="text" class="form-control form-control-sm " name="categoria" required>
                    </div>
                    <div class="col-md-4 d-none">
                        <label for="subcategoria" class="form-label p-0 col-form-label-sm">Subcategoría</label>
                        <input type="text" class="form-control form-control-sm " name="subcategoria">
                    </div>
                    <div class="col-md-4">
                        <label for="monto" class="form-label p-0 col-form-label-sm">Monto</label>
                        <input type="number" step="0.01" class="form-control form-control-sm " name="monto"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label for="metodo_pago" class="form-label p-0 col-form-label-sm">Método de Pago</label>
                        <select name="metodo_pago" class="form-control form-control-sm ">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="nombre" class="form-label p-0 col-form-label-sm">Empleado</label>
                        <div class="d-flex align-items-center gap-1 mb-2">
                            <select class="form-select form-select-sm form-select form-select-sm-sm select_empleados"id="select_empleados_gasto"
                                data-tipo="empleado" name="empleado_id">
                                <option value="">Seleccione un empleado</option>
                                <!-- opciones dinámicas -->
                            </select>

                            <button type="button" class="btn btn-sm btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addEmpleado" title="Agregar">
                                <i class="bi bi-plus"></i>
                            </button>

                            <button type="button" data-source="select_empleados_gasto"
                                class="btn btn-sm btn-primary btn-sm btn_editar_empleado" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button type="button" data-source="select_empleados_gasto"
                                class="btn btn-sm btn-danger btn-sm btn_eliminar_empleado" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="descripcion" class="form-label p-0 col-form-label-sm">Descripción</label>
                        <textarea class="form-control form-control-sm " name="descripcion"></textarea>
                    </div>
                    <div class="col-12">
                        <label for="comprobante" class="form-label p-0 col-form-label-sm">Comprobante</label>
                        <input type="file" class="form-control form-control-sm " name="comprobante">
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<!-- ========== MODAL: EDITAR GASTO ========== -->
<div class="modal fade" id="editGasto" tabindex="-1" aria-labelledby="editGastoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editGastoLabel">Editar Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formEditGasto" autocomplete="off" enctype="multipart/form-data">
                    <input type="hidden" id="edit_id_gasto" name="id">

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label p-0 col-form-label-sm">Fecha*</label>
                            <input type="date" class="form-control form-control-sm " name="fecha" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label p-0 col-form-label-sm ">Categoría*</label>
                            <input type="text" class="form-control form-control-sm " name="categoria" required>
                        </div>

                        <div class="col-md-4 d-none">
                            <label class="form-label p-0 col-form-label-sm ">Subcategoría</label>
                            <input type="text" class="form-control form-control-sm " name="subcategoria">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label p-0 col-form-label-sm">Monto*</label>
                            <input type="number" step="0.01" min="0"
                                class="form-control form-control-sm " name="monto" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label p-0 col-form-label-sm">Método de pago*</label>
                            <select class="form-select form-select-sm form-select form-select-sm-sm" name="metodo_pago" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta">Tarjeta</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="nombre" class="form-label p-0 col-form-label-sm">Empleado</label>
                            <div class="d-flex align-items-center gap-1 mb-2">
                                <select id="select_empleados_gasto_editar"
                                    class="form-select form-select-sm form-select form-select-sm-sm select_empleados" data-tipo="empleado"
                                    name="empleado_id">
                                    <option value="">Seleccione un empleado</option>
                                    <!-- opciones dinámicas -->
                                </select>

                                <button type="button" class="btn btn-sm btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addEmpleado" title="Agregar">
                                    <i class="bi bi-plus"></i>
                                </button>

                                <button type="button" data-source="select_empleados_gasto_editar"
                                    class="btn btn-sm btn-primary btn-sm btn_editar_empleado" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button type="button" data-source="select_empleados_gasto_editar"
                                    class="btn btn-sm btn-danger btn-sm btn_eliminar_empleado" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label p-0 col-form-label-sm">Descripción</label>
                            <textarea class="form-control form-control-sm " name="descripcion" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label p-0 col-form-label-sm">Comprobante (pdf/jpg/png)</label>
                            <input type="file" class="form-control form-control-sm " name="comprobante"
                                accept=".pdf,.jpg,.jpeg,.png">
                            <!-- aqui muestro el comprobante actual si existe -->
                            <div class="form-text" id="comprobante_actual_editar" style="display:none;">
                                Comprobante actual:
                                <a id="link_comprobante_actual" href="#" target="_blank"
                                    rel="noopener">Ver</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formEditGasto" class="btn btn-success">Actualizar</button>
            </div>
        </div>
    </div>
</div>
