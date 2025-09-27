 <!-- Top Bar Start -->


 <div class="topbar d-print-none">
     <div class="container-fluid">
         <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">


             <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                 <li>
                     <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                         <i class="iconoir-menu"></i>
                     </button>
                 </li>
                 <li class="hide-phone app-search d-none">
                     <form role="search" action="#" method="get">
                         <input type="search" name="search" class="form-control top-search mb-0"
                             placeholder="Search here...">
                         <button type="submit"><i class="iconoir-search"></i></button>
                     </form>
                 </li>
             </ul>
             <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">

                 <li class="dropdown d-none">
                     <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                         role="button" aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19">
                         <img src="{{ asset('images/flags/us_flag.jpg') }}" alt=""
                             class="thumb-sm rounded-circle">
                     </a>
                     <div class="dropdown-menu">
                         <a class="dropdown-item" href="#"><img src="{{ asset('images/flags/us_flag.jpg') }}"
                                 alt="" height="15" class="me-2">English</a>
                         <a class="dropdown-item" href="#"><img src="{{ asset('images/flags/spain_flag.jpg') }}"
                                 alt="" height="15" class="me-2">Spanish</a>
                         <a class="dropdown-item" href="#"><img src="{{ asset('images/flags/germany_flag.jpg') }}"
                                 alt="" height="15" class="me-2">German</a>
                         <a class="dropdown-item" href="#"><img src="{{ asset('images/flags/french_flag.jpg') }}"
                                 alt="" height="15" class="me-2">French</a>
                     </div>
                 </li>
                 <!--end topbar-language-->

                 <li class="topbar-item">
                     <a class="nav-link nav-icon" href="javascript:void(0);" id="light-dark-mode">
                         <i class="iconoir-half-moon dark-mode"></i>
                         <i class="iconoir-sun-light light-mode"></i>
                     </a>
                 </li>

                 <li class=" d-none">
                     <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                         role="button" aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19">
                         <i class="iconoir-bell"></i>
                         <span class="alert-badge"></span>
                     </a>
                     <div class="dropdown-menu stop dropdown-menu-end dropdown-lg py-0">

                         <h5 class="dropdown-item-text m-0 py-3 d-flex justify-content-between align-items-center">
                             Notifications <a href="#" class="badge text-body-tertiary badge-pill">
                                 <i class="iconoir-plus-circle fs-4"></i>
                             </a>
                         </h5>
                         <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-1" role="tablist">
                             <li class="nav-item" role="presentation">
                                 <a class="nav-link mx-0 active" data-bs-toggle="tab" href="#All" role="tab"
                                     aria-selected="true">
                                     All <span class="badge bg-primary-subtle text-primary badge-pill ms-1">24</span>
                                 </a>
                             </li>
                             <li class="nav-item" role="presentation">
                                 <a class="nav-link mx-0" data-bs-toggle="tab" href="#Projects" role="tab"
                                     aria-selected="false" tabindex="-1">
                                     Projects
                                 </a>
                             </li>
                             <li class="nav-item" role="presentation">
                                 <a class="nav-link mx-0" data-bs-toggle="tab" href="#Teams" role="tab"
                                     aria-selected="false" tabindex="-1">
                                     Team
                                 </a>
                             </li>
                         </ul>
                         <div class="ms-0" style="max-height:230px;" data-simplebar>
                             <div class="tab-content" id="myTabContent">
                                 <div class="tab-pane fade show active" id="All" role="tabpanel"
                                     aria-labelledby="all-tab" tabindex="0">
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">2 min ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-wolf fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">Your order is placed</h6>
                                                 <small class="text-muted mb-0">Dummy text of the printing and
                                                     industry.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">10 min ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-apple-swift fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">Meeting with designers</h6>
                                                 <small class="text-muted mb-0">It is a long established fact that a
                                                     reader.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">40 min ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-birthday-cake fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">UX 3 Task complete.</h6>
                                                 <small class="text-muted mb-0">Dummy text of the printing.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">1 hr ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-drone fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">Your order is placed</h6>
                                                 <small class="text-muted mb-0">It is a long established fact that a
                                                     reader.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">2 hrs ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-user fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">Payment Successfull</h6>
                                                 <small class="text-muted mb-0">Dummy text of the printing.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                 </div>
                                 <div class="tab-pane fade" id="Projects" role="tabpanel"
                                     aria-labelledby="projects-tab" tabindex="0">
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">40 min ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-birthday-cake fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">UX 3 Task complete.</h6>
                                                 <small class="text-muted mb-0">Dummy text of the printing.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">1 hr ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-drone fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">Your order is placed</h6>
                                                 <small class="text-muted mb-0">It is a long established fact that a
                                                     reader.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">2 hrs ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-user fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">Payment Successfull</h6>
                                                 <small class="text-muted mb-0">Dummy text of the printing.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                 </div>
                                 <div class="tab-pane fade" id="Teams" role="tabpanel"
                                     aria-labelledby="teams-tab" tabindex="0">
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">1 hr ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-drone fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">Your order is placed</h6>
                                                 <small class="text-muted mb-0">It is a long established fact that a
                                                     reader.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                     <!-- item-->
                                     <a href="#" class="dropdown-item py-3">
                                         <small class="float-end text-muted ps-2">2 hrs ago</small>
                                         <div class="d-flex align-items-center">
                                             <div
                                                 class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                                                 <i class="iconoir-user fs-4"></i>
                                             </div>
                                             <div class="flex-grow-1 ms-2 text-truncate">
                                                 <h6 class="my-0 fw-normal text-dark fs-13">Payment Successfull</h6>
                                                 <small class="text-muted mb-0">Dummy text of the printing.</small>
                                             </div>
                                             <!--end media-body-->
                                         </div>
                                         <!--end media-->
                                     </a>
                                     <!--end-item-->
                                 </div>
                             </div>

                         </div>
                         <!-- All-->
                         <a href="pages-notifications.html" class="dropdown-item text-center text-dark fs-13 py-2">
                             View All <i class="fi-arrow-right"></i>
                         </a>
                     </div>
                 </li>

                 <li class="dropdown topbar-item">
                     <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                         role="button" aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19">
                         <img src="{{ auth()->user()->imagen ? asset('storage/' . auth()->user()->imagen) : asset('storage/defaults/default_user.png') }}"
                             alt="" class="thumb-md rounded-circle foto_user">
                     </a>
                     <div class="dropdown-menu dropdown-menu-end py-0">
                         <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                             <div class="flex-shrink-0">
                                 <img src="{{ auth()->user()->imagen ? asset('storage/' . auth()->user()->imagen) : asset('storage/defaults/default_user.png') }}"
                                     alt="" class="thumb-md rounded-circle foto_user">
                             </div>
                             <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                 <h6 class="my-0 fw-medium text-dark fs-13">{{ auth()->user()->name }}</h6>
                                 <small class="text-muted mb-0">
                                     {{ auth()->user()->roles->pluck('name')->join(', ') }}
                                 </small>
                             </div>
                         </div>

                         <div class="dropdown-divider mt-0"></div>
                         <small class="text-muted px-2 pb-1 d-block">Cuenta</small>
                         <a class="dropdown-item" href="{{ route('perfil.index') }}"><i
                                 class="las la-user fs-18 me-1 align-text-bottom"></i> Perfil</a>
                         <a class="d-none dropdown-item" href="pages-faq.html"><i
                                 class="las la-wallet fs-18 me-1 align-text-bottom"></i> Earning</a>
                         <small class="text-muted px-2 py-1 d-block">Ajustes</small>
                         <a class="dropdown-item" href="{{ route('configuracion.index') }}"><i
                                 class="las la-cog fs-18 me-1 align-text-bottom"></i>Configuración</a>
                         <a class="d-none dropdown-item" href="pages-profile.html"><i
                                 class="las la-lock fs-18 me-1 align-text-bottom"></i> Security</a>
                         <a class="d-none dropdown-item" href="pages-faq.html"><i
                                 class="las la-question-circle fs-18 me-1 align-text-bottom"></i> Help Center</a>
                         <div class="dropdown-divider mb-0"></div>
                         <!-- El botón Logout -->
                         <a class="dropdown-item text-danger" href="#"
                             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                             <i class="las la-power-off fs-18 me-1 align-text-bottom"></i> Cerrar sesión
                         </a>

                         <!-- Formulario oculto para enviar POST -->
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                             @csrf
                         </form>
                     </div>
                 </li>
             </ul>
             <!--end topbar-nav-->
         </nav>
         <!-- end navbar-->
     </div>
 </div>
 <!-- Top Bar End -->
 <!-- leftbar-tab-menu -->
 <div class="startbar d-print-none">
     <!--start brand-->
     <div class="brand justify-content-center">
         <a href="{{ route('home') }}" class="logo">
             <span>
                 <img src="{{ $configurations['logo_menu_mini'] }}" alt="logo-small" class="logo-sm logo_menu_mini">
             </span>
             <span class="">
                 <img src="{{ $configurations['logo_menu_expanded'] }}" alt="logo-large"
                     class="logo-lg logo-light logo_menu_expanded">
                 <img src="{{ $configurations['logo_menu_expanded'] }}" alt="logo-large"
                     class="logo-lg logo-dark logo_menu_expanded">
             </span>
         </a>
     </div>
     <!--end brand-->
     <!--start startbar-menu-->
     <div class="startbar-menu">
         <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
             <div class="d-flex align-items-start flex-column w-100">
                 <!-- Navigation -->
                 <ul class="navbar-nav mb-auto w-100">
                     <li class="menu-label mt-2">
                         <span>Principal</span>
                     </li>


                     <li class="nav-item">
                         <a class="nav-link" href="{{ route('home') }}">
                             <i class="iconoir-stats-down-square menu-icon"></i>
                             <span>Estadísticas</span>
                             <span class="d-none badge text-bg-info ms-auto">New</span>
                         </a>
                     </li>


                     @can('usuarios')
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('users.index') }}">
                                 <i class="iconoir-user-badge-check menu-icon"></i>
                                 <span>Usuarios</span>
                             </a>
                         </li>
                     @endcan

                     @can('clientes')
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('clientes.index') }}">
                                 <i class="iconoir-group menu-icon"></i>
                                 <span>Clientes</span>
                             </a>
                         </li>
                     @endcan

                     @can('email_marketing')
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('email_marketing.index') }}">
                                 <i class="iconoir-send-mail menu-icon"></i>
                                 <span>Email marketing</span>
                             </a>
                         </li>
                     @endcan

                     <li class="menu-label mt-2">
                         <small class="label-border">
                             <div class="border_left hidden-xs"></div>
                             <div class="border_right"></div>
                         </small>
                         <span>Ventas</span>
                     </li>

                     @if (auth()->user()->can('articulos') ||
                             auth()->user()->can('crear_factura') ||
                             auth()->user()->can('facturas') ||
                             auth()->user()->can('cobros'))
                         <li class="nav-item">
                             <a class="nav-link" href="#sidebarElements" data-bs-toggle="collapse" role="button"
                                 aria-expanded="false" aria-controls="sidebarElements">
                                 <i class="iconoir-coins menu-icon"></i>
                                 <span>Facturas</span>
                             </a>
                             <div class="collapse " id="sidebarElements">
                                 <ul class="nav flex-column">
                                     @can('articulos')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('articulos.index') }}">
                                                 <i class="iconoir-paste-clipboard menu-icon"></i>
                                                 <span>Artículos</span>
                                             </a>
                                         </li>
                                     @endcan

                                     @can('crear_factura')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('facturas.crear') }}">
                                                 <i class="iconoir-page-plus-in menu-icon"></i>
                                                 <span>Crear</span>
                                             </a>
                                         </li>
                                     @endcan

                                     @can('facturas')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('facturas.index') }}">
                                                 <i class="iconoir-multiple-pages menu-icon"></i>
                                                 <span>Historial</span>
                                             </a>
                                         </li>
                                     @endcan

                                     @can('cobros')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('cobros.index') }}">
                                                 <i class="iconoir-wallet menu-icon"></i>
                                                 <span>Cobros</span>
                                             </a>
                                         </li>
                                     @endcan
                                 </ul>
                             </div>
                         </li>
                     @endif

                     <li class="menu-label mt-2">
                         <small class="label-border">
                             <div class="border_left hidden-xs"></div>
                             <div class="border_right"></div>
                         </small>
                         <span>Empleados</span>
                     </li>

                     @if (auth()->user()->can('fichaje'))
                         <li class="nav-item">
                             <a class="nav-link" href="#fichaje_link" data-bs-toggle="collapse" role="button"
                                 aria-expanded="false" aria-controls="fichaje_link">
                                 <i class="iconoir-navigator-alt menu-icon"></i>
                                 <span>Control de fichaje</span>
                             </a>
                             <div class="collapse " id="fichaje_link">
                                 <ul class="nav flex-column">
                                     @can('fichaje')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('fichaje.index') }}">
                                                 <i class="iconoir-map-pin menu-icon"></i>
                                                 <span>Fichaje</span>
                                             </a>
                                         </li>
                                     @endcan
                                 </ul>
                             </div>
                         </li>
                     @endif

                     @can('nominas')
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('nominas.index') }}">
                                 <i class="iconoir-hand-cash menu-icon"></i>
                                 <span>Nóminas</span>
                             </a>
                         </li>
                     @endcan

                     @can('gastos')
                         <li class="nav-item">
                             <a class="nav-link" href="{{ route('gastos.index') }}">
                                 <i class="iconoir-dollar-circle menu-icon"></i>
                                 <span>Gastos</span>
                             </a>
                         </li>
                     @endcan



                     <li class="menu-label mt-2">
                         <small class="label-border">
                             <div class="border_left hidden-xs"></div>
                             <div class="border_right"></div>
                         </small>
                         <span>Leyma Créditos</span>
                     </li>

                     @can('leyma_creditos')
                         <li class="nav-item">
                             <a class="nav-link" href="#sidebarLeyma" data-bs-toggle="collapse" role="button"
                                 aria-expanded="false" aria-controls="sidebarLeyma">
                                 <i class="iconoir-bank menu-icon"></i>
                                 <span>Leyma Créditos</span>
                             </a>
                             <div class="collapse" id="sidebarLeyma">
                                 <ul class="nav flex-column">
                                     <li class="nav-item">
                                         <a class="nav-link" href="{{ route('leyma-creditos.index') }}">
                                             <i class="iconoir-dashboard menu-icon"></i>
                                             <span>Dashboard</span>
                                         </a>
                                     </li>

                                     @can('leyma_clientes')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('leyma-creditos.clientes.index') }}">
                                                 <i class="iconoir-group menu-icon"></i>
                                                 <span>Clientes</span>
                                             </a>
                                         </li>
                                     @endcan

                                     @can('leyma_creditos_gestion')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('leyma-creditos.creditos.index') }}">
                                                 <i class="iconoir-credit-card menu-icon"></i>
                                                 <span>Créditos</span>
                                             </a>
                                         </li>
                                     @endcan

                                     @can('leyma_gastos')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('leyma-creditos.gastos.index') }}">
                                                 <i class="iconoir-dollar-circle menu-icon"></i>
                                                 <span>Gastos</span>
                                             </a>
                                         </li>
                                     @endcan

                                     @can('leyma_inyecciones')
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('leyma-creditos.inyecciones.index') }}">
                                                 <i class="iconoir-arrow-up-circle menu-icon"></i>
                                                 <span>Inyecciones</span>
                                             </a>
                                         </li>
                                     @endcan

                                     @can('leyma_prestamos_familiares')
                                         <li class="nav-item">
                                             <a class="nav-link"
                                                 href="{{ route('leyma-creditos.prestamos-familiares.index') }}">
                                                 <i class="iconoir-heart menu-icon"></i>
                                                 <span>Préstamos Familiares</span>
                                             </a>
                                         </li>
                                     @endcan
                                 </ul>
                             </div>
                         </li>
                     @endcan
                 </ul>

                 <!--end navbar-nav--->
                 <div class="update-msg text-center d-none">
                     <div
                         class="d-flex justify-content-center align-items-center thumb-lg update-icon-box  rounded-circle mx-auto">
                         <!-- <i class="iconoir-peace-hand h3 align-self-center mb-0 text-primary"></i> -->
                         <img src="{{ asset('images/extra/gold.png') }}" alt="" class=""
                             height="45">
                     </div>
                     <h5 class="mt-3">Today's <span class="text-white">$2450.00</span></h5>
                     <p class="mb-3 text-muted">Today's best Investment for you.</p>
                     <a href="javascript: void(0);" class="btn text-primary shadow-sm rounded-pill px-3">Invest
                         Now</a>
                 </div>
             </div>
         </div>
         <!--end startbar-collapse-->
     </div>
     <!--end startbar-menu-->
 </div>
 <!--end startbar-->
 <div class="startbar-overlay d-print-none"></div>
 <!-- end leftbar-tab-menu-->
