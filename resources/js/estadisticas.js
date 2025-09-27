window.$ = $;
window.jQuery = $;

import "datatables.net-bs5";
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";

import "datatables.net-responsive";
import "datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css";
import "datatables.net-responsive-bs5";

import "datatables.net-buttons"; // core buttons
import "datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css";
import "datatables.net-buttons-bs5"; // bootstrap5 buttons integration

import "datatables.net-buttons/js/buttons.html5.js";
import "datatables.net-buttons/js/buttons.colVis.js";

import "jszip";
// ✅ Y su CSS
import "bootstrap-icons/font/bootstrap-icons.css";

import SlimSelect from "slim-select";
import currency from "currency.js";

async function obtenerFacturas() {
    try {
        const respuesta = await fetch("/home/facturas", {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });

        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}`);
        }

        // Intenta parsear como JSON sí o sí
        const datos = await respuesta.json();
        console.log("[obtenerFacturas] OK:", datos);
        return datos.data;
    } catch (error) {
        console.error("[obtenerFacturas] Error:", error);
        return null;
    }
}

let chart = null;

// ==============================================
function construirOpciones(
    seriesData,
    categorias,
    tituloSerie = "Facturas emitidas"
) {
    // ==============================================
    return {
        series: [
            { name: tituloSerie, data: seriesData },
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
                    val == null ? "" : `${val} factura${val === 1 ? "" : "s"}`,
            },
        },
    };
}

// ==============================================
async function dibujarGraficoFacturas(intervalo = "anio") {
    // ==============================================
    const el = document.querySelector("#reports2");
    if (!el) return;

    const facturas = await obtenerFacturas();
    if (!Array.isArray(facturas)) return;

    const hoy = new Date();
    const anioActual = hoy.getFullYear();
    const mesActual = hoy.getMonth();

    let categorias = [];
    let data = [];

    switch (intervalo) {
        case "semana": {
            // Últimos 7 días
            categorias = [];
            data = [];
            for (let i = 6; i >= 0; i--) {
                const d = new Date();
                d.setDate(hoy.getDate() - i);
                const etiqueta = d.toLocaleDateString("es-ES", {
                    day: "2-digit",
                    month: "short",
                });
                categorias.push(etiqueta);

                const count = facturas.filter((f) => {
                    const fd = new Date(f.fecha);
                    return !isNaN(fd) && fd.toDateString() === d.toDateString();
                }).length;
                data.push(count);
            }
            break;
        }

        case "mes": {
            // Días del mes actual
            const diasEnMes = new Date(anioActual, mesActual + 1, 0).getDate();
            categorias = [];
            data = [];

            for (let dia = 1; dia <= diasEnMes; dia++) {
                const d = new Date(anioActual, mesActual, dia);
                // Ejemplo: "1 may", "2 may"
                const etiqueta = d.toLocaleDateString("es-ES", {
                    day: "numeric",
                    month: "short",
                });
                categorias.push(etiqueta);

                const count = facturas.filter((f) => {
                    const fd = new Date(f.fecha);
                    return (
                        !isNaN(fd) &&
                        fd.getFullYear() === anioActual &&
                        fd.getMonth() === mesActual &&
                        fd.getDate() === dia
                    );
                }).length;
                data.push(count);
            }
            break;
        }

        case "anio": {
            // 12 meses
            categorias = [
                "Ene",
                "Feb",
                "Mar",
                "Abr",
                "May",
                "Jun",
                "Jul",
                "Ago",
                "Sep",
                "Oct",
                "Nov",
                "Dic",
            ];
            data = Array(12).fill(0);
            facturas.forEach((f) => {
                const fd = new Date(f.fecha);
                if (!isNaN(fd) && fd.getFullYear() === anioActual) {
                    data[fd.getMonth()]++;
                }
            });
            break;
        }
    }

    if (chart) {
        chart.updateOptions(construirOpciones(data, categorias));
    } else {
        chart = new ApexCharts(el, construirOpciones(data, categorias));
        await chart.render();
    }
}

// ============================
// Manejar clicks del dropdown
// ============================
document.addEventListener("DOMContentLoaded", () => {
    dibujarGraficoFacturas("anio"); // por defecto

    document.querySelectorAll("#intervalos .dropdown-item").forEach((item) => {
        item.addEventListener("click", (e) => {
            e.preventDefault();
            const intervalo = e.target.dataset.intervalo;
            const texto = e.target.textContent;
            document.querySelector("#intervalo-texto").textContent = texto;
            dibujarGraficoFacturas(intervalo);
        });
    });
}); // ============================
// Obtener datos resumen (polar area)
// ============================
async function obtenerResumen(intervalo = "anio") {
    try {
        const resp = await fetch(`/home/caja?intervalo=${intervalo}`);
        if (!resp.ok) throw new Error("HTTP " + resp.status);
        return await resp.json();
    } catch (e) {
        console.error("[obtenerResumen] Error:", e);
        return null;
    }
}

let chartCaja = null;

// ============================
// Dibujar grafico polarArea
// ============================
async function dibujarGraficoCaja(intervalo = "anio") {
    const datos = await obtenerResumen(intervalo);
    if (!datos) return;

    const series = [
        datos.totalCobrado,
        datos.totalGastado,
        datos.ventasPorCobrar,
    ];

    const options = {
        series: series,
        chart: {
            type: "polarArea",
            width: 420,
        },
        labels: ["Total Cobrado", "Total Gastado", "Ventas por Cobrar"],
        stroke: {
            colors: ["rgba(255, 255, 255, 0.01)"],
        },
        fill: { opacity: 0.9 },
        colors: [
            "var(--bs-success)", // verde
            "var(--bs-danger)", // rojo
            "var(--bs-warning)", // amarillo
        ],
        plotOptions: {
            polarArea: {
                rings: {
                    strokeWidth: 1,
                    strokeColor: "rgba(137, 149, 165, 0.2)",
                },
                spokes: {
                    strokeWidth: 1,
                    strokeColor: "rgba(137, 149, 165, 0.2)",
                },
            },
        },
        legend: {
            position: "bottom",
            labels: { colors: "var(--bs-body-color)" },
        },
        tooltip: {
            y: { formatter: (val) => "$" + val.toLocaleString() },
        },
        responsive: [
            {
                breakpoint: 480,
                options: {
                    chart: { width: 250 },
                    legend: { position: "bottom" },
                },
            },
        ],
    };

    if (chartCaja) {
        chartCaja.updateOptions(options);
    } else {
        chartCaja = new ApexCharts(
            document.querySelector("#grafico_caja"),
            options
        );
        await chartCaja.render();
    }
}

// ============================
// Manejar clicks del dropdown de caja
// ============================
document.addEventListener("DOMContentLoaded", () => {
    dibujarGraficoCaja("anio"); // por defecto

    document.querySelectorAll("#intervalos2 .dropdown-item").forEach((item) => {
        item.addEventListener("click", (e) => {
            e.preventDefault();
            const intervalo = e.target.dataset.intervalo;
            const texto = e.target.textContent;
            document.querySelector("#intervalo-texto2").textContent = texto;
            dibujarGraficoCaja(intervalo);
        });
    });
});
// ============================
// La primera tarjeta ahora muestra el balance total estáticamente
// desde el controlador, por lo que no necesitamos JavaScript para ella
// ============================
