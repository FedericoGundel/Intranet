@extends('layouts.app')

@section('title', 'Inicio')


@section('content')

    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content py-2">
            <div class="container-fluid p-0">
                <div class="row g-2">
                    <div class="col-lg-4">
                        <div class="nav-tabs-custom text-start ">
                            <ul class="nav nav-tabs mb-2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-center active" data-bs-toggle="tab" href="#cu_home"
                                        role="tab" aria-selected="false" tabindex="-1"><i
                                            class="las la-address-card d-block"></i>Cliente</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-center " data-bs-toggle="tab" href="#cu_info" role="tab"
                                        aria-selected="true"><i class="las la-file-invoice d-block"></i>Información</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-center" data-bs-toggle="tab" href="#cu_articulos" role="tab"
                                        aria-selected="false" tabindex="-1"><i
                                            class="las la-couch d-block"></i>Artículos</a>
                                </li>
                            </ul>
                            <form id="formAddFactura" action="" method="POST">
                                <div class="tab-content">
                                    <div class="tab-pane p-0 active show" id="cu_home" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header p-2 p-md-2">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <h5 class="mb-0">Datos del cliente</h5>
                                                    </div>
                                                    <!--end col-->

                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end card-header-->

                                            <div class="card-body pt-0 px-2 pb-2 px-md-2 pb-md-2">

                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <label for="nombre"
                                                            class="form-label p-0 col-form-label-sm">Selecciona un
                                                            cliente</label>
                                                        <div class="d-flex align-items-center gap-1 ">
                                                            <select id="select_clientes_factura" name="id_cliente"
                                                                class="form-select form-select-sm  select_clientes"
                                                                style="">
                                                                <option value="">Seleccione un cliente</option>
                                                                <!-- opciones dinámicas -->
                                                            </select>

                                                            <button type="button" class="btn btn-sm btn-success btn-sm"
                                                                data-bs-toggle="modal" data-bs-target="#addClient"
                                                                title="Agregar">
                                                                <i class="bi bi-plus"></i>
                                                            </button>

                                                            <button type="button" data-source="select_clientes_factura"
                                                                class="btn btn-sm btn-primary btn-sm btn_editar_cliente"
                                                                title="Editar">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>

                                                            <button type="button" data-source="select_clientes_factura"
                                                                class="btn btn-sm btn-danger btn-sm btn_eliminar_cliente"
                                                                title="Eliminar">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Nombre</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="nombre">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Primer
                                                                apellido</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="apellido1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Segundo
                                                                apellido</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="apellido2">
                                                        </div>
                                                    </div>
                                                    <!-- Columna 1 -->
                                                    <div class="col-md-6">


                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Teléfono</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="telefono">
                                                        </div>


                                                    </div>
                                                    <div class="col-md-6">


                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Email</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="email">
                                                        </div>


                                                    </div>
                                                    <div class="col-md-6">



                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Tipo de
                                                                cliente</label>
                                                            <select class="form-select form-select-sm campo_factura"
                                                                name="tipo_cliente">
                                                                <option value="particular">Particular</option>
                                                                <option value="autonomo">Autónomo</option>
                                                                <option value="empresa">Empresa</option>


                                                            </select>
                                                        </div>

                                                    </div>
                                                    <!-- Columna 2 -->
                                                    <div class="col-md-6">

                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">NIF</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="nif">
                                                        </div>



                                                    </div>
                                                    <div class="col-md-6">



                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Código
                                                                postal</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="codigo_postal">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">País</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="pais">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">




                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Provincia</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="provincia">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">



                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Localidad</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="localidad">
                                                        </div>
                                                    </div>
                                                </div> <!-- .row -->
                                            </div>

                                        </div>
                                    </div>
                                    <div class="tab-pane p-0" id="cu_info" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header p-2 p-md-2">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <h5 class="mb-0">Información de la factura</h5>
                                                    </div>
                                                    <!--end col-->

                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end card-header-->

                                            <div class="card-body pt-0 px-2 pb-2 px-md-2 pb-md-2">

                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <label for="nombre"
                                                            class="form-label p-0 col-form-label-sm">Selecciona un
                                                            vendedor</label>
                                                        <div class="d-flex align-items-center gap-1 ">
                                                            <select class="form-select form-select-sm select_empleados"
                                                                data-tipo="vendedor" name="vendedor_id">
                                                                <option value="">Seleccione un vendedor</option>
                                                                <!-- opciones dinámicas -->
                                                            </select>

                                                            <button type="button" class="btn btn-sm btn-success btn-sm"
                                                                data-bs-toggle="modal" data-bs-target="#addEmpleado"
                                                                title="Agregar">
                                                                <i class="bi bi-plus"></i>
                                                            </button>

                                                            <button type="button" data-source="select_empleados_factura"
                                                                class="btn btn-sm btn-primary btn-sm btn_editar_empleado"
                                                                title="Editar">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>

                                                            <button type="button" data-source="select_empleados_factura"
                                                                class="btn btn-sm btn-danger btn-sm btn_eliminar_empleado"
                                                                title="Eliminar">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-12  d-none">
                                                        <div class="d-grid">
                                                            <label for="imagen"
                                                                class="form-label p-0 col-form-label-sm">Imagen:</label>
                                                            <div style="height: 142px;" id="preview_imagen_factura"
                                                                class="preview-box d-flex justify-content-center rounded border-dashed border-theme-color overflow-hidden p-2">
                                                            </div>

                                                            <input type="file" class="input-imagen"
                                                                id="imagen_factura"
                                                                data-preview-target="preview_imagen_factura" hidden
                                                                accept="image/*">

                                                            <div class="d-flex gap-2 mt-2">
                                                                <label class="btn-upload btn btn-sm btn-primary mb-0"
                                                                    for="imagen_factura">Subir nueva imagen</label>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger mb-0 btn-eliminar-imagen">Eliminar
                                                                    imagen</button>
                                                            </div>

                                                            <input type="hidden" name="eliminar_imagen"
                                                                id="eliminar_imagen" value="0">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Número de
                                                                factura</label>
                                                            <input type="text"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="numero">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Fecha</label>
                                                            <input type="date"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="fecha">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Fecha de
                                                                vencimiento</label>
                                                            <input type="date"
                                                                class="form-control form-control-sm  campo_factura"
                                                                name="fecha_vencimiento">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="nombre"
                                                            class="form-label p-0 col-form-label-sm">Marcar como
                                                            señada</label>
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-text">
                                                                <div class="form-check m-0">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="senar">

                                                                </div>
                                                            </div>
                                                            <input name="sena_factura_porcentaje"
                                                                class="form-control form-control-sm" type="number"
                                                                step="0.01" min="0.01"value="25.00">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                    <!-- Columna 1 -->
                                                    <div class="col-12">


                                                        <div class="mb-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Términos y
                                                                condiciones</label>
                                                            <textarea style="height: 193px;" class="form-control form-control-sm  campo_factura" name="condiciones"
                                                                rows="6"></textarea>
                                                        </div>


                                                    </div>

                                                </div> <!-- .row -->
                                            </div>

                                        </div>
                                    </div>
                                    <div class="tab-pane p-0" id="cu_articulos" role="tabpanel">
                                        <div class="card">
                                            <div class="card-header p-2 p-md-2">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <h5 class="mb-0">Artículos</h5>
                                                    </div>
                                                    <!--end col-->

                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end card-header-->

                                            <div class="card-body pt-0 px-2 pb-2 px-md-2 pb-md-2">

                                                <div class="row g-2">

                                                    <div class="col-12">
                                                        <label for="nombre"
                                                            class="form-label p-0 col-form-label-sm">Selecciona un
                                                            artículo</label>
                                                        <div class="d-flex align-items-center gap-1 ">
                                                            <select id="select_articulos_factura"
                                                                class="form-select form-select-sm select_articulos"
                                                                style="">
                                                                <option value="">Seleccione un artículo</option>
                                                                <!-- opciones dinámicas -->
                                                            </select>

                                                            <button type="button" class="btn btn-sm btn-success btn-sm"
                                                                data-bs-toggle="modal" data-bs-target="#addArticulo"
                                                                title="Agregar">
                                                                <i class="bi bi-plus"></i>
                                                            </button>

                                                            <button type="button" data-source="select_articulos_factura"
                                                                class="btn btn-sm btn-primary btn-sm btn_editar_articulo"
                                                                title="Editar">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>

                                                            <button type="button" data-source="select_articulos_factura"
                                                                class="btn btn-sm btn-danger btn-sm btn_eliminar_articulo"
                                                                title="Eliminar">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Nombre*</label>
                                                            <input type="text" class="form-control form-control-sm "
                                                                id="nombre_articulo">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Cantidad*</label>
                                                            <input type="number" class="form-control form-control-sm "
                                                                id="cantidad_articulo" value=1 required min="1"
                                                                step="1" placeholder="Ej: 1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Precio
                                                                unitario*</label>
                                                            <input type="number" class="form-control form-control-sm "
                                                                step="0.01" id="precio_unitario_articulo">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Descuento*</label>
                                                            <input type="number" class="form-control form-control-sm "
                                                                step="0.01" id="descuento_articulo">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class=" mb-md-0">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Impuesto</label>
                                                            <select class="form-select form-select-sm"
                                                                id="impuesto_articulo">
                                                                <option value="0.00">0.00%</option>
                                                                <option value="7.00">7.00%</option>
                                                                <option value="21.00">21.00%</option>



                                                            </select>
                                                        </div>
                                                    </div>
                                                    <!-- Columna 1 -->
                                                    <div class="col-12">


                                                        <div class="">
                                                            <label for="nombre"
                                                                class="form-label p-0 col-form-label-sm">Descripción</label>
                                                            <textarea style="height: 100px;" class="form-control form-control-sm " id="descripcion_articulo" rows="6"></textarea>
                                                        </div>


                                                    </div>
                                                    <div class="col-12">


                                                        <div class=" text-end">
                                                            <button id="btn_add_articulo"
                                                                class="btn btn-sm bg-success text-white"><i
                                                                    class="fas fa-plus me-1"></i>Agregar</button>
                                                        </div>


                                                    </div>

                                                </div> <!-- .row -->
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row g-2">

                            <div class="col-12">
                                <div class="card" id="factura_html">
                                    <div class="card-body bg-black rounded-top">
                                        <div class="row">
                                            <div class="col-4 align-self-center">
                                                <img style="max-height: 50px;" src="{{ $configurations['login_image'] }}"
                                                    alt="logo-pequeño" class="logo-sm me-1" height="70">
                                            </div>
                                            <!--end col-->
                                            <div class="col-8 text-end align-self-center">
                                                <h6 class="mb-1 fw-semibold text-white"><span
                                                        class="text-muted">Factura:</span> <span name="numero"></span>
                                                </h6>
                                                <h6 class="mb-1 fw-semibold text-white"><span class="text-muted">Fecha de
                                                        emisión:</span> <span name="fecha"></span></h6>
                                                <h6 class="mb-0 fw-semibold text-white"><span class="text-muted">Fecha de
                                                        vencimiento:</span> <span name="fecha_vencimiento"></span></h6>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </div>
                                    <!--end card-body-->
                                    <div class="card-body">
                                        <div class="row g-4">
                                            <div class="col-6">
                                                <div class="">

                                                    <h5 class="my-1 fw-semibold fs-24">
                                                        {{ $configurations['razon_social'] }}
                                                    </h5>
                                                    <p class="text-muted mb-0">NIF: {{ $configurations['nif'] }}</p>
                                                </div>
                                                <div class="">
                                                    <address class="fs-13">


                                                        <strong>Dirección:</strong>
                                                        <span>{{ $configurations['direccion'] }} </span>
                                                        <span>{{ $configurations['ciudad'] }} </span>
                                                        <span>{{ $configurations['provincia'] }}</span>
                                                        <span>{{ $configurations['pais'] }}</span>
                                                        <br>
                                                        <strong>Teléfono:
                                                        </strong><span>{{ $configurations['telefono'] }}</span><br>
                                                        <strong>Código postal:
                                                        </strong><span>{{ $configurations['codigo_postal'] }}</span><br>
                                                        <strong>Email: </strong><span>{{ $configurations['email'] }}</span>
                                                        <br>
                                                        <strong>Sitio web:
                                                        </strong><span>{{ $configurations['pagina_web'] }}</span>

                                                    </address>
                                                </div>
                                            </div>
                                            <!--end col-->

                                            <div class="col-6">
                                                <div class="">
                                                    <span class="badge rounded text-dark bg-light">Factura para</span>
                                                    <h5 class="my-1 fw-semibold fs-18">
                                                        <span name="nombre"></span>
                                                        <span name="apellido1"></span>
                                                        <span name="apellido2"></span>
                                                    </h5>
                                                    <p class="text-muted mb-0">NIF: <span name="nif"></span> </p>

                                                    <div class="">
                                                        <address class="fs-13">

                                                            <strong>Dirección:</strong>
                                                            <span name="direccion"></span>
                                                            <span name="localidad"></span>
                                                            <span name="provincia"></span>
                                                            <span name="pais"></span>
                                                            <br>
                                                            <strong>Teléfono:</strong><span name="telefono"></span><br>
                                                            <strong>Email:</strong><span name="email"></span><br>
                                                            <strong>Código postal:</strong><span
                                                                name="codigo_postal"></span><br>
                                                            <strong>Dirección:</strong><span name="direccion"></span><br>


                                                        </address>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->




                                        <div class="row g-0">
                                            <div class="col-lg-12">
                                                <div class="table-responsive project-invoice">
                                                    <table id="tabla_articulos_factura" class="table mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Artículo</th>
                                                                <th>Cantidad</th>
                                                                <th>Precio unitario</th>
                                                                <th>Descuento</th>
                                                                <th>Impuesto</th>
                                                                <th>Total</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                            <!--end tr-->
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                    <!--end table-->
                                                </div>
                                                <!--end /div-->
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->

                                        <div class="row mt-3">
                                            <div class="col-lg-6">
                                                <h5 class="">Términos y Condiciones:</h5>
                                                <small name="condiciones"></small>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <h6 class="mb-1 text-end">Base imponible: <span
                                                        id="base_imponible"></span>
                                                </h6>
                                                <h6 class="mb-1 text-end">Impuestos agregados: <span
                                                        id="impuestos_agregados"></span></h6>
                                                <h5 class="mb-0 text-end">Total: <span id="total_final"></span></h5>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                        <hr>
                                        <div class="row d-flex justify-content-center">

                                            <div class="col-lg-12">
                                                <div class="float-end d-print-none mt-2 mt-md-0">

                                                    <button type="submit" data-enviar="0" form="formAddFactura"
                                                        class="btn_guardar btn btn-sm btn-primary">Guardar</button>
                                                    <button type="submit" data-enviar="1" form="formAddFactura"
                                                        class="btn_guardar btn btn-sm btn-danger">Guardar y enviar</button>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </div>
                                </div>

                            </div> <!-- end col -->

                        </div> <!-- .row -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modales.clientes')
    @include('modales.articulos')
    @include('modales.empleados')

@endsection
@push('styles')
@endpush
@push('scripts')
    @vite(['resources/js/clientes.js'])
    @vite(['resources/js/empleados.js'])
    @vite(['resources/js/articulos.js'])
    @vite(['resources/js/crear_factura.js'])
@endpush
