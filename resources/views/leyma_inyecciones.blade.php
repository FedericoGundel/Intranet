@extends('layouts.app')

@section('title', 'Leyma Créditos - Inyecciones')

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
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Inyecciones Totales</p>
                                        <h4 id="inyecciones_totales" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/medical.json') }}" trigger="hover"
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
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Inyecciones del Mes</p>
                                        <h4 id="inyecciones_mes" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/calendar.json') }}" trigger="hover"
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
                                        <h4 id="monto_total_inyecciones" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/money.json') }}" trigger="hover"
                                            colors="primary:#17a2b8,secondary:#17a2b8"
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
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Promedio Diario</p>
                                        <h4 id="promedio_diario_inyecciones" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/trending-up.json') }}" trigger="hover"
                                            colors="primary:#6f42c1,secondary:#6f42c1"
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Lista de Inyecciones</h5>
                                <button type="button" class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                    data-bs-target="#modalInyeccion" title="Agregar">
                                    <i class="fas fa-plus me-1"></i> Nueva Inyección
                                </button>
                            </div>
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="inyecciones_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Concepto</th>
                                                <th>Monto</th>
                                                <th>Fecha</th>
                                                <th>Observaciones</th>
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
    @include('modales.leyma_inyecciones')
@endsection

@push('scripts')
    @vite(['resources/js/leyma_inyecciones.js'])
@endpush
