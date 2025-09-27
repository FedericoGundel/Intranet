@extends('layouts.app')

@section('title', 'Inicio')


@section('content')
    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content py-2 ">
            <div class="container-fluid p-0">
                <div class="row g-2">
                    <div class="col-lg-4">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Usuarios totales</p>
                                        <h4 id="card_total" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 align-self-center">
                                        <lord-icon src="{{ asset('/images/lordicons/users.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Usuarios aprobados</p>
                                        <h4 id="card_aprobados" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 align-self-center">
                                        <lord-icon src="{{ asset('/images/lordicons/user_a.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card bg-corner-img mb-2">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-9">
                                        <p class="text-muted text-uppercase mb-0 fw-normal fs-13">Usuarios pendientes</p>
                                        <h4 id="card_pendientes" class="mt-1 mb-0 fw-medium">—</h4>
                                    </div>
                                    <div class="col-3 align-self-center">
                                        <lord-icon src="{{ asset('/images/lordicons/user_n.json') }}" trigger="hover"
                                            colors="primary:#000000,secondary:#000000"
                                            style="width:50px;height:50px"></lord-icon>
                                    </div>
                                </div>
                            </div>
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
                                        <h5 class="mb-0">Usuarios</h5>
                                    </div>
                                    <!--end col-->

                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-header-->
                            <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="usuarios_table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Aprobado</th>
                                                <th class="text-start">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>

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



    @include('modales.roles')
@endsection
@push('styles')
@endpush
@push('scripts')
    @vite(['resources/js/roles.js'])
    @vite(['resources/js/users.js'])
@endpush
