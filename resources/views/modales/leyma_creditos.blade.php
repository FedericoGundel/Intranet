<!-- Modal Crédito -->
<div class="modal fade" id="modalCredito" tabindex="-1" aria-labelledby="modalCreditoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreditoLabel">Nuevo Crédito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCredito">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Cliente *</label>
                                <select class="form-select form-select-sm" id="cliente_id" name="cliente_id" required>
                                    <option value="">Seleccionar cliente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Usuario Responsable *</label>
                                <select class="form-select form-select-sm" id="usuario_id" name="usuario_id" required>
                                    <option value="">Seleccionar usuario</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Monto Principal *</label>
                                <input type="number" class="form-control form-control-sm" id="monto_principal"
                                    name="monto_principal" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Tipo de Crédito *</label>
                                <select class="form-select form-select-sm" id="tipo_pago" name="tipo_pago" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="diario">Diario</option>
                                    <option value="semanal">Semanal</option>
                                    <option value="quincenal">Quincenal</option>
                                    <option value="contado">Contado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Cantidad de Cuotas *</label>
                                <input type="number" class="form-control form-control-sm" id="cantidad_cuotas"
                                    name="cantidad_cuotas" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Porcentaje Total *</label>
                                <input type="number" class="form-control form-control-sm" id="porcentaje_base"
                                    name="porcentaje_base" step="0.01" required>
                                <small class="text-muted">Porcentaje total del crédito</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Monto a Cobrar <small
                                        class="text-muted">(calculado)</small></label>
                                <input type="text" class="form-control form-control-sm" id="monto_a_cobrar" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Fecha de Inicio *</label>
                                <input type="date" class="form-control form-control-sm" id="fecha_inicio"
                                    name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Fecha Final <small
                                        class="text-muted">(calculada)</small></label>
                                <input type="date" class="form-control form-control-sm" id="fecha_final"
                                    name="fecha_final">
                                <small class="text-muted">Se calcula automáticamente basado en el tipo y cantidad de
                                    cuotas</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Día de Pago</label>
                                <select class="form-select form-select-sm" id="dia_pago" name="dia_pago">
                                    <option value="">Seleccionar día...</option>
                                    <option value="lunes">Lunes</option>
                                    <option value="martes">Martes</option>
                                    <option value="miercoles">Miércoles</option>
                                    <option value="jueves">Jueves</option>
                                    <option value="viernes">Viernes</option>
                                    <option value="sabado">Sábado</option>
                                </select>
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
                    <button type="submit" class="btn btn-primary">Crear Crédito</button>
                </div>
            </form>
        </div>
    </div>
</div>
