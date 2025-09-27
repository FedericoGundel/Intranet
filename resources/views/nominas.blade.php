@extends('layouts.app')

@section('title', 'Inicio')


@section('content')
    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content py-2 ">
            <div class="container-fluid p-0">
                <div class="row g-2">

                    <div class="col-lg-6">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Vendedores totales</p>
                                        <h4 class="mt-1 mb-0 fw-medium"id="vendedores_totales"></h4>
                                    </div>
                                    <!--end col-->
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/vendedores.json') }}" trigger="hover"
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
                    <div class="col-lg-6">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Saldo pendiente</p>
                                        <h4 class="mt-1 mb-0 fw-medium" id="saldo_pendiente">
                                        </h4>
                                    </div>
                                    <!--end col-->
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/deuda.json') }}" trigger="hover"
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

                </div>
                <!--end row-->

                <div class="row g-0">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-2 p-md-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0">Empleados</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm bg-primary text-white"
                                            data-bs-toggle="modal" data-bs-target="#addEmpleado" title="Agregar">
                                            <i class="fas fa-plus me-1"></i> Agregar Empleado
                                        </button>
                                        <button type="button" class="btn btn-sm bg-success text-white"
                                            data-bs-toggle="modal" data-bs-target="#calcularNominas" title="Agregar">
                                            <i class="fas fa-plus me-1"></i> Calcular nóminas
                                        </button>
                                    </div>
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-header-->
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="empleados_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Apellido</th>
                                                <th>Categoría</th>
                                                <th>DNI</th>
                                                <th>Email</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                                <th>Fecha de ingreso</th>
                                                <th class="text-start">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se llena dinámicamente con DataTables o JS -->
                                        </tbody>
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
    <!-- Modal -->
    <div class="modal fade" id="modalVerEmpleado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-0" style="height: 80vh;">
                    <iframe id="iframeEmpleado" src="" width="100%" height="100%" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>



    @include('modales.empleados')
    @include('modales.facturas')
@endsection
@push('styles')
@endpush
@push('scripts')
    @vite(['resources/js/empleados.js'])
    @vite(['resources/js/facturas.js'])
    @vite(['resources/js/cobros.js'])
@endpush
