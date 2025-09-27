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
                                <form method="POST" action="{{ route('password.update') }}">
                                    @csrf

                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="username">Correo electrónico</label>
                                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ $email ?? old('email') }}" name="email" placeholder="Ingresá tu correo electrónico"required autocomplete="email" autofocus>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="password" class="form-label">{{ __('Password') }}</label>

                                       
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                       
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>

                                       
                                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                       
                                    </div>
                                    <div class="form-group mb-0 row">
                                        <div class="col-12">
                                            <div class="d-grid mt-3">
                                                <button class="btn btn-primary" type="submit">Restablecer <i class="fas fa-sign-in-alt ms-1"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="text-center mt-2">
                                    <p class="text-muted">¿Ya la recordás? <a href="auth-register.html" class="text-primary ms-2">Iniciá sesión aquí</a></p>
                                </div>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end card-body-->
        </div><!--end col-->
    </div><!--end row-->
</div><!-- container -->
@endsection