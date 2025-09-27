<!-- Modal para Registrar Pago -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagoLabel">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPago">
                @csrf
                <div class="modal-body">
                    <!-- Información del Crédito -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="mb-2">Información del Crédito</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Cliente:</small>
                                        <div id="info_cliente" class="fw-medium">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Monto Total:</small>
                                        <div id="info_monto_total" class="fw-medium">—</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">Monto Pagado:</small>
                                        <div id="info_monto_pagado" class="fw-medium text-success">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Saldo Pendiente:</small>
                                        <div id="info_saldo_pendiente" class="fw-medium text-danger">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos del Pago -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="monto_pago" class="form-label">Monto del Pago <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="monto_pago" name="monto" step="0.01"
                                    min="0.01" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_pago" class="form-label">Fecha del Pago <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metodo_pago" class="form-label">Método de Pago</label>
                                <select class="form-select" id="metodo_pago" name="metodo_pago">
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
                                <label for="observaciones_pago" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones_pago" name="observaciones" rows="2"
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
                                        <div id="resumen_monto" class="fw-medium">$0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Saldo Después del Pago:</small>
                                        <div id="resumen_saldo_despues" class="fw-medium">$0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Estado Final:</small>
                                        <div id="resumen_estado_final" class="fw-medium">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
