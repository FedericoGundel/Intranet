@extends('layouts.app')

@section('title', 'Inicio')


@section('content')
    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content py-2">
            <div class="container-fluid p-0">
                <div class="row g-2">
                    <div class="col-12">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Clientes totales</p>
                                        <h4 id="clientes_totales" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/users.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-0">
                    <div class="col-12">
                        <div class="card mb-0">
                            <div class="card-header p-2 p-md-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0">Clientes</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                            data-bs-target="#addClient"><i class="fas fa-plus me-1"></i>Agregar
                                            Cliente</button>
                                        <button class="d-none btn btn-sm bg-secondary text-white" id="prueba"><i
                                                class="fas fa-plus me-1"></i>Prueba</button>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-header-->
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="clientes_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>NIF/DNI</th>
                                                <th>Teléfono</th>

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

    @include('modales.clientes')

@endsection
@push('styles')
@endpush
@push('scripts')
    @vite(['resources/js/clientes.js'])
@endpush
