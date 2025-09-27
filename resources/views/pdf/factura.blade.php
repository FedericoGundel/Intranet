<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
    @page {
        margin: 0mm 0mm 0mm 0mm;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
    }

    .text-end {
        text-align: right;
    }

    .text-muted {
        color: #666;
    }

    .fw-semibold {
        font-weight: 600;
    }

    .fs-12 {
        font-size: 12px;
    }

    .fs-13 {
        font-size: 13px;
    }

    .fs-18 {
        font-size: 18px;
    }

    .fs-24 {
        font-size: 24px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        border: 1px solid #ddd;
        padding: 6px;
    }

    .table th {
        background-color: #f5f5f5;
    }
    </style>
</head>

<body>

    <div style="background: #000; color: #fff;padding:20px;">
        <table width="100%">
            <tr>
                <td width="50%">
                    <img src="{{ $base64_logo }}" alt="logo" style="max-height: 40px;">
                </td>
                <td width="50%" class="text-end">
                    <p style="margin-top: 0;margin-bottom:.2rem;"><strong>Factura:</strong> {{ $factura->numero }}</p>
                    <p style="margin-top: 0;margin-bottom:.2rem;"><strong>Fecha de emisión:</strong>
                        {{ $factura->fecha }}</p>
                    <p style="margin-top: 0;margin-bottom:0;"><strong>Fecha de vencimiento:</strong>
                        {{ $factura->fecha_vencimiento }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div style="padding: 20px;">

        <table width="100%">
            <tr>
                <td width="50%">
                    <h4 style="margin:0;font-size: 12px; color:white;">Factura para:</h4>
                    <h4 style="margin:.2rem 0 1rem 0;font-size: 20px;">{{ $configurations['razon_social'] }}</h4>
                    <p>
                        <strong>NIF:</strong> {{ $configurations['nif'] }}<br>
                        <strong>Dirección:</strong> {{ $configurations['direccion'] }}, {{ $configurations['ciudad'] }},
                        {{ $configurations['provincia'] }}, {{ $configurations['pais'] }}<br>
                        <strong>CP:</strong> {{ $configurations['codigo_postal'] }}<br>
                        <strong>Tel:</strong> {{ $configurations['telefono'] }}<br>
                        <strong>Email:</strong> {{ $configurations['email'] }}<br>
                        <strong>Web:</strong> {{ $configurations['pagina_web'] }}
                    </p>
                </td>
                <td width="50%">
                    <h4 style="margin:0;font-size: 12px;">Factura para:</h4>
                    <h4 style="margin:.2rem 0 1rem 0;font-size: 20px;">{{ $factura->nombre }} {{ $factura->apellido1 }}
                        {{ $factura->apellido2 }}</h4>
                    <p>
                        <strong>NIF:</strong> {{ $factura->nif }}<br>
                        <strong>Dirección:</strong> {{ $factura->direccion }}, {{ $factura->localidad }},
                        {{ $factura->provincia }}, {{ $factura->pais }}<br>
                        <strong>CP:</strong> {{ $factura->codigo_postal }}<br>
                        <strong>Tel:</strong> {{ $factura->telefono }}<br>
                        <strong>Email:</strong> {{ $factura->email }}
                    </p>
                </td>
            </tr>
        </table>

        <h4 style="margin-top: 30px;">Artículos</h4>

        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Artículo</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Descuento</th>
                    <th>Impuesto</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $base = 0;
                $impuestos = 0;
                @endphp
                @foreach ($factura->articuloFacturas as $articulo)
                @php
                $precioUnit = (float) $articulo->precio;
                $cantidad = (float) $articulo->cantidad;
                $descuento = (float) ($articulo->descuento ?? 0);
                $impuestoPct = (float) ($articulo->impuesto ?? 0);

                // Aplicar descuento primero
                $precioConDesc = $precioUnit * (1 - $descuento / 100);

                // Subtotal base con descuento aplicado
                $subtotal = $precioConDesc * $cantidad;

                // Calcular impuesto sobre esa base
                $impuestoMonto = $subtotal * ($impuestoPct / 100);

                // Total de la línea
                $totalLinea = $subtotal + $impuestoMonto;

                // Acumular
                $base += $subtotal;
                $impuestos += $impuestoMonto;
                @endphp
                <tr>
                    <td>{{ $articulo->articulo->codigo ?? '-' }}</td>
                    <td>
                        <p style="margin:0;font-size:13px;line-height:1;"><strong>{{ $articulo->nombre }}</strong></p>
                        <p style="margin:0;font-size:12px;line-height:1;">{{ $articulo->descripcion }}</p>
                    </td>
                    <td>{{ $cantidad }}</td>
                    <td>$ {{ number_format($precioUnit, 2) }}</td>
                    <td>{{ number_format($descuento, 2) }}%</td>
                    <td>{{ number_format($impuestoPct, 2) }}%</td>
                    <td>$ {{ number_format($totalLinea, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; text-align: right;">
            <p style="margin:0 0 .2rem 0;"><strong>Base imponible:</strong> $ {{ number_format($base, 2) }}</p>
            <p style="margin:0 0 .2rem 0;"><strong>Impuestos agregados:</strong> $ {{ number_format($impuestos, 2) }}
            </p>
            <h4 style="margin:0;"><strong>Total:</strong> $ {{ number_format($base + $impuestos, 2) }}</h4>
        </div>

        <div style="margin-top: 20px;">
            <h3>Términos y condiciones</h3>
            <p style="margin:0 0 .2rem 0;"> {{ $factura->condiciones }}</p>
        </div>

    </div>

</body>

</html>