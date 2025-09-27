@extends('layouts.app')

@section('title', 'Inicio')


@section('content')

    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content py-2">
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-12 mb-2">
                        <div class="nav-tabs-custom text-start">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-center active" data-bs-toggle="tab" href="#cu_home"
                                        role="tab" aria-selected="false" tabindex="-1"><i
                                            class="las la-address-card d-block"></i>Información</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-center " data-bs-toggle="tab" href="#cu_media" role="tab"
                                        aria-selected="true"><i class="la la-image d-block"></i>Medios</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-center" data-bs-toggle="tab" href="#cu_colors" role="tab"
                                        aria-selected="false" tabindex="-1"><i
                                            class="la la-palette d-block"></i>Colores</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-center" data-bs-toggle="tab" href="#cu_logs" role="tab"
                                        aria-selected="false" tabindex="-1"><i class="la la-user d-block"></i>Accesos</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="tab-content">
                            <div class="tab-pane p-0 active show" id="cu_home" role="tabpanel">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h5 class="mb-0">Datos de la empresa</h5>
                                            </div><!--end col-->
                                            <div class="col-auto d-none">
                                                <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                                    data-bs-target="#addArticulo"><i class="fas fa-plus me-1"></i>Agregar
                                                    Artículo</button>

                                            </div><!--end col-->
                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body pt-0 px-3 pb-3">
                                        <div class="row g-2">
                                            <!-- Columna 1 -->
                                            <div class="col-md-4">

                                                <div class="mb-2">
                                                    <label for="nombre" class="form-label">Nombre comercial</label>
                                                    <input type="text" class="form-control config-input" id="nombre_app"
                                                        name="nombre_app" value="{{ $configurations['nombre_app'] ?? '' }}"
                                                        data-key="nombre_app">
                                                </div>

                                                <div class="mb-2">
                                                    <label for="nombre" class="form-label">Razón social</label>
                                                    <input type="text" class="form-control config-input"
                                                        id="razon_social" name="razon_social"
                                                        value="{{ $configurations['razon_social'] ?? '' }}"
                                                        data-key="razon_social">
                                                </div>
                                                <div class="mb-2 mb-md-0">
                                                    <label for="nombre" class="form-label">Dirección</label>
                                                    <input type="text" class="form-control config-input" id="direccion"
                                                        name="direccion" value="{{ $configurations['direccion'] ?? '' }}"
                                                        data-key="direccion">
                                                </div>
                                            </div>

                                            <!-- Columna 2 -->
                                            <div class="col-md-4">

                                                <div class="mb-2">
                                                    <label for="nombre" class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control config-input" id="telefono"
                                                        name="telefono" value="{{ $configurations['telefono'] ?? '' }}"
                                                        data-key="telefono">
                                                </div>

                                                <div class="mb-2">
                                                    <label for="nombre" class="form-label">Email</label>
                                                    <input type="text" class="form-control config-input"
                                                        id="email" name="email"
                                                        value="{{ $configurations['email'] ?? '' }}" data-key="email">
                                                </div>
                                                <div class="mb-2 mb-md-0">
                                                    <label for="nombre" class="form-label">Sitio web</label>
                                                    <input type="text" class="form-control config-input"
                                                        id="pagina_web" name="pagina_web"
                                                        value="{{ $configurations['pagina_web'] ?? '' }}"
                                                        data-key="pagina_web">
                                                </div>
                                            </div>
                                            <div class="col-md-4">

                                                <div class="mb-2">
                                                    <label for="nombre" class="form-label">NIF</label>
                                                    <input type="text" class="form-control config-input"
                                                        id="nif" name="nif"
                                                        value="{{ $configurations['nif'] ?? '' }}" data-key="nif">
                                                </div>

                                                <div class="mb-2">
                                                    <label for="nombre" class="form-label">País</label>
                                                    <input type="text" class="form-control config-input"
                                                        id="pais" name="pais"
                                                        value="{{ $configurations['pais'] ?? '' }}" data-key="pais">
                                                </div>
                                                <div class="mb-2 mb-md-0">
                                                    <label for="nombre" class="form-label">Provincia</label>
                                                    <input type="text" class="form-control config-input"
                                                        id="provincia" name="provincia"
                                                        value="{{ $configurations['provincia'] ?? '' }}"
                                                        data-key="provincia">
                                                </div>
                                            </div>

                                            <div class="col-md-4">

                                                <div class="mb-2 mb-md-0">
                                                    <label for="nombre" class="form-label">Ciudad</label>
                                                    <input type="text" class="form-control config-input"
                                                        id="ciudad" name="ciudad"
                                                        value="{{ $configurations['ciudad'] ?? '' }}" data-key="ciudad">
                                                </div>


                                            </div>
                                        </div> <!-- .row -->
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane p-0 " id="cu_media" role="tabpanel">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h5 class="mb-0">Medios de pantalla inicio de sesión</h5>
                                            </div><!--end col-->
                                            <div class="col-auto d-none">
                                                <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                                    data-bs-target="#addArticulo"><i class="fas fa-plus me-1"></i>Agregar
                                                    Artículo</button>

                                            </div><!--end col-->
                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body pt-0 px-3 pb-3">
                                        <div class="row g-2">




                                            <!-- Columna 3: Imagen -->
                                            <div class="col-md-4">


                                                <div class="d-grid">
                                                    <label for="imagen" class="form-label">Logo de inicio de
                                                        sesión:</label>
                                                    <div style="height: 142px;" id="preview_logo_login"
                                                        class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-2">

                                                        <img class="preview-content"
                                                            src="{{ $configurations['login_image'] ?? '' }}"
                                                            style="max-width: 100%; height: 100%;object-fit:contain;" />
                                                    </div>

                                                    <input type="file" name="imagen"
                                                        class="input-imagen config-input" id="login_image"
                                                        name="login_image"
                                                        value="{{ $configurations['login_image'] ?? '' }}"
                                                        data-key="login_image" data-preview-target="preview_logo_login"
                                                        hidden accept="image/*">

                                                    <div class="d-flex gap-0 mt-2">
                                                        <label class=" w-100 btn-upload btn btn-sm btn-primary mb-0"
                                                            for="login_image">Subir nueva imagen</label>

                                                    </div>

                                                    <input type="hidden" name="eliminar_imagen" id="eliminar_imagen"
                                                        value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">


                                                <div class="d-grid">
                                                    <label for="imagen" class="form-label">Fondo de inicio de
                                                        sesión:</label>
                                                    <div style="height: 142px;" id="preview_login_background"
                                                        class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-2">

                                                        <img class="preview-content"
                                                            src="{{ $configurations['login_background'] ?? '' }}"
                                                            style="max-width: 100%; height: 100%;object-fit:contain;" />
                                                    </div>

                                                    <input type="file" name="imagen"
                                                        class="input-imagen config-input" id="login_background"
                                                        name="login_background"
                                                        value="{{ $configurations['login_background'] ?? '' }}"
                                                        data-key="login_background"
                                                        data-preview-target="preview_login_background" hidden
                                                        accept="image/*">

                                                    <div class="d-flex gap-0 mt-2">
                                                        <label class=" w-100 btn-upload btn btn-sm btn-primary mb-0"
                                                            for="login_background">Subir nueva imagen</label>

                                                    </div>

                                                    <input type="hidden" name="eliminar_imagen" id="eliminar_imagen"
                                                        value="0">
                                                </div>
                                            </div>
                                        </div> <!-- .row -->
                                    </div>
                                    <div class="card-header p-2">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h5 class="mb-0">Medios del menú</h5>
                                            </div><!--end col-->
                                            <div class="col-auto d-none">
                                                <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                                    data-bs-target="#addArticulo"><i class="fas fa-plus me-1"></i>Agregar
                                                    Artículo</button>

                                            </div><!--end col-->
                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body pt-0 px-3 pb-3">
                                        <div class="row g-2">




                                            <!-- Columna 3: Imagen -->
                                            <div class="col-md-4">


                                                <div class="d-grid">
                                                    <label for="imagen" class="form-label">Logo de menu mini:</label>
                                                    <div style="height: 142px;" id="preview_logo_menu_mini"
                                                        class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-2">

                                                        <img class="preview-content"
                                                            src="{{ $configurations['logo_menu_mini'] ?? '' }}"
                                                            style="max-width: 100%; height: 100%;object-fit:contain;" />
                                                    </div>

                                                    <input type="file" name="imagen"
                                                        class="input-imagen config-input" id="logo_menu_mini"
                                                        name="logo_menu_mini"
                                                        value="{{ $configurations['logo_menu_mini'] ?? '' }}"
                                                        data-key="logo_menu_mini"
                                                        data-preview-target="preview_logo_menu_mini" hidden
                                                        accept="image/*">

                                                    <div class="d-flex gap-0 mt-2">
                                                        <label class=" w-100 btn-upload btn btn-sm btn-primary mb-0"
                                                            for="logo_menu_mini">Subir nueva imagen</label>

                                                    </div>

                                                    <input type="hidden" name="eliminar_imagen" id="eliminar_imagen"
                                                        value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">


                                                <div class="d-grid">
                                                    <label for="imagen" class="form-label">Logo de menú
                                                        expanded:</label>
                                                    <div style="height: 142px;" id="preview_logo_menu_expanded"
                                                        class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-2">

                                                        <img class="preview-content"
                                                            src="{{ $configurations['logo_menu_expanded'] ?? '' }}"
                                                            style="max-width: 100%; height: 100%;object-fit:contain;" />
                                                    </div>

                                                    <input type="file" name="imagen"
                                                        class="input-imagen config-input" id="logo_menu_expanded"
                                                        name="logo_menu_expanded"
                                                        value="{{ $configurations['logo_menu_expanded'] ?? '' }}"
                                                        data-key="logo_menu_expanded"
                                                        data-preview-target="preview_logo_menu_expanded" hidden
                                                        accept="image/*">

                                                    <div class="d-flex gap-0 mt-2">
                                                        <label class=" w-100 btn-upload btn btn-sm btn-primary mb-0"
                                                            for="logo_menu_expanded">Subir nueva imagen</label>

                                                    </div>

                                                    <input type="hidden" name="eliminar_imagen" id="eliminar_imagen"
                                                        value="0">
                                                </div>
                                            </div>
                                        </div> <!-- .row -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane p-0" id="cu_colors" role="tabpanel">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h5 class="mb-0">Color de elementos</h5>
                                            </div><!--end col-->

                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body pt-0 px-3 pb-3">
                                        <div class="row g-2">
                                            <div class="col-md-2">
                                                <label for="nombre" class="form-label">Color principal</label>
                                                <input type="color" class="config-input form-control form-control-color"
                                                    name="color_principal"
                                                    value="{{ $configurations['color_principal'] ?? '' }}"
                                                    data-key="color_principal">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="nombre" class="form-label">Color secundario</label>
                                                <input type="color" class="config-input form-control form-control-color"
                                                    name="color_secundario"
                                                    value="{{ $configurations['color_secundario'] ?? '' }}"
                                                    data-key="color_secundario">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-header p-2">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h5 class="mb-0">Colores del menú</h5>
                                            </div><!--end col-->

                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body pt-0 px-3 pb-3">
                                        <div class="row g-2">
                                            <div class="col-md-2">
                                                <label for="nombre" class="form-label">Fondo</label>
                                                <input type="color" class="config-input form-control form-control-color"
                                                    name="color_fondo_menu"
                                                    value="{{ $configurations['color_fondo_menu'] ?? '' }}"
                                                    data-key="color_fondo_menu">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="nombre" class="form-label">Enlaces</label>
                                                <input type="color" class="config-input form-control form-control-color"
                                                    name="color_enlaces_menu"
                                                    value="{{ $configurations['color_enlaces_menu'] ?? '' }}"
                                                    data-key="color_enlaces_menu">
                                            </div>
                                            <div class="col-md-2">
                                                <label for="nombre" class="form-label">Enlaces hover</label>
                                                <input type="color" class="config-input form-control form-control-color"
                                                    name="color_enlaces_menu_hover"
                                                    value="{{ $configurations['color_enlaces_menu_hover'] ?? '' }}"
                                                    data-key="color_enlaces_menu_hover">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane p-0" id="cu_logs" role="tabpanel">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h5 class="mb-0">Historial de accesos</h5>
                                            </div><!--end col-->

                                        </div><!--end row-->
                                    </div><!--end card-header-->
                                    <div class="card-body pt-0 px-3 pb-3">
                                        <div class="table-responsive">
                                            <table class="table mb-0" id="logs_table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Usuario</th>
                                                        <th>Navegador</th>
                                                        <th>Fecha </th>
                                                        <th>IP</th>


                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    @endsection
    @push('styles')
    @endpush
    @push('scripts')
        @vite(['resources/js/configuracion.js'])
    @endpush
