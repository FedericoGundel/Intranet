@extends('layouts.app')

@section('title', 'Inicio')


@section('content')
<div class="page-wrapper">

    <!-- Page Content-->
    <div class="page-content py-2">
        <div class="container-fluid p-0">

            <div class="row g-0">
                <div class="col-12">
                    <div class="card mb-0">
                        <div class="card-header p-2 p-md-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">Roles</h5>
                                </div>
                                <!--end col-->
                                <div class="col-auto">
                                    <button class="btn btn-sm bg-primary text-white" data-bs-toggle="modal"
                                        data-bs-target="#addRol"><i class="fas fa-plus me-1"></i>Agregar Rol</button>
                                    <button id="btn_rol_defecto" class="btn btn-sm bg-success text-white"
                                        data-id="{{ $configurations['rol_defecto'] }}"><i
                                            class="fas fa-plus me-1"></i>Rol por
                                        defecto</button>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end card-header-->
                        <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                            <div class="table-responsive">
                                <table class="table mb-0" id="roles_table">
                                    <thead class="table-light">
                                        <tr>

                                            <th>Nombre</th>
                                            <th>Acciones</th>
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

@include('modales.roles')

@endsection
@push('styles')

@endpush
@push('scripts')






@vite(['resources/js/roles.js'])



@endpush