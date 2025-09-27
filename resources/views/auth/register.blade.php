@extends('layouts.app')

@section('content')

<style>
    main{
    
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
                                    <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">Crear una cuenta</h4>   
                                    <p class="text-muted fw-medium mb-0">Ingresá tus datos para crear tu cuenta hoy.</p>  
                                </div>
                            </div>
                            <div class="card-body pt-0">                                    
                                <form method="POST" class="my-4" action="{{ route('register') }}">
                                    @csrf            
                                    <div class="form-group mb-2">
                                        <label class="form-label" for="username">Nombre de usuario</label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="form-label" for="useremail">Correo electrónico</label>
                                        <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="form-label" for="userpassword">Contraseña</label>                                            
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="form-label" for="Confirmpassword">Confirmar contraseña</label>                                            
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    </div>

                                    <div class="form-group mb-2 d-none">
                                        <label class="form-label" for="mobileNo">Número de teléfono</label>
                                        <input type="text" class="form-control" id="mobileNo" name="mobile number" placeholder="Ingresá tu número de teléfono">                               
                                    </div>

                                    <div class="form-group row mt-3 d-none">
                                        <div class="col-12">
                                            <div class="form-check form-switch form-switch-success">
                                                <input class="form-check-input" type="checkbox" id="customSwitchSuccess">
                                                <label class="form-check-label" for="customSwitchSuccess">
                                                    Al registrarte aceptás los <a href="#" class="text-primary">Términos de uso</a> de Approx
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0 row">
                                        <div class="col-12">
                                            <div class="d-grid mt-3">
                                                <button class="btn btn-primary" type="submit">Registrarse <i class="fas fa-sign-in-alt ms-1"></i></button>
                                            </div>
                                        </div>
                                    </div>                           
                                </form>
                                <div class="text-center">
                                    <p class="text-muted">¿Ya tenés una cuenta? <a href="{{ route('login') }}" class="text-primary ms-2">Iniciar sesión</a></p>
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
