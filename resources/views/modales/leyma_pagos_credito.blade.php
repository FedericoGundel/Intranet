<!-- Modal para Gestionar Pagos del Crédito -->
<div class="modal fade" id="modalPagosCredito" tabindex="-1" aria-labelledby="modalPagosCreditoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagosCreditoLabel">Gestión de Pagos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información del Crédito -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="mb-2">Información del Crédito</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">Cliente:</small>
                                    <div id="info_cliente_pagos" class="fw-medium">—</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Monto Total:</small>
                                    <div id="info_monto_total_pagos" class="fw-medium">—</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Monto Pagado:</small>
                                    <div id="info_monto_pagado_pagos" class="fw-medium text-success">—</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Saldo Pendiente:</small>
                                    <div id="info_saldo_pendiente_pagos" class="fw-medium text-danger">—</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón Agregar Pago -->
                <div class="row mb-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" id="btn_agregar_pago_credito">
                            <i class="fas fa-plus me-1"></i>Agregar Pago
                        </button>
                    </div>
                </div>

                <!-- Tabla de Pagos -->
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="pagos_credito_table">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Método</th>
                                        <th>Observaciones</th>
                                        <th>Usuario</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llena dinámicamente con DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar/Editar Pago -->
<div class="modal fade" id="modalAgregarPagoCredito" tabindex="-1" aria-labelledby="modalAgregarPagoCreditoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarPagoCreditoLabel">Agregar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAgregarPagoCredito">
                @csrf
                <div class="modal-body">
                    <!-- Campos del Pago -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="monto_pago_credito" class="form-label">Monto del Pago <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="monto_pago_credito" name="monto"
                                    step="0.01" min="0.01" max="999999999" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_pago_credito" class="form-label">Fecha del Pago <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_pago_credito" name="fecha_pago"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metodo_pago_credito" class="form-label">Método de Pago</label>
                                <select class="form-select" id="metodo_pago_credito" name="metodo_pago">
                                    <option value="">Seleccionar método</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones_pago_credito" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones_pago_credito" name="observaciones" rows="2"
                                    placeholder="Observaciones adicionales..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen del Pago -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <h6 class="mb-2">Resumen del Pago</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Monto a Pagar:</small>
                                        <div id="resumen_monto_credito" class="fw-medium">$0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Saldo Después del Pago:</small>
                                        <div id="resumen_saldo_despues_credito" class="fw-medium">$0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Estado Final:</small>
                                        <div id="resumen_estado_final_credito" class="fw-medium">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
