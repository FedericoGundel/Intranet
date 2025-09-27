@extends('layouts.app')

@section('title', 'Leyma Créditos - Dashboard')

@section('content')
    <div class="page-wrapper">
        <div class="page-content py-2">
            <div class="container-fluid">
                <!-- Header del Dashboard -->
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">Dashboard de Créditos</h4>
                                <p class="text-muted mb-0">Análisis completo del sistema de créditos</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="actualizarDashboard()">
                                    <i class="fas fa-sync-alt me-1"></i> Actualizar
                                </button>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="fas fa-calendar me-1"></i> <span id="periodo-texto">Año actual</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="cambiarPeriodo('mes')">Este
                                                mes</a></li>
                                        <li><a class="dropdown-item" href="#"
                                                onclick="cambiarPeriodo('trimestre')">Este trimestre</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="cambiarPeriodo('año')">Este
                                                año</a></li>
                                        <li><a class="dropdown-item" href="#"
                                                onclick="cambiarPeriodo('personalizado')">Personalizado</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Principales -->
                <div class="row g-2 mb-2">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Créditos Totales</p>
                                        <h4 id="creditos_totales" class="mt-1 mb-0 fw-medium">—</h4>
                                        <small class="text-muted" id="creditos_totales_trend">—</small>
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
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Créditos Activos</p>
                                        <h4 id="creditos_activos" class="mt-1 mb-0 fw-medium">—</h4>
                                        <small class="text-muted" id="creditos_activos_porcentaje">—</small>
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
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Monto Total Prestado</p>
                                        <h4 id="monto_total_prestado" class="mt-1 mb-0 fw-medium">—</h4>
                                        <small class="text-muted" id="monto_total_prestado_trend">—</small>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/wallet.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Monto Pendiente</p>
                                        <h4 id="monto_pendiente" class="mt-1 mb-0 fw-medium">—</h4>
                                        <small class="text-muted" id="monto_pendiente_porcentaje">—</small>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/debt.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segunda fila de estadísticas -->
                <div class="row g-2 mb-2">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Total Cobrado</p>
                                        <h4 id="total_cobrado" class="mt-1 mb-0 fw-medium">—</h4>
                                        <small class="text-muted" id="total_cobrado_trend">—</small>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/wallet.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Total Gastado</p>
                                        <h4 id="total_gastado" class="mt-1 mb-0 fw-medium">—</h4>
                                        <small class="text-muted" id="total_gastado_trend">—</small>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/debt.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-corner-img mb-0">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Saldo Caja</p>
                                        <h4 id="saldo_caja" class="mt-1 mb-0 fw-medium">—</h4>
                                        <small class="text-muted" id="saldo_caja_trend">—</small>
                                    </div>
                                    <div class="col-3 text-end">
                                        <lord-icon src="{{ asset('/images/lordicons/balance.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos y Análisis -->
                <div class="row g-2 mb-2">
                    <!-- Gráfico de Distribución por Tipo -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Distribución por Tipo de Crédito
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoDistribucionTipo" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de Créditos por Mes -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Créditos por Mes
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="graficoCreditosPorMes" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tablas de Información -->
                <div class="row g-2 mb-2">
                    <!-- Top 5 Clientes -->
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-trophy me-2"></i>Top 5 Clientes
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Créditos</th>
                                                <th>Monto Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaTopClientes">
                                            <tr>
                                                <td colspan="3" class="text-center">Cargando...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Gráfico de Montos por Mes -->
                <div class="row g-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-line me-2"></i>Evolución de Montos por Mes
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoMontosPorMes" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/leyma_creditos.js'])

@endsection
