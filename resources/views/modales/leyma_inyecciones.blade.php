<!-- Modal Inyección -->
<div class="modal fade" id="modalInyeccion" tabindex="-1" aria-labelledby="modalInyeccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInyeccionLabel">Nueva Inyección</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formInyeccion">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Concepto *</label>
                                <input type="text" class="form-control form-control-sm" id="concepto"
                                    name="concepto" required>
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
                                <label class="form-label p-0 col-form-label-sm">Fecha *</label>
                                <input type="date" class="form-control form-control-sm" id="fecha" name="fecha"
                                    required>
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
                    <button type="submit" class="btn btn-primary">Guardar Inyección</button>
                </div>
            </form>
        </div>
    </div>
</div>
