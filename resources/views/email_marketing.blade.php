@extends('layouts.app')

@section('title', 'Inicio')


@section('content')

    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content py-2">
            <div class="container-fluid p-0">

                <div class="row d-none">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-1">
                            <select id="select_clientes" class="form-select select_clientes" style="">
                                <option value="">Seleccione un cliente</option>
                                <!-- opciones dinámicas -->
                            </select>

                            <button type="button" class="btn btn-sm btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addClient" title="Agregar">
                                <i class="bi bi-plus"></i>
                            </button>

                            <button type="button" data-source="select_clientes"
                                class="btn btn-sm btn-primary btn-sm btn_editar_cliente" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button type="button" data-source="select_clientes"
                                class="btn btn-sm btn-danger btn-sm btn_eliminar_cliente" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-xl-6">
                        <div class="card mb-0">
                            <div class="card-header p-2 p-md-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0">Clientes</h5>
                                    </div><!--end col-->

                                </div><!--end row-->
                            </div><!--end card-header-->
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="email_marketing_table">
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
                                                <th>Email</th>
                                                <th>NIF/DNI</th>

                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->

                    <div class="col-xl-6">
                        <div class="card mb-0">
                            <div class="card-header p-2 p-md-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0">Redactar email</h5>
                                    </div><!--end col-->

                                </div><!--end row-->
                            </div><!--end card-header-->
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="d-flex align-items-center gap-1 mb-2">
                                    <select id="select_plantilla_email" class="form-select select_plantilla_email"
                                        style="">
                                        <option value="">Selecciona una plantilla de email</option>
                                        <!-- opciones dinámicas -->
                                    </select>

                                    <button type="button" class="btn btn-sm btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#addPlantillaEmail" title="Agregar">
                                        <i class="bi bi-plus"></i>
                                    </button>

                                    <button type="button" data-source="select_plantilla_email"
                                        class="btn btn-sm btn-primary btn-sm btn_editar_plantilla_email" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button type="button" data-source="select_plantilla_email"
                                        class="btn btn-sm btn-danger btn-sm btn_eliminar_plantilla_email" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="mb-2">
                                    <label for="nombre" class="form-label p-0 col-form-label-sm">Asunto*</label>
                                    <input type="text" class="form-control form-control-sm " id="asunto_email_marketing"
                                        required>
                                </div>
                                <div class="mb-2">
                                    <label for="nombre" class="form-label p-0 col-form-label-sm">Cuerpo*</label>
                                    <textarea class="mb-2" id="editor_email_marketing" name="contenido"></textarea>
                                </div>
                                <div class="mb-2">
                                    <div id="files_email_marketing"></div>
                                </div>
                                <div class="mb-0">

                                    <button type="button" id="btn_enviar_email_marketing"
                                        class=" w-100 btn btn-sm btn-success">Comenzar envío</button>
                                </div>

                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

            </div><!-- container -->

            <!--Start Rightbar-->
            <!--Start Rightbar/offcanvas-->


            <!--end footer-->
        </div>
        <!-- end page content -->
    </div>

    @include('modales.clientes')
    @include('modales.plantilla_emails')
@endsection
@push('styles')
    <link href="{{ asset('libs/uppy/uppy.min.css') }}" rel="stylesheet" type="text/css" />
@endpush
@push('scripts')
    <script src="{{ asset('libs/uppy/uppy.legacy.min.js') }}"></script>
    <script src="https://releases.transloadit.com/uppy/locales/v3.0.0/es_ES.min.js"></script>
    @vite(['resources/js/clientes.js'])

    @vite(['resources/js/email_marketing.js'])

    @vite(['resources/js/plantilla_emails.js'])
@endpush
