@extends('layouts.app')

@section('title', 'Inicio')


@section('content')
<div class="page-wrapper">

    <!-- Page Content-->
    <div class="page-content py-2 ">
        <div class="container-fluid p-0">


            <div class="row g-0">
                <div class="col-md-3">

                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header p-2 p-md-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">{{ auth()->user()->name}}</h5>
                                </div><!--end col-->

                            </div><!--end row-->
                        </div><!--end card-header-->
                        <div class="card-body pt-0 px-2 pb-2 px-md-3 pb-md-3">
                            <div class="mb-2" id="map" style="height: 500px; width: 100%;"></div> <!-- Aquí se mostrará el mapa -->
                            <div class="float-end d-print-none mt-2 mt-md-0 w-100">

                                @if(!$fichaje)
                                <button id="btn_entrada" class="btn btn-sm btn-primary w-100">Marcar entrada</button>
                                @elseif($estado === 'trabajando')
                                <div class="d-flex gap-2">
                                    <button id="btn_pausa" class="btn btn-sm btn-warning w-50">Pausar</button>
                                    <button id="btn_salida" class="btn btn-sm btn-danger  w-50">Marcar salida</button>
                                </div>
                                @elseif($estado === 'pausado')
                                <div class="d-flex gap-2">
                                    <button id="btn_reanudar" class="btn btn-sm btn-success w-50">Reanudar</button>
                                    <button id="btn_salida" class="btn btn-sm btn-danger  w-50">Marcar salida</button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->

                <div class="col-md-3">

                </div>
            </div> <!-- end row -->

        </div><!-- container -->

        <!--Start Rightbar-->
        <!--Start Rightbar/offcanvas-->


        <!--end footer-->
    </div>
    <!-- end page content -->
</div>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWWOfka8SjiT9Duq-FEYJON1zYftnN7LI&callback=initMap&loading=async&libraries=places"
    async defer>
</script>
<script>
  let map;
  let userLocation = { lat: null, lng: null };

  function initMap() {
    const mapEl = document.getElementById('map');

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          userLocation = { lat: pos.coords.latitude, lng: pos.coords.longitude };

          map = new google.maps.Map(mapEl, { zoom: 15, center: userLocation });
          new google.maps.Marker({ position: userLocation, map, title: 'Tu ubicación' });
        },
        () => handleLocationError(true)
      );
    } else {
      handleLocationError(false);
    }
  }

  function handleLocationError(browserHasGeolocation) {
    const fallbackCenter = { lat: 40.4168, lng: -3.7038 }; // Madrid
    map = new google.maps.Map(document.getElementById('map'), { zoom: 5, center: fallbackCenter });
    new google.maps.InfoWindow({
      content: browserHasGeolocation
        ? 'Error: El servicio de geolocalización falló.'
        : 'Error: Tu navegador no soporta geolocalización.'
    }).open(map);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const btnEntrada  = document.getElementById('btn_entrada');
    const btnPausa    = document.getElementById('btn_pausa');
    const btnReanudar = document.getElementById('btn_reanudar');
    const btnSalida   = document.getElementById('btn_salida');

    if (btnEntrada)  btnEntrada.addEventListener('click',  () => enviar('entrada'));
    if (btnPausa)    btnPausa.addEventListener('click',    () => enviar('pausa'));
    if (btnReanudar) btnReanudar.addEventListener('click', () => enviar('reanudar'));
    if (btnSalida)   btnSalida.addEventListener('click',   () => enviar('salida'));
  });

  async function enviar(accion) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // localización como string "lat,lng" (tu nueva columna)
    const localizacion =
      (userLocation.lat != null && userLocation.lng != null)
        ? `${userLocation.lat},${userLocation.lng}`
        : '';

    try {
      const resp = await fetch(`/fichaje/${accion}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ localizacion })
      });

      const data = await resp.json();

      if (!resp.ok || !data.success) {
        alert(data.message || 'No se pudo completar la acción.');
        return;
      }

      // Toggle local según la acción realizada (no recargamos)
      toggleButtonsAfter(accion);
    } catch (e) {
      console.error(e);
      alert('Error de red. Intenta nuevamente.');
    }
  }

  // Regla de UI:
  // - tras ENTRADA -> trabajando => mostrar PAUSAR y SALIDA
  // - tras PAUSA   -> pausado    => mostrar REANUDAR y SALIDA
  // - tras REANUDAR-> trabajando => mostrar PAUSAR y SALIDA
  // - tras SALIDA  -> sin jornada=> mostrar ENTRADA
  function toggleButtonsAfter(accion) {
    const btnEntrada  = document.getElementById('btn_entrada');
    const btnPausa    = document.getElementById('btn_pausa');
    const btnReanudar = document.getElementById('btn_reanudar');
    const btnSalida   = document.getElementById('btn_salida');

    [btnEntrada, btnPausa, btnReanudar, btnSalida].forEach(b => b?.classList.add('d-none'));

    if (accion === 'entrada' || accion === 'reanudar') {
      btnPausa?.classList.remove('d-none');
      btnSalida?.classList.remove('d-none');
    } else if (accion === 'pausa') {
      btnReanudar?.classList.remove('d-none');
      btnSalida?.classList.remove('d-none');
    } else if (accion === 'salida') {
      btnEntrada?.classList.remove('d-none');
    }
  }
</script>





@endsection
@push('styles')

@endpush
@push('scripts')






@vite(['resources/js/users.js'])



@endpush