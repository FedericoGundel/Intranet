@extends('layouts.app')

@section('title', 'Inicio')


@section('content')
    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content py-2">
            <div class="container-fluid p-0">

                <div class="row g-2">
                    <!-- Card Caja Total -->
                    <div class="col-lg-4">
                        <div class="card bg-globe-img mb-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fs-16 fw-semibold"> <span class="fs-24">Caja</span> <br>
                                                (Todos los tiempos)</span>
                                        </div>

                                        <h4 class="my-2 fs-24 fw-semibold">
                                            €{{ number_format($totalCobros - $totalGastos, 2, ',', '.') }}
                                        </h4>

                                        <p class="mb-0 text-muted fw-semibold">
                                            <i class="las la-calculator"></i> Balance total
                                        </p>
                                    </div>
                                    <div class="col d-flex justify-content-end align-items-center">
                                        <lord-icon src="{{ asset('/images/lordicons/char.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:70%;height:70%"></lord-icon>
                                    </div>
                                </div>
                            </div><!--end card-body-->
                        </div>
                    </div>

                    <!-- Card Gastos Totales -->
                    <div class="col-lg-4">
                        <div class="card bg-danger-subtle mb-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fs-16 fw-semibold"> <span class="fs-24">Gastos Totales</span> <br>
                                                (Todos los tiempos)</span>
                                        </div>

                                        <h4 class="my-2 fs-24 fw-semibold text-danger">
                                            €{{ number_format($totalGastos, 2, ',', '.') }}
                                        </h4>

                                        <p class="mb-0 text-muted fw-semibold">
                                            <i class="las la-arrow-down text-danger"></i> Total gastado
                                        </p>
                                    </div>
                                    <div class="col d-flex justify-content-end align-items-center">
                                        <lord-icon src="{{ asset('/images/lordicons/spend.json') }}" trigger="hover"
                                            colors="primary:#dc3545,secondary:#dc3545"
                                            style="width:70%;height:70%"></lord-icon>
                                    </div>
                                </div>
                            </div><!--end card-body-->
                        </div>
                    </div>

                    <!-- Card Cobros Totales -->
                    <div class="col-lg-4">
                        <div class="card bg-success-subtle mb-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fs-16 fw-semibold"> <span class="fs-24">Cobros Totales</span> <br>
                                                (Todos los tiempos)</span>
                                        </div>

                                        <h4 class="my-2 fs-24 fw-semibold text-success">
                                            €{{ number_format($totalCobros, 2, ',', '.') }}
                                        </h4>

                                        <p class="mb-0 text-muted fw-semibold">
                                            <i class="las la-arrow-up text-success"></i> Total cobrado
                                        </p>
                                    </div>
                                    <div class="col d-flex justify-content-end align-items-center">
                                        <lord-icon src="{{ asset('/images/lordicons/paid.json') }}" trigger="hover"
                                            colors="primary:#198754,secondary:#198754"
                                            style="width:70%;height:70%"></lord-icon>
                                    </div>
                                </div>
                            </div><!--end card-body-->
                        </div>
                    </div>
                </div>

                <div class="row g-2 justify-content-center">

                    <div class="col-md-12 col-lg-8 mb-2">
                        <div class="card mb-0 h-100">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Ventas</h4>
                                    </div>
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm bt btn-light dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icofont-calendar fs-5 me-1"></i>
                                                <span id="intervalo-texto">Año actual</span>
                                                <i class="las la-angle-down ms-1"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end" id="intervalos">
                                                <a class="dropdown-item" href="#" data-intervalo="semana">Última
                                                    semana</a>
                                                <a class="dropdown-item" href="#" data-intervalo="mes">Mes actual</a>
                                                <a class="dropdown-item" href="#" data-intervalo="anio">Año actual</a>
                                                <a class="dropdown-item" href="#" data-intervalo="rango">Rango de
                                                    fechas</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-header-->
                            <div class="card-body py-0">
                                <div id="reports2" class="apex-charts pill-bar"></div>
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                    <div class="col-md-12 col-lg-4 mb-2">
                        <div class="card mb-0 h-100">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Caja</h4>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm bt btn-light dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icofont-calendar fs-5 me-1"></i>
                                                <span id="intervalo-texto2">Año actual</span>
                                                <i class="las la-angle-down ms-1"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end" id="intervalos2">
                                                <a class="dropdown-item" href="#" data-intervalo="semana">Última
                                                    semana</a>
                                                <a class="dropdown-item" href="#" data-intervalo="mes">Mes
                                                    actual</a>
                                                <a class="dropdown-item" href="#" data-intervalo="anio">Año
                                                    actual</a>
                                                <a class="dropdown-item" href="#" data-intervalo="rango">Rango de
                                                    fechas</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-header-->
                            <div class="card-body pt-0 text-center">
                                <div id="grafico_caja"
                                    class="apex-charts d-flex justify-content-center align-items-center h-100"></div>


                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->

                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-3 order-2 order-lg-1">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Balance Details</h4>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <div class="p-2 border-dashed border-theme-color rounded">
                                            <h5 class="mt-1 mb-0 fw-medium">$82365.00</h5>
                                            <small class="text-muted">Available</small>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <div class="card-body pt-0">
                                <div id="balance" class="apex-charts"></div>
                                <div class="bg-light py-3 px-2 mb-0 mt-3 text-center rounded">
                                    <h6 class="mb-0"><i class="icofont-calendar fs-5 me-1"></i> 01 January 2024 to 31
                                        December 2024</h6>
                                </div>
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                    <div class="col-md-12 col-lg-6 order-1 order-lg-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Transaction History</h4>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm bt btn-light dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icofont-calendar fs-5 me-1"></i> This Month<i
                                                    class="las la-angle-down ms-1"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Today</a>
                                                <a class="dropdown-item" href="#">Last Week</a>
                                                <a class="dropdown-item" href="#">Last Month</a>
                                                <a class="dropdown-item" href="#">This Year</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-header-->
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="border-top-0">Transaction</th>
                                                <th class="border-top-0">Date</th>
                                                <th class="border-top-0">AApprox</th>
                                                <th class="border-top-0">Status</th>
                                                <th class="border-top-0">Action</th>
                                            </tr>
                                            <!--end tr-->
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/logos/lang-logo/chatgpt.png') }}"
                                                            height="40" class="me-3 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0">Chat Gpt</h6>
                                                            <a href="#" class="fs-12 text-primary">ID: A3652</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                </td>
                                                <td>20 july 2024</td>
                                                <td>$560</td>
                                                <td><span
                                                        class="badge bg-success-subtle text-success fs-11 fw-medium px-2">Successful</span>
                                                </td>
                                                <td>
                                                    <a href="#"><i
                                                            class="las la-print text-secondary fs-18"></i></a>
                                                    <a href="#"><i
                                                            class="las la-trash-alt text-secondary fs-18"></i></a>
                                                </td>
                                            </tr>
                                            <!--end tr-->
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/logos/lang-logo/gitlab.png') }}"
                                                            height="40" class="me-3 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0">Gitlab</h6>
                                                            <a href="#" class="fs-12 text-primary">ID: B5784</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                </td>
                                                <td>09 July 2024</td>
                                                <td>$2350</td>
                                                <td><span
                                                        class="badge bg-warning-subtle text-warning fs-11 fw-medium px-2">Pending</span>
                                                </td>
                                                <td>
                                                    <a href="#"><i
                                                            class="las la-print text-secondary fs-18"></i></a>
                                                    <a href="#"><i
                                                            class="las la-trash-alt text-secondary fs-18"></i></a>
                                                </td>
                                            </tr>
                                            <!--end tr-->
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/logos/lang-logo/nextjs.png') }}"
                                                            height="40" class="me-3 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0">Nextjs</h6>
                                                            <a href="#" class="fs-12 text-primary">ID: C9632</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                </td>
                                                <td>02 June 2024</td>
                                                <td>$2200</td>
                                                <td><span
                                                        class="badge bg-success-subtle text-success fs-11 fw-medium px-2">Successful</span>
                                                </td>
                                                <td>
                                                    <a href="#"><i
                                                            class="las la-print text-secondary fs-18"></i></a>
                                                    <a href="#"><i
                                                            class="las la-trash-alt text-secondary fs-18"></i></a>
                                                </td>
                                            </tr>
                                            <!--end tr-->
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/logos/lang-logo/vue.png') }}"
                                                            height="40" class="me-3 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0">Vue</h6>
                                                            <a href="#" class="fs-12 text-primary">ID: D8596</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                </td>
                                                <td>28 MAY 2024</td>
                                                <td>$1320</td>
                                                <td><span
                                                        class="badge bg-danger-subtle text-danger fs-11 fw-medium px-2">Cancle</span>
                                                </td>
                                                <td>
                                                    <a href="#"><i
                                                            class="las la-print text-secondary fs-18"></i></a>
                                                    <a href="#"><i
                                                            class="las la-trash-alt text-secondary fs-18"></i></a>
                                                </td>
                                            </tr>
                                            <!--end tr-->
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/logos/lang-logo/symfony.png') }}"
                                                            height="40" class="me-3 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0">Symfony</h6>
                                                            <a href="#" class="fs-12 text-primary">ID: E7778</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                </td>
                                                <td>15 May 2024</td>
                                                <td>$3650</td>
                                                <td><span
                                                        class="badge bg-success-subtle text-success fs-11 fw-medium px-2">Successful</span>
                                                </td>
                                                <td>
                                                    <a href="#"><i
                                                            class="las la-print text-secondary fs-18"></i></a>
                                                    <a href="#"><i
                                                            class="las la-trash-alt text-secondary fs-18"></i></a>
                                                </td>
                                            </tr>
                                            <!--end tr-->
                                        </tbody>
                                    </table>
                                    <!--end table-->
                                </div>
                                <!--end /div-->
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                    <div class="col-md-6 col-lg-3 order-3 order-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Send Money</h4>
                                    </div>
                                    <!--end col-->
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm bt btn-light">
                                                <i class="icofont-contact-add fs-5 me-1"></i> Add Member
                                            </a>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-header-->
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <tbody>
                                            <tr class="">
                                                <td class="px-0">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/users/avatar-1.jpg') }}"
                                                            height="36" class="me-2 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0 text-truncate">Scott Holland</h6>
                                                            <a href="#"
                                                                class="font-12 text-muted text-decoration-underline">#3652</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                    <!--end media-->
                                                </td>
                                                <td class="px-0 text-end"><span
                                                        class="text-primary ps-2 align-self-center text-end fw-medium">$3325.00</span>
                                                </td>
                                                <td class="px-0 text-end"><a href="#" class="text-body"><i
                                                            class="las la-sync-alt"></i></a></td>
                                            </tr>
                                            <!--end tr-->
                                            <tr class="">
                                                <td class="px-0">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/users/avatar-2.jpg') }}"
                                                            height="36" class="me-2 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0 text-truncate">Karen Savage</h6>
                                                            <a href="#"
                                                                class="font-12 text-muted text-decoration-underline">#4789</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                    <!--end media-->
                                                </td>
                                                <td class="px-0 text-end"><span
                                                        class="text-primary ps-2 align-self-center text-end fw-medium">$2548.00</span>
                                                </td>
                                                <td class="px-0 text-end"><a href="#" class="text-body"><i
                                                            class="las la-sync-alt"></i></a></td>
                                            </tr>
                                            <!--end tr-->
                                            <tr class="">
                                                <td class="px-0">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/users/avatar-3.jpg') }}"
                                                            height="36" class="me-2 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0 text-truncate">Steven Sharp </h6>
                                                            <a href="#"
                                                                class="font-12 text-muted text-decoration-underline">#4521</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                    <!--end media-->
                                                </td>
                                                <td class="px-0 text-end"><span
                                                        class="text-primary ps-2 align-self-center text-end fw-medium">$2985.00</span>
                                                </td>
                                                <td class="px-0 text-end"><a href="#" class="text-body"><i
                                                            class="las la-sync-alt"></i></a></td>
                                            </tr>
                                            <!--end tr-->
                                            <tr class="">
                                                <td class="px-0">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/users/avatar-4.jpg') }}"
                                                            height="36" class="me-2 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0 text-truncate">Teresa Himes </h6>
                                                            <a href="#"
                                                                class="font-12 text-muted text-decoration-underline">#3269</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                    <!--end media-->
                                                </td>
                                                <td class="px-0 text-end"><span
                                                        class="text-primary ps-2 align-self-center text-end fw-medium">$1845.00</span>
                                                </td>
                                                <td class="px-0 text-end"><a href="#" class="text-body"><i
                                                            class="las la-sync-alt"></i></a></td>
                                            </tr>
                                            <!--end tr-->
                                            <tr>
                                                <td class="px-0">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/users/avatar-5.jpg') }}"
                                                            height="36" class="me-2 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0 text-truncate">Ralph Denton</h6>
                                                            <a href="#"
                                                                class="font-12 text-muted text-decoration-underline">#4521</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                    <!--end media-->
                                                </td>
                                                <td class="px-0 text-end"><span
                                                        class="text-primary ps-2 align-self-center text-end fw-medium">$1422.00</span>
                                                </td>
                                                <td class="px-0 text-end"><a href="#" class="text-body"><i
                                                            class="las la-sync-alt"></i></a></td>
                                            </tr>
                                            <!--end tr-->
                                            <tr class="">
                                                <td class="px-0">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('images/users/avatar-9.jpg') }}"
                                                            height="36" class="me-2 align-self-center rounded"
                                                            alt="...">
                                                        <div class="flex-grow-1 text-truncate">
                                                            <h6 class="m-0 text-truncate">Steven Sharp </h6>
                                                            <a href="#"
                                                                class="font-12 text-muted text-decoration-underline">#4521</a>
                                                        </div>
                                                        <!--end media body-->
                                                    </div>
                                                    <!--end media-->
                                                </td>
                                                <td class="px-0 text-end"><span
                                                        class="text-primary ps-2 align-self-center text-end fw-medium">$2985.00</span>
                                                </td>
                                                <td class="px-0 text-end"><a href="#" class="text-body"><i
                                                            class="las la-sync-alt"></i></a></td>
                                            </tr>
                                            <!--end tr-->
                                        </tbody>
                                    </table>
                                    <!--end table-->
                                </div>
                                <!--end /div-->
                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div><!-- container -->

            <!--Start Rightbar-->
            <!--Start Rightbar/offcanvas-->


            <!--end footer-->
        </div>
        <!-- end page content -->
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>

    <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="https://apexcharts.com/samples/{{ asset('stock-prices.js') }}"></script>

    <script src="{{ asset('js/DynamicSelect.js') }}"></script>

    <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="https://apexcharts.com/samples/assets/irregular-data-series.js"></script>
    <script src="https://apexcharts.com/samples/assets/ohlc.js"></script>
    @vite(['resources/js/estadisticas.js'])
@endpush
