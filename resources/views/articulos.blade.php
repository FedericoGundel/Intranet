@extends('layouts.app')

@section('title', 'Inicio')


@section('content')
    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content py-2 ">
            <div class="container-fluid p-0">
                <div class="row g-2">

                    <div class="col-12">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Artículos totales</p>
                                        <h4 class="mt-1 mb-0 fw-medium" id="articulos_totales"></h4>
                                    </div>
                                    <!--end col-->
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/articulo.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000" style="width:50px;height:50px">
                                        </lord-icon>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div>
                    </div>

                </div><!--end row-->
                <div class="row d-none">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-1">
                            <select id="select_clientes" class="form-select form-select-sm select_clientes" style="">
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
                <div class="row g-0">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-2 p-md-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0">Artículos</h5>
                                    </div><!--end col-->
                                    <div class="col-auto">
                                        <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                            data-bs-target="#addArticulo"><i class="fas fa-plus me-1"></i>Agregar
                                            Artículo</button>

                                    </div><!--end col-->
                                </div><!--end row-->
                            </div><!--end card-header-->
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0  table-striped" id="articulos_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Stock</th>
                                                <th>Descripción</th>
                                                <th>Precio</th>

                                                <th class="text-start">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>

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

    @include('modales.articulos')


@endsection
@push('styles')
@endpush
@push('scripts')
    @vite(['resources/js/articulos.js'])
@endpush
