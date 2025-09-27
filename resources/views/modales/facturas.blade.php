<!-- Modal: Agregar Plantilla -->
<div class="modal fade" id="verPagosFactura" tabindex="-1" aria-labelledby="agregarPlantillaEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">


        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pagos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 text-end">
                    <button type="button" class=" btn btn-sm btn-success" id="btn_agregar_pago">
                        <i class="las la-plus"></i> Agregar pago
                    </button>

                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="pagos_factura_table">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Método de pago</th>

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


<div class="modal fade" id="modalAgregarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Agregar pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formAgregarPago">
                <div class="modal-body">

                    <input type="hidden" id="pago_factura_id" name="factura_id">
                    <div class="mb-2">
                        <label for="nombre" class="form-label p-0 col-form-label-sm">Selecciona un
                            cobrador</label>
                        <div class="d-flex align-items-center gap-1 ">
                            <select id="select_cobrador_pago" class="form-select form-select-sm select_empleados"
                                data-tipo="cobrador" name="cobrador_id">
                                <option value="">Seleccione un cobrador</option>
                                <!-- opciones dinámicas -->
                            </select>

                            <button type="button" class="btn btn-sm btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addEmpleado" title="Agregar">
                                <i class="bi bi-plus"></i>
                            </button>

                            <button type="button" data-source="select_cobrador_pago"
                                class="btn btn-sm btn-primary btn-sm btn_editar_empleado" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button type="button" data-source="select_cobrador_pago"
                                class="btn btn-sm btn-danger btn-sm btn_eliminar_empleado" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Fecha</label>
                        <input type="date" class="form-control form-control-sm " id="pago_fecha" name="fecha"
                            required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Monto</label>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm "
                            id="pago_monto" name="monto" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Método</label>
                        <input type="text" class="form-control form-control-sm " id="pago_metodo" name="metodo"
                            placeholder="Efectivo, transferencia, MP...">
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Referencia</label>
                        <input type="text" class="form-control form-control-sm " id="pago_referencia"
                            name="referencia" placeholder="N° operación, cupón, etc.">
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Notas</label>
                        <textarea class="form-control form-control-sm " id="pago_notas" name="notas" rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-sm btn-primary" type="sumbit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formEditarPago">
                <div class="modal-body">
                    <input type="hidden" id="edit_pago_id"> <!-- solo para JS -->
                    <input type="hidden" id="edit_pago_factura_id" name="factura_id"> <!-- por si querés validar -->
                    <div class="mb-2">
                        <label for="nombre" class="form-label p-0 col-form-label-sm">Selecciona un
                            cobrador</label>
                        <div class="d-flex align-items-center gap-1 ">
                            <select id="select_cobrador_pago_edit" class="form-select form-select-sm select_empleados"
                                data-tipo="cobrador" name="cobrador_id">
                                <option value="">Seleccione un cobrador</option>
                                <!-- opciones dinámicas -->
                            </select>

                            <button type="button" class="btn btn-sm btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addEmpleado" title="Agregar">
                                <i class="bi bi-plus"></i>
                            </button>

                            <button type="button" data-source="select_cobrador_pago_edit"
                                class="btn btn-sm btn-primary btn-sm btn_editar_empleado" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button type="button" data-source="select_cobrador_pago_edit"
                                class="btn btn-sm btn-danger btn-sm btn_eliminar_empleado" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Fecha</label>
                        <input type="date" class="form-control form-control-sm " id="edit_pago_fecha"
                            name="fecha" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Monto</label>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm "
                            id="edit_pago_monto" name="monto" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Método</label>
                        <input type="text" class="form-control form-control-sm " id="edit_pago_metodo"
                            name="metodo" placeholder="Efectivo, transferencia, MP...">
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Referencia</label>
                        <input type="text" class="form-control form-control-sm " id="edit_pago_referencia"
                            name="referencia" placeholder="N° operación, cupón, etc.">
                    </div>

                    <div class="mb-2">
                        <label class="form-label p-0 col-form-label-sm">Notas</label>
                        <textarea class="form-control form-control-sm " id="edit_pago_notas" name="notas" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-sm btn-primary" type="submit">Guardar cambios</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalVerFactura" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="iframeFactura" src="" width="100%" height="100%" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
