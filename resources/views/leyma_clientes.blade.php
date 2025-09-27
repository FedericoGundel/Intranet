@extends('layouts.app')

@section('title', 'Leyma Créditos - Clientes')

@section('content')
    <div class="page-wrapper">
        <div class="page-content py-2">
            <div class="container-fluid">
                <!-- Estadísticas -->
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Clientes Totales</p>
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

                    <div class="col-md-4">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Con Garante</p>
                                        <h4 id="clientes_con_garante" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/user_check.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Con Créditos</p>
                                        <h4 id="clientes_con_creditos" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/client-credit.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-2 p-md-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0">Clientes</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                            data-bs-target="#modalCliente"><i class="fas fa-plus me-1"></i>Agregar
                                            Cliente</button>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="clientes_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>DNI</th>
                                                <th>Teléfono</th>
                                                <th>Domicilio</th>
                                                <th>Comercio</th>
                                                <th>Garante</th>
                                                <th>Créditos Activos</th>
                                                <th>Múltiples Créditos</th>
                                                <th>Fecha Registro</th>
                                                <th class="text-start">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se llena dinámicamente con DataTables -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir modal -->
    @include('modales.leyma_clientes')
@endsection

@push('scripts')
    @vite(['resources/js/leyma_clientes.js'])
@endpush
