<!-- Modal Unificado de Pagos y Cronograma -->
<div class="modal fade" id="modalPagosCronograma" tabindex="-1" aria-labelledby="modalPagosCronogramaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagosCronogramaLabel">Gestión de Pagos y Cronograma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información del Crédito -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Pagado/Total</p>
                                        <h6 id="info_pagado_total_unificado" class="mt-1 mb-0 fw-medium">—</h6>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/wallet.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Cantidad de Cuotas</p>
                                        <h6 id="info_cantidad_cuotas_unificado" class="mt-1 mb-0 fw-medium">—</h6>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/payment.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Tipo de Crédito</p>
                                        <h6 id="info_tipo_credito_unificado" class="mt-1 mb-0 fw-medium">—</h6>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/cal.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary d-none" id="btn_agregar_pago_credito">
                                <i class="fas fa-plus me-1"></i>Agregar Pago
                            </button>
                            <button type="button" class="btn btn-success" id="btn_pagar_cuotas_seleccionadas"
                                style="display: none;">
                                <i class="fas fa-credit-card me-1"></i>Pagar Seleccionadas (<span
                                    id="cuotas_seleccionadas_count">0</span>)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="pagosCronogramaTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cronograma-tab" data-bs-toggle="tab"
                            data-bs-target="#cronograma" type="button" role="tab" aria-controls="cronograma"
                            aria-selected="true">
                            <i class="fas fa-calendar-alt me-1"></i>Cronograma
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos"
                            type="button" role="tab" aria-controls="pagos" aria-selected="false">
                            <i class="fas fa-credit-card me-1"></i>Pagos
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="pagosCronogramaTabsContent">
                    <!-- Tab de Cronograma -->
                    <div class="tab-pane fade show active" id="cronograma" role="tabpanel"
                        aria-labelledby="cronograma-tab">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="cronograma_table">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="select_all_cuotas" name="select_all">
                                                    <label class="form-check-label" for="select_all_cuotas">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary ms-2"
                                                            id="btn_pagar_seleccion_header">
                                                            <i class="fas fa-credit-card me-1"></i>Pagar (<span
                                                                id="header_cuotas_count">0</span>)
                                                        </button>
                                                    </label>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Cuota</th>
                                        <th>Fecha Programada</th>
                                        <th>Monto Programado</th>
                                        <th>Ajuste</th>
                                        <th>Estado</th>
                                        <th>Fecha Real</th>
                                        <th>Monto Real</th>
                                        <th>Días de Atraso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llena dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab de Pagos -->
                    <div class="tab-pane fade" id="pagos" role="tabpanel" aria-labelledby="pagos-tab">
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
                <div id="monto_restante_alert_container">

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
                <!-- Campos ocultos requeridos -->
                <input type="hidden" name="credito_id" value="">
                <input type="hidden" id="tipo_pago_credito" name="tipo_pago" value="">
                <input type="hidden" id="cuotas_afectadas_credito" name="cuotas_afectadas" value="">

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

<!-- Modal para Pago de Cuota Individual -->
<div class="modal fade" id="modalPagoCuota" tabindex="-1" aria-labelledby="modalPagoCuotaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagoCuotaLabel">Pagar Cuota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPagoCuota">
                @csrf
                <div class="modal-body">
                    <!-- Información de la cuota -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="mb-2">Información de la Cuota</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Número de Cuota:</small>
                                        <div id="info_numero_cuota" class="fw-medium">—</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Fecha Programada:</small>
                                        <div id="info_fecha_programada" class="fw-medium">—</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Monto Programado:</small>
                                        <div id="info_monto_programado" class="fw-medium">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos del Pago -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_pago_cuota" class="form-label">Fecha del Pago <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_pago_cuota" name="fecha_pago"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metodo_pago_cuota" class="form-label">Método de Pago</label>
                                <select class="form-select" id="metodo_pago_cuota" name="metodo_pago">
                                    <option value="">Seleccionar método</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Campo de Monto -->
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="monto_pago_cuota" class="form-label">Monto a Pagar <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="monto_pago_cuota" name="monto"
                                        step="0.01" min="0.01" required>
                                    <div class="input-group-text">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="pagar_monto_exacto">
                                            <label class="form-check-label" for="pagar_monto_exacto">
                                                Monto exacto
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">
                                        Marque "Monto exacto" para pagar automáticamente el monto programado de la
                                        cuota.
                                        Puede ingresar un monto menor o mayor según sea necesario.
                                    </small>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="observaciones_pago_cuota" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones_pago_cuota" name="observaciones" rows="3"
                                    placeholder="Observaciones adicionales sobre el pago..."></textarea>
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
                                        <small class="text-muted">Monto Programado:</small>
                                        <div id="resumen_monto_programado" class="fw-medium">$0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Monto a Pagar:</small>
                                        <div id="resumen_monto_a_pagar" class="fw-medium">$0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Diferencia:</small>
                                        <div id="resumen_diferencia" class="fw-medium">$0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-credit-card me-1"></i>Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
