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
                                            <img src="{{ $configurations['login_image']}}" height="100" alt="logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">Bienvenido a {{ $configurations['nombre_app']}}!</h4>   
                                        <p class="text-muted fw-medium mb-0">Inicia sesión para continuar.</p>  
                                    </div>
                                </div>

                                <div class="card-body pt-0">                                    
                                    <form method="POST" class="my-4" action="{{ route('login') }}"onsubmit="guardarCredenciales()">
                        @csrf
           
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="username">Correo electrónico</label>
                                             <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                              
                                           @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                        </div><!--end form-group--> 
            
                                        <div class="form-group">
                                            <label class="form-label" for="userpassword">Contraseña</label>                                            
                                              <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                        </div><!--end form-group--> 
            
                                        <div class="form-group row mt-3">
                                            <div class="col-sm-6">
                                                <div class="form-check form-switch form-switch-success">
                                                    <input class="form-check-input" type="checkbox" id="remember"name="remember"  {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="remember">Recuérdame</label>
                                                </div>
                                            </div><!--end col--> 
                                            <div class="col-sm-6 text-end">
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="text-muted font-13"><i class="dripicons-lock"></i>¿Olvidaste tu contraseña?</a>                                    
                                             @endif
                                            </div><!--end col--> 
                                        </div><!--end form-group--> 
            
                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <div class="d-grid mt-3">
                                                    <button type="submit"class="btn btn-primary" type="button">Iniciar sesión <i class="fas fa-sign-in-alt ms-1"></i></button>
                                                </div>
                                            </div><!--end col--> 
                                        </div> <!--end form-group-->                           
                                     </form>
                                    <div class="text-center  mb-2">
                                        <p class="text-muted">No tienes una cuenta?  <a href="{{ route('register') }}" class="text-primary ms-2">Regístrate gratis</a></p>
                                        <h6 class="px-3 d-inline-block">O inicia sesión con</h6>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <a href="" class="d-flex justify-content-center align-items-center thumb-md bg-blue-subtle text-blue rounded-circle me-2">
                                            <i class="fab fa-facebook align-self-center"></i>
                                        </a>
                                        <a href="" class="d-flex justify-content-center align-items-center thumb-md bg-info-subtle text-info rounded-circle me-2">
                                            <i class="fab fa-twitter align-self-center"></i>
                                        </a>
                                        <a href="" class="d-flex justify-content-center align-items-center thumb-md bg-danger-subtle text-danger rounded-circle">
                                            <i class="fab fa-google align-self-center"></i>
                                        </a>
                                    </div>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->                                        
    </div><!-- container -->
    <script>
    // Cargar credenciales si existen
    window.onload = function () {
        if (localStorage.getItem("remember") === "true") {
            document.getElementById("email").value = localStorage.getItem("email") || "";
            document.getElementById("password").value = localStorage.getItem("password") || "";
            document.getElementById("remember").checked = true;
        }
    };

    // Guardar credenciales si se selecciona "recordarme"
    function guardarCredenciales() {
        const remember = document.getElementById("remember").checked;

        if (remember) {
            localStorage.setItem("email", document.getElementById("email").value);
            localStorage.setItem("password", document.getElementById("password").value);
            localStorage.setItem("remember", "true");
        } else {
            localStorage.removeItem("email");
            localStorage.removeItem("password");
            localStorage.setItem("remember", "false");
        }
    }
</script>
@endsection
