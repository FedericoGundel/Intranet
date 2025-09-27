@extends('layouts.app')

@section('title', 'Leyma Créditos - Préstamos Familiares')

@section('content')
    <div class="page-wrapper">
        <div class="page-content py-2">
            <div class="container-fluid">
                <!-- Estadísticas -->
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Préstamos Totales</p>
                                        <h4 id="prestamos_totales" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/home.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Préstamos Activos</p>
                                        <h4 id="prestamos_activos" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/check-circle.json') }}" trigger="hover"
                                            colors="primary:#28a745,secondary:#28a745"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Monto Total</p>
                                        <h4 id="monto_total_prestamos" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/money.json') }}" trigger="hover"
                                            colors="primary:#ffc107,secondary:#ffc107"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Préstamos Vencidos</p>
                                        <h4 id="prestamos_vencidos" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/warning.json') }}" trigger="hover"
                                            colors="primary:#dc3545,secondary:#dc3545"
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
                                        <h5 class="mb-0">Préstamos Familiares</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                            data-bs-target="#modalPrestamo"><i class="fas fa-plus me-1"></i>Agregar
                                            Préstamo</button>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="prestamos_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Familiar</th>
                                                <th>DNI</th>
                                                <th>Monto</th>
                                                <th>Fecha Préstamo</th>
                                                <th>Fecha Vencimiento</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
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
    @include('modales.leyma_prestamos_familiares')
@endsection

@push('scripts')
    @vite(['resources/js/leyma_prestamos_familiares.js'])
@endpush
