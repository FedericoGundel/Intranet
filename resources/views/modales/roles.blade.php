<!-- Modal: Agregar Plantilla -->
<div class="modal fade" id="addRol" tabindex="-1" aria-labelledby="agregarPlantillaEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal modal-dialog-centered">


        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label p-0 col-form-label-sm">Nombre*</label>
                    <input type="text" class="form-control form-control-sm " id="nombre_rol" placeholder="">
                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="permisos_table">
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
                <button class="btn btn-sm btn-primary" id="btn_add_rol">Guardar</button>
            </div>
        </div>

    </div>
</div>





<div class="modal fade" id="editRol" tabindex="-1" aria-labelledby="agregarPlantillaEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal modal-dialog-centered">


        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label p-0 col-form-label-sm">Nombre*</label>
                    <input type="text" class="form-control form-control-sm " id="nombre_rol_editar" placeholder="">
                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="permisos_table_editar">
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
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llena dinámicamente con DataTables o JS -->
                        </tbody>
                    </table>



                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="edit_id_rol">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-sm btn-primary" id="btn_edit_rol">Guardar</button>
            </div>
        </div>

    </div>
</div>






<div class="modal fade" id="RolDefecto" tabindex="-1" aria-labelledby="agregarPlantillaEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal modal-dialog-centered">


        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Rol por defecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label p-0 col-form-label-sm">Elegir rol (este rol sera asignado a los usuarios nuevos)</label>
                    <div class="d-flex align-items-center gap-1">
                        <select id="select_roles" class="form-select form-select-sm select_roles" id="rol_defecto" name="rol_defecto"
                            data-key="rol_defecto" style="">
                            <option value="">Seleccione un rol</option>
                            <!-- opciones dinámicas -->
                        </select>


                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <input type="hidden" id="edit_id_rol">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-sm btn-primary" id="btn_edit_rol_defecto">Guardar</button>
            </div>
        </div>

    </div>
</div>






<div class="modal fade" id="editarRolUsuario" tabindex="-1" aria-labelledby="agregarPlantillaEmailLabel"
    aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal modal-dialog-centered">


        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label p-0 col-form-label-sm">Elegir rol</label>
                    <div class="d-flex align-items-center gap-1">
                        <select id="rol_usuario" class="form-select form-select-sm select_roles" style="">
                            <option value="">Seleccione un rol</option>
                            <!-- opciones dinámicas -->
                        </select>


                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <input type="hidden" id="edit_id_rol">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-sm btn-primary" id="btn_edit_rol_usuario">Guardar</button>
            </div>
        </div>

    </div>
</div>
