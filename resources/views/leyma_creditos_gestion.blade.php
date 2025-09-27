@extends('layouts.app')

@section('title', 'Leyma Créditos - Gestión de Créditos')

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
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Créditos Totales</p>
                                        <h4 id="creditos_totales" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/credit-card.json') }}" trigger="hover"
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
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Créditos Activos</p>
                                        <h4 id="creditos_activos" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/check-circle.json') }}" trigger="hover"
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
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Monto Total Prestado</p>
                                        <h4 id="monto_total_prestado" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/money.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="card m-0">
                            <div class="card-body py-2">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">Filtrar por Usuario</label>
                                        <select class="form-select form-select-sm" id="filtro_usuario">
                                            <option value="">Todos los usuarios</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">Filtrar por Estado</label>
                                        <select class="form-select form-select-sm" id="filtro_estado">
                                            <option value="">Todos los estados</option>
                                            <option value="activo">Activo</option>
                                            <option value="pagado">Pagado</option>
                                            <option value="vencido">Vencido</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label mb-1">Filtrar por Tipo</label>
                                        <select class="form-select form-select-sm" id="filtro_tipo">
                                            <option value="">Todos los tipos</option>
                                            <option value="diario">Diario</option>
                                            <option value="semanal">Semanal</option>
                                            <option value="quincenal">Quincenal</option>
                                            <option value="contado">Contado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-secondary btn-sm w-100" id="limpiar_filtros">
                                            <i class="fas fa-times me-1"></i>Limpiar Filtros
                                        </button>
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
                                        <h5 class="mb-0">Créditos</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                            data-bs-target="#modalCredito"><i class="fas fa-plus me-1"></i>Agregar
                                            Crédito</button>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="creditos_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Monto</th>
                                                <th>Tipo</th>
                                                <th>Días</th>
                                                <th>Porcentaje</th>
                                                <th>Monto a Cobrar</th>
                                                <th>Fecha Inicio</th>
                                                <th>Saldo Pendiente</th>
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

    <!-- Incluir modales -->
    @include('modales.leyma_creditos')
    @include('modales.leyma_pagos')
    @include('modales.leyma_cronograma_credito')
    @include('modales.leyma_ajustes_credito')
@endsection

@push('scripts')
    @vite(['resources/js/leyma_creditos_gestion.js'])
@endpush
