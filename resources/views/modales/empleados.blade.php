<div class="modal fade" id="addEmpleado" tabindex="-1" aria-labelledby="addEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addEmpleadoLabel">Agregar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formAddEmpleado">
                    @csrf
                    <div class="row g-3">
                        <!-- Columna 1 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="add_nombre" class="form-label p-0 col-form-label-sm">Nombre*</label>
                                <input type="text" class="form-control form-control-sm " id="add_nombre"
                                    name="nombre" required>
                            </div>
                            <div class="mb-2">
                                <label for="add_apellido" class="form-label p-0 col-form-label-sm">Apellido</label>
                                <input type="text" class="form-control form-control-sm " id="add_apellido"
                                    name="apellido">
                            </div>
                            <div class="mb-2">
                                <label for="add_dni" class="form-label p-0 col-form-label-sm">DNI</label>
                                <input type="text" class="form-control form-control-sm " id="add_dni"
                                    name="dni" placeholder="Ej: 32.123.456">
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="add_email" class="form-label p-0 col-form-label-sm">Email</label>
                                <input type="email" class="form-control form-control-sm " id="add_email"
                                    name="email" placeholder="empleado@empresa.com">
                            </div>
                            <div class="mb-2">
                                <label for="add_telefono" class="form-label p-0 col-form-label-sm">Teléfono</label>
                                <input type="text" class="form-control form-control-sm " id="add_telefono"
                                    name="telefono" placeholder="Ej: +54 9 291...">
                            </div>
                            <div class="mb-2">
                                <label for="add_fecha_ingreso" class="form-label p-0 col-form-label-sm">Fecha de
                                    Ingreso</label>
                                <input type="date" class="form-control form-control-sm " id="add_fecha_ingreso"
                                    name="fecha_ingreso">
                            </div>
                        </div>

                        <!-- Columna 3 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="add_estado" class="form-label p-0 col-form-label-sm">Estado</label>
                                <select id="add_estado" name="estado" class="form-select form-select-sm">
                                    <option value="activo" selected>Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>

                            <!-- Switch Vendedor -->
                            <!-- Select Rol -->
                            <div class="mb-2">
                                <label for="add_rol" class="form-label p-0 col-form-label-sm">Rol</label>
                                <select id="add_rol" name="rol" class="form-select form-select-sm">
                                    <option value="otro" selected>Otro</option>
                                    <option value="vendedor">Vendedor</option>
                                    <option value="cobrador">Cobrador</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- Bloque Vendedor (colapsable) -->
                    <div id="add_bloque_vendedor" class="border rounded p-3 mt-3 collapse">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label p-0 col-form-label-sm" for="add_meta_mensual">Meta mensual
                                    ($)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm "
                                    id="add_meta_mensual" name="vendedor[meta_mensual]" placeholder="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label p-0 col-form-label-sm" for="add_comision_porcentaje">Comisión
                                    (%)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm "
                                    id="add_comision_porcentaje" name="vendedor[comision_porcentaje]"
                                    placeholder="Ej: 3.50">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label p-0 col-form-label-sm" for="add_zona">Zona</label>
                                <input type="text" class="form-control form-control-sm " id="add_zona"
                                    name="vendedor[zona]" placeholder="Centro, Norte, etc.">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formAddEmpleado" class="btn btn-sm btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL: EDITAR EMPLEADO ========== -->
<div class="modal fade" id="editEmpleado" tabindex="-1" aria-labelledby="editEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editEmpleadoLabel">Editar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formEditEmpleado" autocomplete="off">
                    @csrf
                    <input type="hidden" id="edit_empleado_id" name="id">

                    <div class="row g-3">
                        <!-- Columna 1 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="edit_nombre" class="form-label p-0 col-form-label-sm">Nombre*</label>
                                <input type="text" class="form-control form-control-sm " id="edit_nombre"
                                    name="nombre" required>
                            </div>
                            <div class="mb-2">
                                <label for="edit_apellido" class="form-label p-0 col-form-label-sm">Apellido</label>
                                <input type="text" class="form-control form-control-sm " id="edit_apellido"
                                    name="apellido">
                            </div>
                            <div class="mb-2">
                                <label for="edit_dni" class="form-label p-0 col-form-label-sm">DNI</label>
                                <input type="text" class="form-control form-control-sm " id="edit_dni"
                                    name="dni">
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="edit_email" class="form-label p-0 col-form-label-sm">Email</label>
                                <input type="email" class="form-control form-control-sm " id="edit_email"
                                    name="email">
                            </div>
                            <div class="mb-2">
                                <label for="edit_telefono" class="form-label p-0 col-form-label-sm">Teléfono</label>
                                <input type="text" class="form-control form-control-sm " id="edit_telefono"
                                    name="telefono">
                            </div>
                            <div class="mb-2">
                                <label for="edit_fecha_ingreso" class="form-label p-0 col-form-label-sm">Fecha de
                                    Ingreso</label>
                                <input type="date" class="form-control form-control-sm " id="edit_fecha_ingreso"
                                    name="fecha_ingreso">
                            </div>
                        </div>

                        <!-- Columna 3 -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="edit_estado" class="form-label p-0 col-form-label-sm">Estado</label>
                                <select id="edit_estado" name="estado" class="form-select form-select-sm">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>

                            <!-- Switch Vendedor -->
                            <!-- Select Rol -->
                            <div class="mb-2">
                                <label for="edit_rol" class="form-label p-0 col-form-label-sm">Rol</label>
                                <select id="edit_rol" name="rol" class="form-select form-select-sm">
                                    <option value="otro" selected>Otro</option>
                                    <option value="vendedor">Vendedor</option>
                                    <option value="cobrador">Cobrador</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- Bloque Vendedor (colapsable) -->
                    <div id="edit_bloque_vendedor" class="border rounded p-3 mt-3 collapse">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label p-0 col-form-label-sm" for="edit_meta_mensual">Meta mensual
                                    ($)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm "
                                    id="edit_meta_mensual" name="vendedor[meta_mensual]">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label p-0 col-form-label-sm"
                                    for="edit_comision_porcentaje">Comisión (%)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm "
                                    id="edit_comision_porcentaje" name="vendedor[comision_porcentaje]">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label p-0 col-form-label-sm" for="edit_zona">Zona</label>
                                <input type="text" class="form-control form-control-sm " id="edit_zona"
                                    name="vendedor[zona]">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" form="formEditEmpleado" class="btn btn-sm btn-success">Actualizar</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="verFacturasVendedor" tabindex="-1" aria-labelledby="agregarPlantillaEmailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive mb-3">
                    <table class="table mb-0" id="facturas_vendedor_table">
                        <thead class="table-light">
                            <tr>
                                <th>Número</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Pagado</th>
                                <th>Saldo</th>
                                <th class="text-start">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llena dinámicamente con DataTables o JS -->
                        </tbody>
                    </table>
                </div>

                <!-- ===== Cálculo de nómina ===== -->

                <!-- ============================ -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verNominasEmpleado" tabindex="-1"
    data-bs-focus="false"aria-labelledby="verNominasEmpleadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verNominasEmpleadoLabel">Nóminas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive mb-3">
                    <table class="table mb-0" id="nominas_empleado_table">
                        <thead class="table-light">
                            <tr>
                                <th>Período</th>

                                <th>%</th>
                                <th>Base</th>
                                <th>Comisión</th>

                                <th>Total</th>
                                <th>Pagado</th>
                                <th>Saldo</th>
                                <th class="text-start">Estado</th>
                                <th class="text-start">Acciones</th>
                            </tr>
                        </thead>
                        <tbody><!-- dinámico --></tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
















<div class="modal fade" id="calcularNominas" data-bs-focus="false" tabindex="-1"
    aria-labelledby="agregarPlantillaEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Empleados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">

                <div class="row g-3 align-items-end">
                    <div class="col-12">
                        <h5 class="m-0">Calular nómina por vendedor en un rango de fechas</h5>
                    </div>
                    <div class="col-md-3">
                        <label for="nomina_inicio" class="form-label p-0 col-form-label-sm">Inicio de semana</label>
                        <input type="date" class="form-control form-control-sm " id="nomina_inicio"
                            name="nomina_inicio">
                    </div>
                    <div class="col-md-3">
                        <label for="nomina_fin" class="form-label p-0 col-form-label-sm">Fin de semana</label>
                        <input type="date" class="form-control form-control-sm " id="nomina_fin"
                            name="nomina_fin">
                    </div>
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table mb-0" id="empleados_nomina_table">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    name="select_all">
                                                <label class="form-check-label" for="">

                                                </label>
                                            </div>
                                        </th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>DNI</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llena dinámicamente con DataTables o JS -->
                                </tbody>
                            </table>



                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Hidden para el vendedor/empleado -->
                        <input type="hidden" id="nomina_empleado_id" name="empleado_id">

                    </div>
                </div>

                <!-- Resultado (opcional) -->
                <div class="mt-3" id="nomina_resultado" style="display:none;">
                    <div class="alert alert-info mb-0">
                        <strong>Resultado:</strong>
                        <span id="nomina_resumen_texto">—</span>
                    </div>
                </div>



            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" id="btn_calcular_nomina">
                    Calcular nómina
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalNomina" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Nómina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Acá inyectamos -->
                <div id="nomina_preview_contenedor"></div>
                <!-- O si querés un wrapper distinto -->
                <div id="modal_nomina_contenido"></div>
            </div>
        </div>
    </div>
</div>
