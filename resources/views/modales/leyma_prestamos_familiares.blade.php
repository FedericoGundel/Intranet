<!-- Modal Préstamo Familiar -->
<div class="modal fade" id="modalPrestamo" tabindex="-1" aria-labelledby="modalPrestamoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPrestamoLabel">Nuevo Préstamo Familiar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPrestamo">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Nombre del Familiar *</label>
                                <input type="text" class="form-control form-control-sm" id="familiar_nombre"
                                    name="familiar_nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">DNI del Familiar</label>
                                <input type="text" class="form-control form-control-sm" id="familiar_dni"
                                    name="familiar_dni">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Monto *</label>
                                <input type="number" class="form-control form-control-sm" id="monto" name="monto"
                                    step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Fecha de Préstamo *</label>
                                <input type="date" class="form-control form-control-sm" id="fecha_prestamo"
                                    name="fecha_prestamo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Fecha de Vencimiento</label>
                                <input type="date" class="form-control form-control-sm" id="fecha_vencimiento"
                                    name="fecha_vencimiento">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Teléfono del Familiar</label>
                                <input type="text" class="form-control form-control-sm" id="familiar_telefono"
                                    name="familiar_telefono">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Observaciones</label>
                                <textarea class="form-control form-control-sm" id="observaciones" name="observaciones" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Préstamo</button>
                </div>
            </form>
        </div>
    </div>
</div>
