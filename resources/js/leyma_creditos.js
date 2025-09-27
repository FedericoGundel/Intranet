import $ from "jquery";
window.$ = $;
window.jQuery = $;

import Chart from "chart.js/auto";
import currency from "currency.js";
// ApexCharts se carga desde public/libs/apexcharts/apexcharts.min.js

let graficoDistribucionTipo, graficoCreditosPorMes, graficoMontosPorMes;
let periodoActual = "año";

$(document).ready(function () {
    cargarDashboard();
});

function cargarDashboard() {
    cargarEstadisticas();
    cargarGraficos();
}

function cargarEstadisticas() {
    $.ajax({
        url: "/api/leyma-creditos/estadisticas",
        type: "GET",
        data: {
            fecha_inicio: getFechaInicio(),
            fecha_fin: getFechaFin(),
        },
        success: function (data) {
            actualizarEstadisticas(data);
            actualizarTablas(data);
            // Solo completamos datos de caja (gastos/saldo). El total cobrado de la card
            // se toma exclusivamente de pagos de créditos (monto_total_cobrado)
            if (
                typeof data.total_gastado === "undefined" ||
                typeof data.saldo_caja === "undefined"
            ) {
                fetch(`/home/caja?intervalo=anio`)
                    .then((r) => (r.ok ? r.json() : null))
                    .then((caja) => {
                        if (!caja) return;
                        $("#total_gastado").text(
                            formatearMoneda(caja.totalGastado ?? 0)
                        );
                        $("#saldo_caja").text(
                            formatearMoneda(
                                (caja.totalCobrado ?? 0) -
                                    (caja.totalGastado ?? 0)
                            )
                        );
                    })
                    .catch(() => {});
            }
        },
        error: function () {
            console.error("Error al cargar estadísticas");
        },
    });
}

function cargarGraficos() {
    $.ajax({
        url: "/api/leyma-creditos/dashboard/graficos",
        type: "GET",
        data: {
            fecha_inicio: getFechaInicio(),
            fecha_fin: getFechaFin(),
        },
        success: function (data) {
            console.log("Datos de gráficos recibidos:", data);
            console.log("Datos de créditos por mes:", data.creditos_por_mes);
            crearGraficoDistribucionTipo(data.distribucion_tipo);
            crearGraficoCreditosPorMes(data.creditos_por_mes);
            crearGraficoMontosPorMes(data.montos_por_mes);
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar gráficos:", error);
            console.error("Response:", xhr.responseText);
        },
    });
}

function actualizarEstadisticas(data) {
    // Formatear números con separadores de miles
    const creditosTotales = Number(data.creditos_totales ?? 0);
    const creditosActivos = Number(data.creditos_activos ?? 0);
    const montoPrestado = Number(data.monto_total_prestado ?? 0);
    const montoPendiente = Number(
        data.monto_pendiente_cobro ?? data.monto_pendiente ?? 0
    );

    $("#creditos_totales").text(formatearNumero(creditosTotales));
    $("#creditos_activos").text(formatearNumero(creditosActivos));
    $("#monto_total_prestado").text(formatearMoneda(montoPrestado));
    $("#monto_pendiente").text(formatearMoneda(montoPendiente));

    // Estadísticas de la caja
    // Total cobrado en esta card = SOLO pagos de créditos
    const totalCobradoCreditos = Number(
        data.monto_total_cobrado ?? data.total_cobrado ?? 0
    );
    $("#total_cobrado").text(formatearMoneda(totalCobradoCreditos));
    if (typeof data.total_gastado !== "undefined")
        $("#total_gastado").text(formatearMoneda(data.total_gastado));
    if (typeof data.saldo_caja !== "undefined")
        $("#saldo_caja").text(formatearMoneda(data.saldo_caja));

    // Actualizar porcentajes y tendencias
    const porcActivos =
        creditosTotales > 0
            ? Math.round((creditosActivos / creditosTotales) * 100)
            : 0;
    $("#creditos_activos_porcentaje").text(porcActivos + "% del total");
    if (typeof data.porcentaje_pagado !== "undefined") {
        $("#monto_pendiente_porcentaje").text(
            data.porcentaje_pagado + "% pagado"
        );
    } else {
        $("#monto_pendiente_porcentaje").text("");
    }

    // Tendencias de la caja (simplificadas por ahora)
    $("#total_cobrado_trend").text("Total recaudado");
    $("#total_gastado_trend").text("Total gastado");
    $("#saldo_caja_trend").text(
        data.saldo_caja >= 0 ? "Saldo positivo" : "Saldo negativo"
    );
}

function actualizarTablas(data) {
    // Top 5 Clientes
    const top = Array.isArray(data.top_clientes) ? data.top_clientes : [];
    let htmlTopClientes = "";
    if (top.length === 0) {
        htmlTopClientes = `
            <tr>
                <td colspan="3" class="text-center text-muted">Sin datos</td>
            </tr>
        `;
    }
    top.forEach(function (cliente) {
        htmlTopClientes += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <div class="fw-medium">${cliente.nombre}</div>
                            <small class="text-muted">DNI: ${
                                cliente.dni
                            }</small>
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-primary">${
                    cliente.total_creditos
                }</span></td>
                <td>${formatearMoneda(cliente.monto_total)}</td>
            </tr>
        `;
    });
    $("#tablaTopClientes").html(htmlTopClientes);
}

function crearGraficoDistribucionTipo(data) {
    const ctx = document
        .getElementById("graficoDistribucionTipo")
        .getContext("2d");

    if (graficoDistribucionTipo) {
        graficoDistribucionTipo.destroy();
    }

    graficoDistribucionTipo = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: data.map((item) => item.label),
            datasets: [
                {
                    data: data.map((item) => item.value),
                    backgroundColor: data.map((item) => item.color),
                    borderWidth: 2,
                    borderColor: "#fff",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "bottom",
                },
            },
        },
    });
}

function crearGraficoCreditosPorMes(data) {
    console.log("Creando gráfico de créditos por mes:", data);
    console.log("ApexCharts disponible:", typeof ApexCharts);

    const el = document.getElementById("graficoCreditosPorMes");
    if (!el) {
        console.error("Elemento graficoCreditosPorMes no encontrado");
        return;
    }

    if (typeof ApexCharts === "undefined") {
        console.error("ApexCharts no está disponible globalmente");
        return;
    }

    const categorias = data.map((item) => item.mes);
    const seriesData = data.map((item) => item.cantidad);
    console.log("Categorías:", categorias);
    console.log("Datos:", seriesData);

    const opciones = {
        series: [
            { name: "Créditos otorgados", data: seriesData },
            {
                name: "Negativo (oculto)",
                data: Array(seriesData.length).fill(null),
            },
        ],
        chart: {
            toolbar: { show: false },
            type: "bar",
            fontFamily: "inherit",
            foreColor: "#adb0bb",
            height: 370,
            stacked: true,
            offsetX: -15,
        },
        colors: ["var(--bs-success)", "rgba(155, 171, 187, .25)"],
        plotOptions: {
            bar: {
                horizontal: false,
                barHeight: "80%",
                columnWidth: "20%",
                borderRadius: [3],
            },
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        grid: {
            show: true,
            strokeDashArray: 3,
            padding: { top: 0, bottom: 0, right: 0 },
            borderColor: "rgba(0,0,0,0.05)",
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } },
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
        },
        xaxis: {
            axisBorder: { show: false },
            axisTicks: { show: false },
            categories: categorias,
        },
        tooltip: {
            y: {
                formatter: (val) =>
                    val == null ? "" : `${val} crédito${val === 1 ? "" : "s"}`,
            },
        },
    };

    if (graficoCreditosPorMes) {
        graficoCreditosPorMes.updateOptions(opciones);
    } else {
        graficoCreditosPorMes = new ApexCharts(el, opciones);
        graficoCreditosPorMes.render();
    }
}

function crearGraficoMontosPorMes(data) {
    const ctx = document.getElementById("graficoMontosPorMes").getContext("2d");

    if (graficoMontosPorMes) {
        graficoMontosPorMes.destroy();
    }

    graficoMontosPorMes = new Chart(ctx, {
        type: "line",
        data: {
            labels: data.map((item) => item.mes),
            datasets: [
                {
                    label: "Monto Total",
                    data: data.map((item) => item.monto),
                    borderColor: "rgba(75, 192, 192, 1)",
                    backgroundColor: "rgba(75, 192, 192, 0.2)",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatearMoneda(value);
                        },
                    },
                },
            },
        },
    });
}

function cambiarPeriodo(periodo) {
    periodoActual = periodo;

    // Actualizar el texto del botón
    const textos = {
        mes: "Este mes",
        trimestre: "Este trimestre",
        año: "Año actual",
        personalizado: "Personalizado",
    };

    document.getElementById("periodo-texto").textContent =
        textos[periodo] || "Año actual";

    cargarDashboard();
}

function actualizarDashboard() {
    cargarDashboard();
}

function getFechaInicio() {
    const hoy = new Date();
    switch (periodoActual) {
        case "mes":
            return new Date(hoy.getFullYear(), hoy.getMonth(), 1)
                .toISOString()
                .split("T")[0];
        case "trimestre":
            return new Date(hoy.getFullYear(), hoy.getMonth() - 3, 1)
                .toISOString()
                .split("T")[0];
        case "año":
            return new Date(hoy.getFullYear(), 0, 1)
                .toISOString()
                .split("T")[0];
        default:
            // Por defecto mostrar año actual (como en la vista vieja de estadísticas)
            return new Date(hoy.getFullYear(), 0, 1)
                .toISOString()
                .split("T")[0];
    }
}

function getFechaFin() {
    const hoy = new Date();
    switch (periodoActual) {
        case "mes":
            return new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0)
                .toISOString()
                .split("T")[0];
        case "trimestre":
            return new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0)
                .toISOString()
                .split("T")[0];
        case "año":
            return new Date(hoy.getFullYear(), 11, 31)
                .toISOString()
                .split("T")[0];
        default:
            // Por defecto mostrar año actual (como en la vista vieja de estadísticas)
            return new Date(hoy.getFullYear(), 11, 31)
                .toISOString()
                .split("T")[0];
    }
}

function formatearNumero(numero) {
    return new Intl.NumberFormat("es-AR").format(numero);
}

function formatearMoneda(monto) {
    return currency(monto, {
        separator: ".",
        decimal: ",",
        precision: 2,
    }).format();
}

// Hacer funciones globales para uso en HTML
window.cambiarPeriodo = cambiarPeriodo;
window.actualizarDashboard = actualizarDashboard;
