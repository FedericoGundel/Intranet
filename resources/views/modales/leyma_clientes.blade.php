<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClienteLabel">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCliente">
                <input type="hidden" id="estado" name="estado" value="activo">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Nombre Completo *</label>
                                <input type="text" class="form-control form-control-sm" id="nombre" name="nombre"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">DNI *</label>
                                <input type="text" class="form-control form-control-sm" id="dni" name="dni"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Teléfono *</label>
                                <input type="text" class="form-control form-control-sm" id="telefono"
                                    name="telefono" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Domicilio *</label>
                                <input type="text" class="form-control form-control-sm" id="domicilio"
                                    name="domicilio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Comercio</label>
                                <input type="text" class="form-control form-control-sm" id="comercio_negocio"
                                    name="comercio_negocio">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Email</label>
                                <input type="email" class="form-control form-control-sm" id="email"
                                    name="email">
                            </div>
                        </div>

                        <!-- Datos del Garante -->
                        <div class="col-12">
                            <hr>
                            <h6>Datos del Garante</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Nombre del Garante</label>
                                <input type="text" class="form-control form-control-sm" id="garante_nombre"
                                    name="garante_nombre">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">DNI del Garante</label>
                                <input type="text" class="form-control form-control-sm" id="garante_dni"
                                    name="garante_dni">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Teléfono del Garante</label>
                                <input type="text" class="form-control form-control-sm" id="garante_telefono"
                                    name="garante_telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Domicilio del Garante</label>
                                <input type="text" class="form-control form-control-sm" id="garante_domicilio"
                                    name="garante_domicilio">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label p-0 col-form-label-sm">Observaciones</label>
                                <textarea class="form-control form-control-sm" id="observaciones" name="observaciones" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="permite_creditos_multiples" name="permite_creditos_multiples" checked>
                                    <label class="form-check-label" for="permite_creditos_multiples">
                                        Permite múltiples créditos activos
                                    </label>
                                </div>
                                <small class="text-muted">Si está activado, el cliente puede tener varios créditos activos simultáneamente</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>
