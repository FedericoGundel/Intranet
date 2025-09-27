<div class="modal fade" id="modalAjustesCredito" tabindex="-1" aria-labelledby="modalAjustesCreditoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAjustesCreditoLabel">Ajustes del Crédito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body py-2">
                                <p class="text-muted text-uppercase mb-0 fs-12">Base del Crédito</p>
                                <h6 id="aj_base_total" class="mb-0">—</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body py-2">
                                <p class="text-muted text-uppercase mb-0 fs-12">Pagado</p>
                                <h6 id="aj_pagado" class="mb-0">—</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body py-2">
                                <p class="text-muted text-uppercase mb-0 fs-12">Ajustes (+/-)</p>
                                <h6 id="aj_ajustes" class="mb-0">—</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body py-2">
                                <p class="text-muted text-uppercase mb-0 fs-12">Total Final</p>
                                <h6 id="aj_total_final" class="mb-0">—</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Ajustes</h6>
                    <button id="btn_agregar_ajuste_credito" class="btn btn-sm btn-success"><i
                            class="las la-plus me-1"></i>Agregar Ajuste</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="ajustes_credito_table">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Última Cuota</th>
                                <th>Observaciones</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAgregarAjusteCredito" tabindex="-1" aria-labelledby="modalAgregarAjusteCreditoLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarAjusteCreditoLabel">Agregar Ajuste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAgregarAjusteCredito">
                <div class="modal-body">
                    <div class="alert alert-info d-none" id="ajuste_alert_base">Los descuentos solo se pueden aplicar
                        cuando hay saldo pendiente en el crédito.</div>
                    <div class="mb-2">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" id="aj_tipo" required>
                            <option value="recargo">Recargo (+)</option>
                            <option value="descuento">Descuento (-)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Monto</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="aj_monto"
                            required />
                    </div>
                    <div class="mb-2" id="ultimo_pago_container">
                        <label class="form-label">Última Cuota Pagada</label>
                        <select class="form-select" id="aj_ultimo_pago_considerado">
                            <option value="">Usar última cuota pagada</option>
                        </select>
                        <small class="form-text text-muted">El descuento se aplicará sobre el saldo insoluto después de
                            esta cuota</small>
                    </div>
                    <div class="alert alert-info d-none" id="descuento_info">
                        <strong>Información importante:</strong><br>
                        • El descuento se calcula sobre el capital pendiente después de la cuota seleccionada<br>
                        • Se recalculará el plan de pagos desde la siguiente cuota<br>
                        • Las cuotas ya pagadas mantienen su valor original
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Concepto</label>
                        <input type="text" class="form-control" id="aj_concepto" required />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" id="aj_obs" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
