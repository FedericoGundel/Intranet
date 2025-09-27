@extends('layouts.app')

@section('content')
<style>
    main {

        background-image: url("{{ $configurations['login_background']}}");
    }
</style>

<div class="container-xxl">
    <div class="row vh-100 d-flex justify-content-center">
        <div class="col-12 align-self-center">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 mx-auto">
                        <div class="card border-0">
                            <div class="card-body p-0 bg-black auth-header-box rounded-top">
                                <div class="text-center p-3">
                                    <a href="index.html" class="logo logo-admin">
                                        <img src="{{ $configurations['login_image']}}" height="50" alt="logo" class="auth-logo">
                                    </a>
                                    <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">Restablecer contraseña</h4>
                                    <p class="text-muted fw-medium mb-0">Ingresá tu correo y te enviaremos instrucciones.</p>
                                </div>
                            </div>
                            <div class="card-body pt-4">
                                  @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                                <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                                   
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="username">Correo electrónico</label>
                                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ $email ?? old('email') }}" name="email" placeholder="Ingresá tu correo electrónico"required autocomplete="email" autofocus>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                  
                                    <div class="form-group mb-0 row">
                                        <div class="col-12">
                                            <div class="d-grid mt-3">
                                                <button class="btn btn-primary" type="submit">Enviar email de confirmación <i class="fas fa-sign-in-alt ms-1"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                              
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end card-body-->
        </div><!--end col-->
    </div><!--end row-->
</div><!-- container -->
@endsection