import $ from "jquery";
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
import "bootstrap-icons/font/bootstrap-icons.css";

import currency from "currency.js";
let tableCreditos;

// Estado global de la aplicación de créditos
const estadoGlobal = {
    tablaPagos: {
        inicializada: false,
        creditoId: null,
    },
    guardandoPago: false,
};

// Funciones globales se expondrán al final del archivo

$(document).ready(function () {
    inicializarTabla();
    inicializarSelects();
    inicializarEventos();
    cargarEstadisticas();
});

function inicializarTabla() {
    const columnasCreditos = [
        { data: "id", name: "id" },
        { data: "cliente.nombre", name: "cliente.nombre" },
        {
            data: "monto",
            name: "monto",
            render: function (data, type, row) {
                if (type === "display" || type === "type") {
                    return formatearMoneda(data || 0);
                }
                return data;
            },
        },
        { data: "tipo", name: "tipo" },
        { data: "dias_duracion", name: "dias_duracion" },
        {
            data: "porcentaje",
            name: "porcentaje",
            render: function (data, type, row) {
                if (type === "display" || type === "type") {
                    return parseFloat(data || 0).toFixed(2) + "%";
                }
                return data;
            },
        },
        {
            data: "monto_a_cobrar",
            name: "monto_a_cobrar",
            render: function (data, type, row) {
                if (type === "display" || type === "type") {
                    return formatearMoneda(data || 0);
                }
                return data;
            },
        },
        { data: "fecha_inicio", name: "fecha_inicio" },
        {
            data: "saldo_pendiente",
            name: "saldo_pendiente",
            render: function (data, type, row) {
                if (type === "display" || type === "type") {
                    const saldo = parseFloat(data || 0);
                    const colorClass =
                        saldo > 0 ? "text-danger" : "text-success";
                    return `<span class="${colorClass} fw-medium">${formatearMoneda(
                        saldo
                    )}</span>`;
                }
                return data;
            },
        },
        { data: "estado", name: "estado" },
        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
                return `
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Acciones <i class="las la-angle-down ms-1"></i>
                            </button>
                            <div class="dropdown-menu">
                            <button class="dropdown-item btn_editar_credito" type="button" data-id="${row.id}">Editar</button>
                          
                                <button class="dropdown-item btn_ver_pagos_cronograma" type="button" data-id="${row.id}">Pagos</button>
                            <button class="dropdown-item btn_ver_ajustes_credito" type="button" data-id="${row.id}">Ajustes (Desc./Recargos)</button>
                                <div class="dropdown-divider"></div>
                            <button class="dropdown-item text-danger btn_eliminar_credito" type="button" data-id="${row.id}">Eliminar</button>
                            </div>
                        </div>
                    `;
            },
        },
    ];

    const configCreditos = crearConfiguracionDataTable({
        ajax: {
            url: "/api/leyma-creditos/creditos/data",
            type: "GET",
            dataSrc: "data",
        },
        columns: columnasCreditos,
        order: [[0, "desc"]], // Ordenar por ID descendente por defecto
    });

    tableCreditos = $("#creditos_table").DataTable(configCreditos);
}

function inicializarSelects() {
    // Inicializar select de clientes
    fetch("/api/leyma-creditos/clientes/data-tom")
        .then((response) => response.json())
        .then((clientes) => {
            const select = $("#cliente_id");
            select.empty();
            select.append('<option value="">Seleccionar cliente</option>');

            clientes.forEach((cliente) => {
                select.append(
                    `<option value="${cliente.value}">${cliente.text}</option>`
                );
            });

            console.log("Select de clientes inicializado correctamente");

            // Evento cuando se selecciona un cliente
            $("#cliente_id").on("change", function () {
                const clienteId = $(this).val();
                if (clienteId) {
                    cargarDatosCliente(clienteId);
                }
            });
        })
        .catch((error) => {
            console.error("Error al inicializar select de clientes:", error);
        });

    // Inicializar select de usuarios
    fetch("/api/leyma-creditos/creditos/create")
        .then((response) => response.json())
        .then((data) => {
            const usuarios = data.usuarios || [];
            const select = $("#usuario_id");
            select.empty();
            select.append('<option value="">Seleccionar usuario</option>');

            usuarios.forEach((usuario) => {
                select.append(
                    `<option value="${usuario.id}">${usuario.name}</option>`
                );
            });

            console.log("Select de usuarios inicializado correctamente");
        })
        .catch((error) => {
            console.error("Error al inicializar select de usuarios:", error);
        });
}

function cargarDatosCliente(clienteId) {
    console.log("Cargando datos del cliente:", clienteId);
    fetch(`/api/leyma-creditos/clientes/${clienteId}`)
        .then((response) => response.json())
        .then((cliente) => {
            console.log("Datos del cliente recibidos:", cliente);
            // Aquí se pueden cargar otros datos del cliente si es necesario
        })
        .catch((error) => {
            console.error("Error al cargar datos del cliente:", error);
        });
}

function inicializarEventos() {
    // Formulario de crédito
    $("#formCredito").on("submit", function (e) {
        e.preventDefault();
        guardarCredito();
    });

    // Limpiar formulario al cerrar modal
    $("#modalCredito").on("hidden.bs.modal", function () {
        limpiarFormulario();
    });

    // Event listeners para cálculos automáticos
    $("#tipo_pago").on("change", function () {
        const tipoCredito = $(this).val();
        const cantidadCuotasInput = $("#cantidad_cuotas");

        // Limitar cantidad de cuotas para contado
        if (tipoCredito === "contado") {
            // Contado
            cantidadCuotasInput.val(1).prop("readonly", true);
            cantidadCuotasInput.attr("max", 1);
            cantidadCuotasInput.attr("min", 1);
        } else {
            cantidadCuotasInput.prop("readonly", false);
            cantidadCuotasInput.removeAttr("max");
            cantidadCuotasInput.attr("min", 1);
        }

        ejecutarTodosLosCalculos();
    });

    $("#cantidad_cuotas").on("input", function () {
        ejecutarTodosLosCalculos();
    });

    $("#monto_principal").on("input", function () {
        calcularMontoFinal();
    });

    $("#porcentaje_base").on("input", function () {
        calcularMontoFinal();
    });

    $("#fecha_inicio").on("change", function () {
        calcularFechaFinal();
    });

    $("#fecha_final").on("change", function () {
        // Recalcular monto final cuando cambie la fecha final
        calcularMontoFinal();
    });

    // Ejecutar cálculos iniciales cuando se abre el modal
    $("#modalCredito").on("shown.bs.modal", function () {
        // Ejecutar cálculos si ya hay valores
        setTimeout(function () {
            ejecutarTodosLosCalculos();
        }, 100);
    });

    // Event listeners para gestión de pagos de crédito
    $("#btn_agregar_pago_credito").on("click", function () {
        abrirModalAgregarPago();
    });

    $("#formAgregarPagoCredito").on("submit", function (e) {
        e.preventDefault();
        guardarPagoCredito();
    });

    // Actualizar resumen cuando cambia el monto del pago
    $("#monto_pago_credito").on("input", function () {
        actualizarResumenPagoCredito();
    });

    // Limpiar formulario al cerrar modal de agregar pago
    $("#modalAgregarPagoCredito").on("hidden.bs.modal", function () {
        $("#formAgregarPagoCredito")[0].reset();
        // Resetear campos ocultos
        $('input[name="tipo_pago"]').val("");
        $('input[name="cuotas_afectadas"]').val("");
        $("#resumen_monto_credito").text(formatearMoneda(0));
        $("#resumen_saldo_despues_credito").text(formatearMoneda(0));
        $("#resumen_estado_final_credito").text("—");
        $("#formAgregarPagoCredito").removeData("credito-id");
    });

    // Event listeners para el modal de pago de cuotas
    $("#formPagoCuota").on("submit", function (e) {
        e.preventDefault();
        guardarPagoCuota();
    });

    // Event listeners para el modal de pago de cuotas
    $("#monto_pago_cuota").on("input", function () {
        actualizarResumenPagoCuota();
    });

    $("#pagar_monto_exacto").on("change", function () {
        if ($(this).is(":checked")) {
            const montoProgramado =
                parseFloat($("#formPagoCuota").data("monto-programado")) || 0;
            $("#monto_pago_cuota").val(montoProgramado.toFixed(2));
            actualizarResumenPagoCuota();
        }
    });

    // Limpiar formulario al cerrar modal de pago de cuotas
    $("#modalPagoCuota").on("hidden.bs.modal", function () {
        $("#formPagoCuota")[0].reset();
        $("#resumen_monto_programado").text(formatearMoneda(0));
        $("#resumen_monto_a_pagar").text(formatearMoneda(0));
        $("#resumen_diferencia").text(formatearMoneda(0));
        $("#formPagoCuota").removeData(
            "credito-id monto-programado cuota-numero credito-id-modal"
        );
    });

    // Resetear estado de tablas al cerrar modal principal
    $("#modalPagosCronograma").on("hidden.bs.modal", function () {
        resetearEstadoTablas();
    });

    // Event listeners para editar y eliminar pagos
    $(document).on("click", ".btn_editar_pago_credito", function () {
        const id = $(this).data("id");
        editarPagoCredito(id);
    });

    $(document).on("click", ".btn_eliminar_pago_credito", function () {
        const id = $(this).data("id");
        eliminarPagoCredito(id);
    });

    // Ajustes de crédito (descuentos/recargos)
    $(document).on("click", ".btn_ver_pagos_cronograma", function () {
        const id = $(this).attr("data-id");
        if (!id) return;
        verPagosCronograma(id);
    });

    $(document).on("click", ".btn_ver_ajustes_credito", function () {
        const id = $(this).attr("data-id");
        if (!id) return;
        verAjustesCredito(id);
    });

    $(document).on("click", "#btn_agregar_ajuste_credito", function () {
        const creditoId = $("#modalAjustesCredito").data("credito-id");
        if (!creditoId) return;
        $("#formAgregarAjusteCredito").data("credito-id", creditoId);
        $("#formAgregarAjusteCredito")[0].reset();
        $("#modalAgregarAjusteCredito").modal("show");
    });

    // Validar monto del descuento en tiempo real
    function validarMontoAjuste() {
        const monto = parseFloat($("#aj_monto").val()) || 0;
        const tipo = $("#aj_tipo").val();
        const creditoId = $("#modalAjustesCredito").data("credito-id");

        if (tipo === "descuento" && creditoId && monto > 0) {
            // Obtener saldo actual del crédito
            fetch(`/api/leyma-creditos/creditos/${creditoId}`)
                .then((response) => response.json())
                .then((data) => {
                    const saldoPendiente =
                        parseFloat(data.saldo_pendiente) || 0;

                    if (monto > saldoPendiente) {
                        $("#aj_monto").addClass("is-invalid");
                        $("#aj_monto").next(".invalid-feedback").remove();
                        $("#aj_monto").after(`
                            <div class="invalid-feedback d-block">
                                El descuento (${formatearMoneda(
                                    monto
                                )}) no puede ser mayor que el saldo pendiente (${formatearMoneda(
                            saldoPendiente
                        )})
                            </div>
                        `);
                        $("#btn_guardar_ajuste").prop("disabled", true);
                    } else {
                        $("#aj_monto").removeClass("is-invalid");
                        $("#aj_monto").next(".invalid-feedback").remove();
                        $("#btn_guardar_ajuste").prop("disabled", false);
                    }
                })
                .catch((error) => {
                    console.error("Error al validar monto:", error);
                    $("#aj_monto").removeClass("is-invalid");
                    $("#aj_monto").next(".invalid-feedback").remove();
                    $("#btn_guardar_ajuste").prop("disabled", false);
                });
        } else {
            $("#aj_monto").removeClass("is-invalid");
            $("#aj_monto").next(".invalid-feedback").remove();
            $("#btn_guardar_ajuste").prop("disabled", false);
        }
    }

    // Event listeners para validación
    $("#aj_monto").on("input", validarMontoAjuste);
    $("#aj_tipo").on("change", validarMontoAjuste);

    $(document).on("submit", "#formAgregarAjusteCredito", function (e) {
        e.preventDefault();
        guardarAjusteCredito();
    });

    $(document).on("submit", "#formAgregarPagoCredito", function (e) {
        e.preventDefault();
        guardarPagoCredito();
    });

    // Limpiar validaciones al cerrar modal de agregar ajuste
    $("#modalAgregarAjusteCredito").on("hidden.bs.modal", function () {
        $("#aj_monto").removeClass("is-invalid");
        $("#aj_monto").next(".invalid-feedback").remove();
        $("#btn_guardar_ajuste").prop("disabled", false);
    });

    // Limpiar validaciones al cerrar modal de agregar pago
    $("#modalAgregarPagoCredito").on("hidden.bs.modal", function () {
        $("#monto_pago_credito").removeClass("is-invalid");
        $("#monto_pago_credito").next(".invalid-feedback").remove();
        $("#btn_guardar_pago").prop("disabled", false);
    });

    // Resetear checkboxes cuando se cierre el modal de cronograma
    $("#modalPagosCronograma").on("hidden.bs.modal", function () {
        $("#select_all_cuotas").prop("checked", false);
        $("#cronograma_table tbody .cuota-checkbox").prop("checked", false);
        actualizarEstadoBotonPagarMultiple();
    });

    // También resetear cuando se abra el modal (por si acaso)
    $("#modalPagosCronograma").on("show.bs.modal", function () {
        // Pequeño delay para asegurar que la tabla esté renderizada
        setTimeout(() => {
            actualizarEstadoBotonPagarMultiple();
        }, 100);
    });

    // Event listener para pagar cuotas seleccionadas
    $(document).on("click", "#btn_pagar_cuotas_seleccionadas", function () {
        pagarCuotasSeleccionadas();
    });

    // Event listener para pagar cuotas seleccionadas desde header
    $(document).on("click", "#btn_pagar_seleccion_header", function () {
        pagarCuotasSeleccionadas();
    });
}

function ejecutarTodosLosCalculos() {
    // Ejecutar todos los cálculos en orden
    calcularFechaFinal();
    calcularMontoFinal();
}

function calcularMontoFinal() {
    const monto = parseFloat($("#monto_principal").val()) || 0;
    const porcentajeBase = parseFloat($("#porcentaje_base").val()) || 0;

    // Solo calcular si hay monto válido
    if (monto <= 0) {
        $("#monto_a_cobrar").val("");
        return;
    }

    // Solo calcular si hay porcentaje válido
    if (porcentajeBase <= 0) {
        $("#monto_a_cobrar").val("");
        return;
    }

    const montoFinal = monto + (monto * porcentajeBase) / 100;
    $("#monto_a_cobrar").val(formatearMoneda(montoFinal));
}

function calcularFechaFinal() {
    const fechaInicio = $("#fecha_inicio").val();
    const tipoCredito = $("#tipo_pago").val();
    const cantidadCuotas = parseInt($("#cantidad_cuotas").val()) || 0;

    // Solo calcular si hay valores válidos
    if (!fechaInicio || !tipoCredito || cantidadCuotas <= 0) {
        $("#fecha_final").val("");
        return;
    }

    const fecha = new Date(fechaInicio);
    let dias = 0;

    // Calcular días basado en tipo y cantidad de cuotas
    switch (tipoCredito) {
        case "1": // Diario
            dias = cantidadCuotas;
            break;
        case "2": // Semanal
            dias = cantidadCuotas * 7;
            break;
        case "3": // Quincenal
            dias = cantidadCuotas * 15;
            break;
        case "4": // Contado
            // Para contado, no calcular automáticamente la fecha final
            // Se debe establecer manualmente la fecha de vencimiento
            return; // No modificar la fecha final para contado
        default:
            dias = cantidadCuotas;
    }

    fecha.setDate(fecha.getDate() + dias);

    const fechaFinal = fecha.toISOString().split("T")[0];
    $("#fecha_final").val(fechaFinal);
}

function guardarCredito() {
    const formData = new FormData($("#formCredito")[0]);
    const id = $("#formCredito").attr("data-id");
    const url = id
        ? `/api/leyma-creditos/creditos/${id}`
        : "/api/leyma-creditos/creditos";
    // Siempre usar POST y spoofear método en edición para máxima compatibilidad
    const method = "POST";

    // Asegurar _method=PUT cuando es edición
    if (id) {
        formData.set("_method", "PUT");
    }

    // Los valores ya están en formData automáticamente

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            Swal.fire({
                title: "Éxito",
                text: id
                    ? "Crédito actualizado correctamente"
                    : "Crédito guardado correctamente",
                icon: "success",
                confirmButtonText: "OK",
            });
            $("#modalCredito").modal("hide");
            // Recargar tabla principal
            tableCreditos.ajax.reload();
            // Actualizar cards de estadísticas
            cargarEstadisticas();
            // Si el modal unificado está abierto, refrescar sus cards y tablas
            const $modalUnificado = $("#modalPagosCronograma");
            const unificadoAbierto =
                $modalUnificado.is(":visible") ||
                $modalUnificado.hasClass("show");
            const unificadoCreditoId = $modalUnificado.data("credito-id");
            if (unificadoAbierto && unificadoCreditoId) {
                verPagosCronograma(unificadoCreditoId);
            }
        },
        error: function (xhr) {
            let mensaje = "Error al guardar el crédito";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                mensaje = xhr.responseJSON.message;
            }
            Swal.fire({
                title: "Error",
                text: mensaje,
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

// Event listeners para los dropdown items
$(document).on("click", ".btn_editar_credito", async function () {
    const id = $(this).attr("data-id");
    if (!id) return false;

    try {
        Swal.fire({
            title: "Cargando...",
            html: "Verificando si el crédito puede editarse",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        const resp = await fetch(
            `/api/leyma-creditos/creditos/${id}?_=${Date.now()}`
        );
        const data = await resp.json();

        Swal.close();

        const tienePagos = Array.isArray(data.pagos) && data.pagos.length > 0;
        if (tienePagos) {
            Swal.fire({
                icon: "warning",
                title: "Edición no permitida",
                html: "Este crédito ya tiene pagos asociados. Para evitar inconsistencias, no se puede editar.",
                showCancelButton: true,
                confirmButtonText: "Ver cronograma",
                cancelButtonText: "Cerrar",
            }).then((r) => {
                if (r.isConfirmed) {
                    verCronogramaCredito(id);
                }
            });
            return;
        }

        // Si NO tiene pagos, proceder con la edición normal
        editarCredito(id);
    } catch (e) {
        console.error(e);
        Swal.close();
        editarCredito(id); // fallback: intentar abrir edición
    }
});

$(document).on("click", ".btn_ver_credito", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    verCredito(id);
});

$(document).on("click", ".btn_ver_pagos_credito", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    verPagosCredito(id);
});

$(document).on("click", ".btn_ver_pagos_credito", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    verPagosCronograma(id);
});

// Event listener para pagar cuota desde cronograma
$(document).on("click", ".btn_pagar_cuota", function () {
    var cuota = $(this).attr("data-cuota");
    var fecha = $(this).attr("data-fecha");
    var monto = $(this).attr("data-monto");

    if (
        cuota == "" ||
        cuota == undefined ||
        fecha == "" ||
        fecha == undefined ||
        monto == "" ||
        monto == undefined
    ) {
        return false;
    }

    abrirModalPagoCuota(cuota, fecha, monto);
});

$(document).on("click", ".btn_eliminar_credito", function () {
    var id = $(this).attr("data-id");
    if (id == "" || id == undefined) {
        return false;
    }
    eliminarCredito(id);
});

function verCredito(id) {
    $.ajax({
        url: `/api/leyma-creditos/creditos/${id}`,
        type: "GET",
        success: function (response) {
            // Mostrar datos en modal de visualización
            mostrarDetallesCredito(response);
        },
        error: function () {
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del crédito",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function editarCredito(id) {
    $.ajax({
        url: `/api/leyma-creditos/creditos/${id}`,
        type: "GET",
        success: function (response) {
            // Llenar formulario con datos existentes
            $("#modalCreditoLabel").text("Editar Crédito");
            $("#formCredito").attr("data-id", id);

            // Establecer cliente seleccionado
            $("#cliente_id").val(String(response.cliente_id)).trigger("change");

            // Establecer usuario seleccionado
            $("#usuario_id")
                .val(String(response.usuario_id || response.user_id))
                .trigger("change");
            $("#monto_principal").val(response.monto_principal);
            $("#tipo_pago").val(response.tipo_pago);
            $("#cantidad_cuotas").val(response.cantidad_cuotas);
            $("#porcentaje_base").val(response.porcentaje_base);
            // Normalizar fechas a YYYY-MM-DD (evita value inválido en input date)
            const fi = response.fecha_inicio
                ? new Date(response.fecha_inicio).toISOString().split("T")[0]
                : "";
            const ffSrc = response.fecha_final || response.fecha_vencimiento;
            const ff = ffSrc ? new Date(ffSrc).toISOString().split("T")[0] : "";
            $("#fecha_inicio").val(fi);
            $("#fecha_final").val(ff);
            $("#dia_pago").val(response.dia_pago);
            $("#observaciones").val(response.observaciones);

            ejecutarTodosLosCalculos();

            // Cambiar texto del botón a Guardar cambios y asegurar method PUT
            $("#formCredito button[type='submit']").text("Guardar cambios");
            $("#modalCredito").modal("show");
        },
        error: function () {
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del crédito",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
}

function verAjustesCredito(id) {
    // Cargar información del crédito y sus ajustes
    cargarInformacionCredito(id)
        .then((data) => {
            // Guardar ID del crédito en el modal
            $("#modalAjustesCredito").data("credito-id", id);

            const montoPrincipal = parseFloat(data.monto_principal || 0);
            const porcentaje = parseFloat(data.porcentaje_base || 0);
            const baseTotal =
                montoPrincipal + (montoPrincipal * porcentaje) / 100;
            const pagado = parseFloat(data.monto_pagado || 0);
            const totalRecargos = (data.recargos || []).reduce(
                (s, r) => s + (parseFloat(r.monto) || 0),
                0
            );
            const totalDescuentos = (data.descuentos || []).reduce(
                (s, d) => s + (parseFloat(d.monto) || 0),
                0
            );
            const ajustes = totalRecargos - totalDescuentos;
            const totalFinal = baseTotal + (pagado >= baseTotal ? ajustes : 0);

            $("#aj_base_total").text(formatearMoneda(baseTotal));
            $("#aj_pagado").text(formatearMoneda(pagado));
            $("#aj_ajustes").text(
                (ajustes >= 0 ? "+ " : "- ") +
                    formatearMoneda(Math.abs(ajustes))
            );
            $("#aj_total_final").text(formatearMoneda(totalFinal));

            // Verificar si puede aplicar ajustes (descuentos si hay saldo, recargos siempre)
            const saldoPendiente = parseFloat(data.saldo_pendiente) || 0;
            const puedeAplicarDescuento = saldoPendiente > 0;
            const puedeAplicarRecargo = true; // Los recargos siempre se pueden aplicar

            // Bloquear solo si es descuento y no hay saldo pendiente
            const esDescuento = $("#aj_tipo").val() === "descuento";
            const puedeAjustar = esDescuento
                ? puedeAplicarDescuento
                : puedeAplicarRecargo;

            $("#btn_agregar_ajuste_credito").prop("disabled", !puedeAjustar);

            if (esDescuento && !puedeAplicarDescuento) {
                $("#ajuste_alert_base").removeClass("d-none").html(`
                    No puedes aplicar descuentos cuando el crédito está completamente pagado.
                    <br><small class="text-muted">El saldo pendiente es: ${formatearMoneda(
                        saldoPendiente
                    )}</small>
                `);
            } else if (esDescuento) {
                $("#ajuste_alert_base").addClass("d-none");
            }

            // Renderizar tabla de ajustes
            const rows = [];
            (data.recargos || []).forEach((r) => {
                rows.push({
                    fecha: r.fecha_aplicacion || "",
                    tipo: "Recargo",
                    concepto: r.concepto || "",
                    monto: parseFloat(r.monto) || 0,
                    observaciones: r.observaciones || "",
                    usuario: r.usuario?.name || "—",
                });
            });
            (data.descuentos || []).forEach((d) => {
                rows.push({
                    fecha: d.fecha_aplicacion || "",
                    tipo: "Descuento",
                    concepto: d.concepto || "",
                    monto: parseFloat(d.monto) || 0,
                    observaciones: d.observaciones || "",
                    usuario: d.usuario?.name || "—",
                });
            });

            const tbody = $("#ajustes_credito_table tbody");
            tbody.empty();
            rows.sort((a, b) => new Date(a.fecha) - new Date(b.fecha)).forEach(
                (row) => {
                    const color = row.tipo === "Recargo" ? "danger" : "success";
                    const tr = `
                    <tr>
                        <td>${
                            row.fecha
                                ? new Date(row.fecha).toLocaleDateString(
                                      "es-AR"
                                  )
                                : "—"
                        }</td>
                        <td><span class="badge bg-${color}">${
                        row.tipo
                    }</span></td>
                        <td>${row.concepto}</td>
                        <td>${formatearMoneda(row.monto)}</td>
                        <td>${row.observaciones}</td>
                        <td>${row.usuario}</td>
                    </tr>`;
                    tbody.append(tr);
                }
            );

            $("#modalAjustesCredito").modal("show");
        })
        .catch(() => {
            // Error ya manejado en cargarInformacionCredito
        });
}

async function guardarAjusteCredito() {
    const creditoId = $("#formAgregarAjusteCredito").data("credito-id");
    const tipo = $("#aj_tipo").val();
    const monto = parseFloat($("#aj_monto").val()) || 0;
    const concepto = $("#aj_concepto").val();
    const observaciones = $("#aj_obs").val() || "";

    if (!creditoId || !tipo || !monto || !concepto) return;

    const endpoint =
        tipo === "recargo"
            ? "/api/leyma-creditos/aplicar-recargo"
            : "/api/leyma-creditos/aplicar-descuento";
    try {
        const resp = await fetch(endpoint, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                credito_id: creditoId,
                monto,
                concepto,
                observaciones,
            }),
        });
        const data = await resp.json();
        if (!resp.ok || !data.success)
            throw new Error(data.message || "Error en el alta del ajuste");

        $("#modalAgregarAjusteCredito").modal("hide");
        verAjustesCredito(creditoId);
        // refrescar cards principales
        tableCreditos?.ajax?.reload();
        cargarEstadisticas();

        // Recargar información del crédito en el modal unificado si está abierto
        const $modalUnificado = $("#modalPagosCronograma");
        const unificadoAbierto =
            $modalUnificado.is(":visible") || $modalUnificado.hasClass("show");
        const unificadoCreditoId = $modalUnificado.data("credito-id");
        if (unificadoAbierto && unificadoCreditoId === creditoId) {
            verPagosCronograma(creditoId);
        }
    } catch (e) {
        Swal.fire(
            "Error",
            e.message || "No se pudo guardar el ajuste",
            "error"
        );
    }
}
function inicializarTablaPagos(creditoId) {
    // Verificar que el elemento existe
    const tablaElement = $("#pagos_credito_table");
    if (!tablaElement.length) {
        console.error("Elemento #pagos_credito_table no encontrado");
        return;
    }

    // Si la tabla ya está inicializada para el mismo crédito, solo recargar datos
    if (
        estadoGlobal.tablaPagos.inicializada &&
        estadoGlobal.tablaPagos.creditoId === creditoId &&
        $.fn.DataTable.isDataTable("#pagos_credito_table")
    ) {
        console.log(
            "Recargando datos de DataTable existente para crédito:",
            creditoId
        );
        $("#pagos_credito_table").DataTable().ajax.reload();
        return;
    }

    // Destruir tabla existente si existe
    if ($.fn.DataTable.isDataTable("#pagos_credito_table")) {
        try {
            $("#pagos_credito_table").DataTable().destroy();
            tablaElement.empty();
            estadoGlobal.tablaPagos.inicializada = false; // Reset flag
        } catch (error) {
            console.error("Error al destruir DataTable:", error);
        }
    }

    // Inicializar nueva tabla en el modal unificado
    try {
        tablaElement.DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: `/api/leyma-creditos/creditos/${creditoId}/pagos`,
                type: "GET",
                dataSrc: "data",
                error: function (xhr, error, thrown) {
                    console.error("Error en DataTable:", error, thrown);
                    console.error("Response:", xhr.responseText);
                },
            },
            columns: [
                { data: "id", name: "id" },
                {
                    data: "fecha_pago",
                    name: "fecha_pago",
                    render: function (data) {
                        return data
                            ? new Date(data).toLocaleDateString("es-AR")
                            : "-";
                    },
                },
                {
                    data: "monto",
                    name: "monto",
                    render: function (data) {
                        return formatearMoneda(data || 0);
                    },
                },
                { data: "metodo_pago", name: "metodo_pago" },
                { data: "observaciones", name: "observaciones" },
                { data: "usuario.name", name: "usuario.name" },
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Acciones <i class="las la-angle-down ms-1"></i>
                            </button>
                            <div class="dropdown-menu">
                                    <button class="dropdown-item text-danger btn_eliminar_pago_credito" data-id="${row.id}">Eliminar</button>
                            </div>
                        </div>
                    `;
                    },
                },
            ],
            language: {
                url: "/js/datatables/i18n/es-ES.json",
            },
            responsive: true,
            pageLength: 10,
            order: [[0, "desc"]],
            initComplete: function () {
                console.log(
                    "DataTable de pagos inicializada correctamente para crédito:",
                    creditoId
                );
                estadoGlobal.tablaPagos.inicializada = true;
                estadoGlobal.tablaPagos.creditoId = creditoId;
            },
        });
    } catch (error) {
        console.error("Error al inicializar DataTable de pagos:", error);
    }
}

function parsearMoneda(texto) {
    // Remover símbolos de moneda y espacios
    let numero = texto.replace(/[$,\s]/g, "");

    // En formato argentino, el punto es separador de miles
    // Si hay un punto, asumimos que es separador de miles
    if (numero.includes(".")) {
        // Remover el punto (separador de miles)
        numero = numero.replace(/\./g, "");
    }

    return parseFloat(numero) || 0;
}

function actualizarResumenPago() {
    const montoPago = parseFloat($("#monto_pago").val()) || 0;
    const saldoPendiente = parsearMoneda($("#info_saldo_pendiente").text());

    $("#resumen_monto").text(formatearMoneda(montoPago));

    const saldoDespues = saldoPendiente - montoPago;
    $("#resumen_saldo_despues").text(formatearMoneda(saldoDespues));

    if (saldoDespues <= 0) {
        $("#resumen_estado_final")
            .text("Crédito Completado")
            .removeClass("text-warning text-danger")
            .addClass("text-success");
    } else if (saldoDespues < saldoPendiente * 0.5) {
        $("#resumen_estado_final")
            .text("Casi Completado")
            .removeClass("text-success text-danger")
            .addClass("text-warning");
    } else {
        $("#resumen_estado_final")
            .text("Pendiente")
            .removeClass("text-success text-warning")
            .addClass("text-danger");
    }
}

function eliminarCredito(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "No podrás revertir esta acción",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/leyma-creditos/creditos/${id}`,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    Swal.fire({
                        title: "Eliminado",
                        text: "Crédito eliminado correctamente",
                        icon: "success",
                        confirmButtonText: "OK",
                    });
                    tableCreditos.ajax.reload();
                    cargarEstadisticas();
                    const $modalUnificado = $("#modalPagosCronograma");
                    const unificadoAbierto =
                        $modalUnificado.is(":visible") ||
                        $modalUnificado.hasClass("show");
                    const unificadoCreditoId =
                        $modalUnificado.data("credito-id");
                    if (unificadoAbierto && unificadoCreditoId) {
                        verPagosCronograma(unificadoCreditoId);
                    }
                },
                error: function (xhr) {
                    let mensaje = "Error al eliminar el crédito";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: "Error",
                        text: mensaje,
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        }
    });
}

function limpiarFormulario() {
    $("#formCredito")[0].reset();
    $("#modalCreditoLabel").text("Nuevo Crédito");
    $("#formCredito").removeAttr("data-id");
    $("#monto_a_cobrar").val("");
}

function cargarEstadisticas() {
    fetch("/api/leyma-creditos/creditos/estadisticas")
        .then((response) => response.json())
        .then((data) => {
            // Actualizar las estadísticas en las cards
            $("#creditos_totales").text(data.creditos_totales || 0);
            $("#creditos_activos").text(data.creditos_activos || 0);
            $("#monto_total_prestado").text(
                formatearMoneda(data.monto_total_prestado || 0)
            );
        })
        .catch((error) => {
            console.error("Error al cargar estadísticas:", error);
            // Mostrar valores por defecto en caso de error
            $("#creditos_totales").text("0");
            $("#creditos_activos").text("0");
            $("#monto_total_prestado").text(formatearMoneda(0));
        });
}

function formatearMoneda(monto) {
    return currency(monto, {
        separator: ".",
        decimal: ",",
        precision: 2,
    }).format();
}

// Función helper para cargar información básica del crédito
function cargarInformacionCredito(creditoId) {
    return fetch(`/api/leyma-creditos/creditos/${creditoId}`)
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch((error) => {
            console.error("Error al cargar información del crédito:", error);
            Swal.fire({
                title: "Error",
                text: "No se pudo cargar la información del crédito",
                icon: "error",
                confirmButtonText: "OK",
            });
            throw error;
        });
}

// Función helper para crear configuración común de DataTables
function crearConfiguracionDataTable(opciones = {}) {
    const configPorDefecto = {
        processing: true,
        serverSide: false,
        language: {
            url: "/js/datatables/i18n/es-ES.json",
        },
        responsive: true,
        pageLength: 25,
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100],
        ],
        dom: "<'row mb-2 g-0 justify-content-between'<'col-md-auto dt-length'f><'col-md-auto dt-buttons-search d-flex align-items-center gap-2'Bl>>rtip",
        buttons: [
            {
                extend: "excel",
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: "btn btn-success btn-sm",
            },
            {
                extend: "pdf",
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: "btn btn-danger btn-sm",
            },
            {
                extend: "colvis",
                text: '<i class="fas fa-columns"></i> Columnas',
                className: "btn btn-info btn-sm",
            },
        ],
        initComplete: function () {
            // Agregar clase al info (footer con texto "Mostrando...")
            $(this.api().table().container()).find(".dt-info").addClass("my-2");
        },
    };

    return { ...configPorDefecto, ...opciones };
}

// Función helper para procesar pago genérico
async function procesarPago(datosPago) {
    const {
        formData,
        endpoint = "/api/leyma-creditos/registrar-pago",
        successMessage = "Pago registrado correctamente",
        successCallback = null,
        validationCallback = null,
    } = datosPago;

    // Validación personalizada si existe
    if (validationCallback) {
        const validacion = await validationCallback();
        if (!validacion.valido) {
            return { success: false, message: validacion.mensaje };
        }
    }

    // Mostrar loading
    const submitBtn = datosPago.submitBtn;
    const originalText = submitBtn.html();
    submitBtn
        .html('<i class="fas fa-spinner fa-spin me-1"></i>Guardando...')
        .prop("disabled", true);

    try {
        const response = await fetch(endpoint, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                title: "¡Éxito!",
                text: data.message || successMessage,
                icon: "success",
                confirmButtonText: "Aceptar",
            }).then(() => {
                if (successCallback) {
                    successCallback(data);
                }
            });
            return { success: true, data };
        } else {
            Swal.fire(
                "Error",
                data.message || "Error al procesar el pago",
                "error"
            );
            return { success: false, message: data.message };
        }
    } catch (error) {
        console.error("Error:", error);
        Swal.fire("Error", "Error de conexión al procesar el pago", "error");
        return { success: false, message: "Error de conexión" };
    } finally {
        // Restaurar botón
        submitBtn.html(originalText).prop("disabled", false);
    }
}

// Función para resetear las variables de estado de las tablas
function resetearEstadoTablas() {
    estadoGlobal.tablaPagos.inicializada = false;
    estadoGlobal.tablaPagos.creditoId = null;
}

// Función para actualizar el alert de monto restante
function actualizarMontoRestanteAlert(creditoId) {
    // Obtener información actualizada del crédito
    return cargarInformacionCredito(creditoId)
        .then((credito) => {
            // Generar cronograma para verificar condición del alert
            const cronograma = generarCronograma(credito);

            // Verificar si todas las cuotas tienen pagos asociados
            const tienePagosAsociados = cronograma.every(
                (c) => c.pago_real !== null && c.pago_real !== undefined
            );

            // Buscar el elemento del alert en el modal
            const modalContent = $("#modalPagosCronograma .modal-body");
            const montoRestanteElement = modalContent.find(
                "#monto_restante_alert"
            );

            if (tienePagosAsociados) {
                const sumaPagosReales = parseFloat(credito.monto_pagado) || 0;
                const montoTotal = parseFloat(credito.monto_total) || 0;
                const montoRestante = montoTotal - sumaPagosReales;

                // Usar la función helper común
                crearActualizarAlertMontoRestante(
                    credito,
                    montoRestante,
                    montoTotal,
                    sumaPagosReales
                );
            } else {
                // Ocultar alert si no todas las cuotas tienen pagos
                montoRestanteElement.remove();
            }
        })
        .catch((error) => {
            console.error(
                "Error al actualizar alert de monto restante:",
                error
            );
        });
}

// Función para pagar el monto restante (disponible globalmente)
window.pagarMontoRestante = function (creditoId, montoRestante) {
    console.log("Pagando monto restante:", { creditoId, montoRestante });

    // Abrir modal de agregar pago
    abrirModalAgregarPago();

    // Esperar a que el modal esté abierto y luego configurar el monto
    setTimeout(() => {
        // Establecer el monto restante en el campo de monto
        $("#monto_pago_credito").val(montoRestante.toFixed(2));

        // Configurar campos ocultos requeridos
        $('input[name="credito_id"]').val(creditoId); // ID del crédito
        $('input[name="tipo_pago"]').val("pago_monto_restante"); // Tipo de pago para monto restante
        $('input[name="cuotas_afectadas"]').val(""); // String vacío para pagos generales

        // Configurar método de pago por defecto
        $("#metodo_pago_credito").val("efectivo");

        // Actualizar el resumen del pago
        actualizarResumenPagoCredito();

        // Agregar observación automática
        $("#observaciones_pago_credito").val("Pago de monto restante");

        // Establecer fecha actual
        const hoy = new Date().toISOString().split("T")[0];
        $("#fecha_pago_credito").val(hoy);
    }, 200);
};

function abrirModalAgregarPago() {
    // Obtener el ID del crédito del modal unificado
    const creditoId = $("#modalPagosCronograma").data("credito-id");

    if (!creditoId) {
        Swal.fire("Error", "No se ha seleccionado un crédito", "error");
        return;
    }

    // Establecer fecha actual como predeterminada
    const hoy = new Date().toISOString().split("T")[0];
    $("#fecha_pago_credito").val(hoy);

    // Limpiar formulario
    $("#formAgregarPagoCredito")[0].reset();
    // Resetear campos ocultos y configurar valores por defecto
    $('input[name="tipo_pago"]').val("pago_manual"); // Tipo por defecto para pagos manuales
    $('input[name="cuotas_afectadas"]').val("");
    $("#fecha_pago_credito").val(hoy);
    $("#monto_pago_credito").val("");
    $("#metodo_pago_credito").val("");
    $("#observaciones_pago_credito").val("");

    // Guardar ID del crédito en el formulario
    $("#formAgregarPagoCredito").data("credito-id", creditoId);

    // Establecer máximo basado en saldo pendiente del crédito
    cargarInformacionCredito(creditoId)
        .then((credito) => {
            const saldoPendiente = parseFloat(credito.saldo_pendiente) || 0;
            $("#monto_pago_credito").attr("max", saldoPendiente);
        })
        .catch(() => {
            // En caso de error, establecer un máximo alto por defecto
            $("#monto_pago_credito").attr("max", 999999999);
        });

    // Limpiar ID del pago (para modo agregar)
    $("#formAgregarPagoCredito").removeData("pago-id");

    // Actualizar resumen inicial
    actualizarResumenPagoCredito();

    // Cambiar título del modal
    $("#modalAgregarPagoCreditoLabel").text("Agregar Pago");

    // Mostrar modal
    $("#modalAgregarPagoCredito").modal("show");
}

function actualizarResumenPagoCredito() {
    const montoPago = parseFloat($("#monto_pago_credito").val()) || 0;

    // Obtener el saldo actual del crédito desde el modal
    const creditoId = $("#formAgregarPagoCredito").data("credito-id");
    if (!creditoId) {
        console.error(
            "No se pudo obtener el ID del crédito para calcular el saldo"
        );
        return;
    }

    // Hacer una petición para obtener los datos actualizados del crédito
    fetch(`/api/leyma-creditos/creditos/${creditoId}`)
        .then((response) => response.json())
        .then((credito) => {
            const saldoPendiente = parseFloat(credito.saldo_pendiente) || 0;

            $("#resumen_monto_credito").text(formatearMoneda(montoPago));

            const saldoDespues = saldoPendiente - montoPago;
            $("#resumen_saldo_despues_credito").text(
                formatearMoneda(saldoDespues)
            );

            // Validar que no se exceda el saldo pendiente
            const submitBtn = $(
                "#formAgregarPagoCredito button[type='submit']"
            );

            if (montoPago > saldoPendiente) {
                // Excede el saldo pendiente
                $("#resumen_estado_final_credito")
                    .text("¡Excede el saldo pendiente!")
                    .removeClass("text-success text-warning")
                    .addClass("text-danger");
                submitBtn.prop("disabled", true).addClass("btn-danger");
            } else if (saldoDespues <= 0) {
                // Crédito completado
                $("#resumen_estado_final_credito")
                    .text("Crédito Completado")
                    .removeClass("text-warning text-danger")
                    .addClass("text-success");
                submitBtn.prop("disabled", false).removeClass("btn-danger");
            } else if (saldoDespues <= saldoPendiente * 0.1) {
                // Casi completado (10% o menos del saldo original)
                $("#resumen_estado_final_credito")
                    .text("Casi Completado")
                    .removeClass("text-success text-danger")
                    .addClass("text-warning");
                submitBtn.prop("disabled", false).removeClass("btn-danger");
            } else {
                // Pendiente
                $("#resumen_estado_final_credito")
                    .text("Pendiente")
                    .removeClass("text-success text-warning")
                    .addClass("text-danger");
                submitBtn.prop("disabled", false).removeClass("btn-danger");
            }
        })
        .catch((error) => {
            console.error("Error al obtener saldo del crédito:", error);
        });
}

async function guardarPagoCredito() {
    // Evitar múltiples ejecuciones simultáneas
    if (estadoGlobal.guardandoPago) {
        return;
    }
    estadoGlobal.guardandoPago = true;

    const creditoId = $("#formAgregarPagoCredito").data("credito-id");

    if (!creditoId) {
        Swal.fire("Error", "No se ha seleccionado un crédito", "error");
        guardandoPago = false;
        return;
    }

    // Validar que el monto no exceda el saldo pendiente
    const montoPago = parseFloat($("#monto_pago_credito").val()) || 0;

    // Obtener el saldo pendiente actual del crédito
    let saldoPendiente = 0;
    try {
        const credito = await cargarInformacionCredito(creditoId);
        saldoPendiente = parseFloat(credito.saldo_pendiente) || 0;
    } catch (error) {
        console.error("Error al obtener saldo pendiente:", error);
        saldoPendiente = 0;
    }

    if (montoPago > saldoPendiente) {
        Swal.fire({
            title: "Error de Validación",
            text: `El monto del pago (${formatearMoneda(
                montoPago
            )}) no puede exceder el saldo pendiente (${formatearMoneda(
                saldoPendiente
            )})`,
            icon: "error",
            confirmButtonText: "Aceptar",
        });
        return;
    }

    // Asegurar que los campos ocultos tengan valores válidos
    $('input[name="credito_id"]').val(creditoId);
    $('input[name="tipo_pago"]').val(
        $('input[name="tipo_pago"]').val() || "pago_manual"
    );
    $('input[name="cuotas_afectadas"]').val(
        $('input[name="cuotas_afectadas"]').val() || ""
    );

    // Si método de pago está vacío, configurar efectivo
    if (!$("#metodo_pago_credito").val()) {
        $("#metodo_pago_credito").val("efectivo");
    }

    // Crear FormData del formulario
    const formData = new FormData($("#formAgregarPagoCredito")[0]);

    // Usar función helper para procesar el pago
    const resultado = await procesarPago({
        formData,
        submitBtn: $("#formAgregarPagoCredito button[type='submit']"),
        successMessage: "Pago registrado correctamente",
        validationCallback: async () => {
            // Validación ya realizada arriba
            return { valido: true };
        },
        successCallback: () => {
            // Cerrar modal
            $("#modalAgregarPagoCredito").modal("hide");

            // Recargar información del crédito
            if (creditoId) {
                verPagosCronograma(creditoId);
            }

            // Recargar tabla principal
            tableCreditos.ajax.reload();

            // Recargar estadísticas
            cargarEstadisticas();
        },
    });

    // Resetear bandera de guardado
    estadoGlobal.guardandoPago = false;
}

function editarPagoCredito(id) {
    console.log("Editar pago:", id);

    // Cargar información del pago
    fetch(`/api/leyma-creditos/creditos/pagos/${id}`)
        .then((response) => response.json())
        .then((data) => {
            // Llenar formulario con datos del pago
            $("#monto_pago_credito").val(data.monto);

            // Formatear fecha para input date
            const fecha = data.fecha_pago
                ? new Date(data.fecha_pago).toISOString().split("T")[0]
                : "";
            $("#fecha_pago_credito").val(fecha);

            $("#metodo_pago_credito").val(data.metodo_pago || "");
            $("#observaciones_pago_credito").val(data.observaciones || "");

            // Guardar ID del pago para actualización
            $("#formAgregarPagoCredito").data("pago-id", id);

            // Establecer máximo basado en saldo pendiente + monto actual del pago
            const saldoPendiente = parsearMoneda(
                $("#info_saldo_pendiente_unificado").text()
            );
            const montoActualPago = parseFloat(data.monto) || 0;
            const maximoPermitido = saldoPendiente + montoActualPago;
            $("#monto_pago_credito").attr("max", maximoPermitido);

            // Actualizar resumen
            actualizarResumenPagoCredito();

            // Cambiar título del modal
            $("#modalAgregarPagoCreditoLabel").text("Editar Pago");

            // Mostrar modal
            $("#modalAgregarPagoCredito").modal("show");
        })
        .catch((error) => {
            console.error("Error al cargar información del pago:", error);
            Swal.fire(
                "Error",
                "No se pudo cargar la información del pago",
                "error"
            );
        });
}

function eliminarPagoCredito(id) {
    console.log("Eliminar pago:", id);
    let creditoId; // Variable para mantener el ID del crédito en todos los scopes
    let tipoPago; // Variable para mantener el tipo de pago

    // Primero obtener información del pago para verificar restricciones
    fetch(`/api/leyma-creditos/creditos/pagos/${id}`)
        .then((response) => response.json())
        .then((pago) => {
            creditoId = pago.credito_id; // Guardar el ID del crédito
            tipoPago = pago.tipo_pago; // Guardar el tipo de pago

            // Si es un pago de cuota individual, verificar si hay pagos de monto restante
            if (tipoPago === "cuota_individual") {
                return fetch(`/api/leyma-creditos/creditos/${creditoId}/pagos`)
                    .then((response) => {
                        console.log("Response status:", response.status);
                        if (!response.ok) {
                            throw new Error(
                                `HTTP error! status: ${response.status}`
                            );
                        }
                        return response.json();
                    })
                    .then((data) => {
                        console.log("Datos recibidos:", data);
                        const pagos = data.data || data || [];
                        console.log("Pagos encontrados:", pagos.length);
                        const pagosMontoRestante = pagos.filter(
                            (p) => p.tipo_pago === "pago_monto_restante"
                        );
                        console.log(
                            "Pagos de monto restante:",
                            pagosMontoRestante.length
                        );

                        if (pagosMontoRestante.length > 0) {
                            // Hay pagos de monto restante, no permitir eliminación
                            Swal.fire({
                                title: "No se puede eliminar",
                                text: "Para eliminar este pago de cuota individual, primero debe eliminar los pagos de monto restante existentes.",
                                icon: "warning",
                                confirmButtonText: "Entendido",
                            });
                            return false; // Indicar que no se debe proceder
                        }

                        // No hay pagos de monto restante, proceder con la eliminación
                        return true; // Indicar que se puede proceder
                    })
                    .catch((error) => {
                        console.error(
                            "Error al verificar pagos de monto restante:",
                            error
                        );
                        // En caso de error, permitir la eliminación
                        return true;
                    });
            }

            // Para otros tipos de pago, proceder normalmente
            return true;
        })
        .then((puedeProceder) => {
            if (puedeProceder === false) {
                return; // No proceder con la eliminación
            }

            // Actualizar el alert antes de mostrar el diálogo de confirmación
            return actualizarMontoRestanteAlert(creditoId).then(() => {
                return Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Esta acción eliminará el pago de forma permanente.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar",
                });
            });
        })
        .then((result) => {
            // Verificar si result existe antes de acceder a sus propiedades
            if (!result || !result.isConfirmed) {
                return; // El usuario canceló o no se pudo proceder
            }

            if (result.isConfirmed) {
                // Mostrar loading
                Swal.fire({
                    title: "Eliminando...",
                    text: "Por favor espera",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    },
                });

                // Ahora eliminar el pago
                return fetch(
                    `/api/leyma-creditos/creditos/pagos/${id}/delete`,
                    {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    }
                )
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            Swal.fire({
                                title: "¡Eliminado!",
                                text:
                                    data.message ||
                                    "El pago ha sido eliminado correctamente",
                                icon: "success",
                                confirmButtonText: "Aceptar",
                            }).then(() => {
                                // Recargar tabla de pagos
                                if (
                                    $.fn.DataTable.isDataTable(
                                        "#pagos_credito_table"
                                    )
                                ) {
                                    $("#pagos_credito_table")
                                        .DataTable()
                                        .ajax.reload();
                                }

                                // Recargar información del crédito
                                if (creditoId) {
                                    verPagosCronograma(creditoId);
                                }

                                // Recargar tabla principal
                                tableCreditos.ajax.reload();

                                // Recargar estadísticas
                                cargarEstadisticas();
                            });
                        } else {
                            Swal.fire(
                                "Error",
                                data.message || "No se pudo eliminar el pago",
                                "error"
                            );
                        }
                    });
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            Swal.fire(
                "Error",
                "Error de conexión al eliminar el pago",
                "error"
            );
        })
        .finally(() => {
            // Restaurar botón y resetear bandera
            const submitBtn = $(
                "#formAgregarPagoCredito button[type='submit']"
            );
            submitBtn
                .html('<i class="fas fa-save me-1"></i>Guardar Pago')
                .prop("disabled", false);
            estadoGlobal.guardandoPago = false;
        });
}

function verPagosCronograma(id) {
    console.log("Ver pagos y cronograma para crédito:", id);

    // Cargar información del crédito
    cargarInformacionCredito(id)
        .then((data) => {
            // Llenar información del crédito en el modal unificado
            $("#info_pagado_total_unificado").text(
                `${formatearMoneda(data.monto_pagado || 0)} / ${formatearMoneda(
                    data.monto_total || 0
                )}`
            );
            $("#info_cantidad_cuotas_unificado").text(
                data.cantidad_cuotas || "—"
            );
            $("#info_tipo_credito_unificado").text(data.tipo_pago || "—");

            // Guardar ID del crédito en el modal para uso posterior
            $("#modalPagosCronograma").data("credito-id", id);

            // Generar cronograma primero
            const cronogramaGenerado = generarCronograma(data);

            // Mostrar modal
            $("#modalPagosCronograma").modal("show");

            // Inicializar tablas después de que el modal esté visible
            setTimeout(() => {
                inicializarTablaPagos(id);
            }, 100);

            // Event listener para inicializar tablas cuando se cambie de tab
            $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
                const target = $(e.target).attr("data-bs-target");
                if (
                    target === "#pagos" &&
                    !$.fn.DataTable.isDataTable("#pagos_credito_table")
                ) {
                    // Inicializar tabla de pagos si no está inicializada
                    inicializarTablaPagos(id);
                }
            });

            // Activar el tab de cronograma por defecto (primera tab)
            // $("#cronograma-tab").tab("show");
        })
        .catch(() => {
            // Error ya manejado en cargarInformacionCredito
        });
}

function generarCronograma(credito) {
    console.log("Datos del crédito:", credito);

    // Validar datos requeridos
    if (!credito.monto_total || !credito.fecha_inicio) {
        console.error("Datos del crédito incompletos:", credito);
        Swal.fire("Error", "Los datos del crédito están incompletos", "error");
        return [];
    }

    const cronograma = [];
    const diaPago = parseInt(credito.dia_pago) || 1;

    // Obtener pagos reales
    const pagosReales = credito.pagos || [];

    // Generar fechas programadas
    let fechaInicio = new Date(credito.fecha_inicio);

    if (credito.tipo_pago === "contado") {
        // Para créditos contado, usar fecha_vencimiento directamente
        let fechaProgramada = new Date(
            credito.fecha_vencimiento || credito.fecha_final
        );

        // Buscar pago real correspondiente por número de cuota (para contado, cuota 1)
        const pagoReal = pagosReales.find((pago) => {
            return (
                pago.cuotas_afectadas &&
                pago.cuotas_afectadas.toString() === "1"
            );
        });

        // Calcular días de atraso
        let diasAtraso = 0;
        if (pagoReal) {
            // Si hay pago, calcular atraso al momento del pago
            const fechaPago = new Date(pagoReal.fecha_pago);
            fechaPago.setHours(0, 0, 0, 0); // Normalizar a medianoche
            const fechaProgramadaNormalizada = new Date(fechaProgramada);
            fechaProgramadaNormalizada.setHours(0, 0, 0, 0); // Normalizar a medianoche

            if (fechaPago > fechaProgramadaNormalizada) {
                const diffTime = fechaPago - fechaProgramadaNormalizada;
                diasAtraso = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            }
        } else {
            // Si no hay pago, calcular atraso hasta hoy
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
            const fechaProgramadaNormalizada = new Date(fechaProgramada);
            fechaProgramadaNormalizada.setHours(0, 0, 0, 0); // Normalizar a medianoche

            if (hoy > fechaProgramadaNormalizada) {
                const diffTime = hoy - fechaProgramadaNormalizada;
                diasAtraso = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            }
        }

        cronograma.push({
            cuota: 1,
            fecha_programada: fechaProgramada.toISOString().split("T")[0],
            monto_programado: credito.monto_total,
            estado: pagoReal
                ? "Pagado"
                : new Date() > fechaProgramada
                ? "Vencido"
                : "Pendiente",
            fecha_real: pagoReal ? pagoReal.fecha_pago : null,
            monto_real: pagoReal ? pagoReal.monto : null,
            dias_atraso: diasAtraso,
            pago_real: pagoReal,
        });
    } else {
        // Para otros tipos de crédito, usar cantidad_cuotas
        if (!credito.cantidad_cuotas) {
            console.error(
                "Cantidad de cuotas no especificada para tipo:",
                credito.tipo_pago
            );
            Swal.fire(
                "Error",
                "La cantidad de cuotas no está especificada",
                "error"
            );
            return;
        }

        const cantidadCuotas = parseInt(credito.cantidad_cuotas);
        const montoPorCuota = credito.monto_total / cantidadCuotas;
        // Saldo arrastrado entre cuotas (positivo = en contra, negativo = a favor)
        let saldoArrastre = 0;

        for (let i = 1; i <= cantidadCuotas; i++) {
            // Calcular fecha programada
            let fechaProgramada = new Date(fechaInicio);

            if (credito.tipo_pago === "diario") {
                fechaProgramada.setDate(fechaInicio.getDate() + (i - 1));
            } else if (credito.tipo_pago === "semanal") {
                fechaProgramada.setDate(fechaInicio.getDate() + (i - 1) * 7);
            } else if (credito.tipo_pago === "quincenal") {
                fechaProgramada.setDate(fechaInicio.getDate() + (i - 1) * 15);
            } else if (credito.tipo_pago === "mensual") {
                fechaProgramada.setMonth(fechaInicio.getMonth() + (i - 1));
                fechaProgramada.setDate(diaPago);
            }

            // Buscar pago real correspondiente por número de cuota
            const pagoReal = pagosReales.find((pago) => {
                return (
                    pago.cuotas_afectadas &&
                    pago.cuotas_afectadas.toString() === i.toString()
                );
            });

            // Monto esperado para esta cuota considerando saldos arrastrados
            // El arrastre reduce el monto, pero nunca puede ser negativo
            const montoEsperadoAjustado = Math.max(
                0,
                montoPorCuota + saldoArrastre
            );
            const ajusteAplicado = saldoArrastre; // guardar ajuste usado en esta cuota (puede ser 0)

            // Calcular días de atraso
            let diasAtraso = 0;
            if (pagoReal) {
                // Si hay pago, calcular atraso al momento del pago
                const fechaPago = new Date(pagoReal.fecha_pago);
                const fechaProgramadaNormalizada = new Date(fechaProgramada);
                fechaProgramadaNormalizada.setHours(0, 0, 0, 0); // Normalizar fecha programada a medianoche

                if (fechaPago > fechaProgramadaNormalizada) {
                    // Cualquier atraso, aunque sea menor a un día, cuenta como 1 día mínimo
                    fechaPago.setHours(0, 0, 0, 0);
                    const diffTime = fechaPago - fechaProgramadaNormalizada;
                    diasAtraso = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                }
            } else {
                // Si no hay pago, calcular atraso hasta hoy
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
                const fechaProgramadaNormalizada = new Date(fechaProgramada);
                fechaProgramadaNormalizada.setHours(0, 0, 0, 0); // Normalizar a medianoche

                if (hoy > fechaProgramadaNormalizada) {
                    const diffTime = hoy - fechaProgramadaNormalizada;
                    diasAtraso = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                }
            }

            // Si hubo pago en esta cuota, calcular saldo a favor/en contra
            let diferenciaPago = null;
            if (pagoReal) {
                const diferencia =
                    (parseFloat(pagoReal.monto) || 0) -
                    (parseFloat(montoEsperadoAjustado) || 0);
                // Para la próxima cuota: si pagó menos, diferencia < 0 -> arrastre positivo (en contra)
                // si pagó más, diferencia > 0 -> arrastre negativo (a favor)
                saldoArrastre = -diferencia;
                diferenciaPago = diferencia;
            } else {
                // Si no hubo pago en esta cuota, el arrastre NO se propaga a la siguiente
                // (según requerimiento: solo se arrastra al pagar una cuota)
                saldoArrastre = 0;
                diferenciaPago = null;
            }

            cronograma.push({
                cuota: i,
                fecha_programada: fechaProgramada.toISOString().split("T")[0],
                monto_programado: montoEsperadoAjustado,
                estado: pagoReal
                    ? "Pagado"
                    : new Date() > fechaProgramada
                    ? "Vencido"
                    : "Pendiente",
                fecha_real: pagoReal ? pagoReal.fecha_pago : null,
                monto_real: pagoReal ? pagoReal.monto : null,
                dias_atraso: diasAtraso,
                pago_real: pagoReal,
                ajuste: ajusteAplicado,
                diferencia_pago: diferenciaPago,
            });
        }
    }

    console.log("Cronograma generado:", cronograma);

    // Llenar tabla
    llenarTablaCronograma(cronograma);

    // Actualizar resumen con información del crédito
    actualizarResumenCronograma(cronograma, credito);

    // Devolver el cronograma generado para uso externo
    return cronograma;
}

function llenarTablaCronograma(cronograma) {
    const tbody = $("#cronograma_table tbody");
    if (!tbody.length) {
        console.error("Elemento #cronograma_table tbody no encontrado");
        return;
    }
    tbody.empty();

    cronograma.forEach((cuota) => {
        const estadoClass =
            cuota.estado === "Pagado"
                ? "success"
                : cuota.estado === "Vencido"
                ? "danger"
                : "warning";

        // Checkbox solo para cuotas no pagadas
        let checkboxCell = "";
        if (cuota.estado === "Pendiente" || cuota.estado === "Vencido") {
            checkboxCell = `
                <div class="form-check">
                    <input class="form-check-input row-checkbox cuota-checkbox" type="checkbox" value="${cuota.cuota}" data-monto="${cuota.monto_programado}">
                </div>
            `;
        } else {
            checkboxCell = '<span class="text-muted">—</span>';
        }

        // Botón de acción solo para pagos pendientes o vencidos
        let botonAccion = "";
        if (cuota.estado === "Pendiente" || cuota.estado === "Vencido") {
            botonAccion = `
                <button class="btn btn-success btn-sm btn_pagar_cuota" 
                        data-cuota="${cuota.cuota}" 
                        data-fecha="${cuota.fecha_programada}"
                        data-monto="${cuota.monto_programado}"
                        title="Pagar cuota">
                    <i class="fas fa-credit-card"></i> Pagar
                </button>
            `;
        } else {
            botonAccion = '<span class="text-muted">—</span>';
        }

        const chipAjuste = (() => {
            const aj = Number(cuota.ajuste || 0);
            if (!aj) return "";
            const tipo = aj > 0 ? "En contra" : "A favor";
            const color = aj > 0 ? "danger" : "success";
            const titulo =
                aj > 0
                    ? "Saldo en contra aplicado desde cuota anterior"
                    : "Saldo a favor aplicado desde cuota anterior";
            return `<span class="badge bg-${color}" title="${titulo}">${tipo} ${formatearMoneda(
                Math.abs(aj)
            )}</span>`;
        })();

        const chipDiferencia = (() => {
            const dif = cuota.diferencia_pago;
            if (dif === null || dif === undefined) return "";
            if (Number.isNaN(Number(dif))) return "";
            const d = Number(dif);
            if (d === 0) return "";
            const tipo = d < 0 ? "En contra" : "A favor";
            const color = d < 0 ? "danger" : "success";
            const titulo =
                d < 0
                    ? "Pago menor al esperado: se arrastra a la siguiente cuota"
                    : "Pago mayor al esperado: se descuenta de la siguiente cuota";
            return `<span class="badge bg-${color}" title="${titulo}">${tipo} ${formatearMoneda(
                Math.abs(d)
            )}</span>`;
        })();

        const fila = `
            <tr>
                <td>${checkboxCell}</td>
                <td><strong>${cuota.cuota}</strong></td>
                <td>${new Date(cuota.fecha_programada).toLocaleDateString(
                    "es-AR"
                )}</td>
                <td>${formatearMoneda(cuota.monto_programado)}</td>
                <td class="d-flex gap-1 align-items-center flex-wrap">${chipAjuste} ${chipDiferencia}</td>
                <td>
                    <span class="badge bg-${estadoClass}">${cuota.estado}</span>
                </td>
                <td>${
                    cuota.fecha_real
                        ? new Date(cuota.fecha_real).toLocaleDateString("es-AR")
                        : "—"
                }</td>
                <td>${
                    cuota.monto_real ? formatearMoneda(cuota.monto_real) : "—"
                }</td>
                <td>
                    ${
                        cuota.dias_atraso > 0
                            ? cuota.estado === "Pagado"
                                ? `<span class="badge bg-warning">${cuota.dias_atraso} días</span>`
                                : `<span class="badge bg-danger">${cuota.dias_atraso} días</span>`
                            : "—"
                    }
                </td>
                <td>${botonAccion}</td>
            </tr>
        `;
        tbody.append(fila);
    });

    // Actualizar estado del botón de pagar múltiples
    actualizarEstadoBotonPagarMultiple();
}

function actualizarEstadoBotonPagarMultiple() {
    const checkedCount = $(
        "#cronograma_table tbody .cuota-checkbox:checked"
    ).length;
    const $btnPagarMultiple = $("#btn_pagar_cuotas_seleccionadas");
    const $countSpan = $("#cuotas_seleccionadas_count");
    const $btnHeader = $("#btn_pagar_seleccion_header");
    const $headerCountSpan = $("#header_cuotas_count");

    // El botón del header siempre es visible
    $btnHeader.show();
    $headerCountSpan.text(checkedCount);

    if (checkedCount > 0) {
        $btnPagarMultiple.show();
        $countSpan.text(checkedCount);
    } else {
        $btnPagarMultiple.hide();
    }
}

function pagarCuotasSeleccionadas() {
    const cuotasSeleccionadas = [];
    $("#cronograma_table tbody .cuota-checkbox:checked").each(function () {
        const $checkbox = $(this);
        const cuota = $checkbox.val();
        const monto = parseFloat($checkbox.data("monto"));

        cuotasSeleccionadas.push({
            cuota: cuota,
            monto: monto,
        });
    });

    if (cuotasSeleccionadas.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Atención",
            text: "Por favor selecciona al menos una cuota antes de hacer clic en 'Pagar'.",
        });
        return;
    }

    // Calcular monto total
    const montoTotal = cuotasSeleccionadas.reduce(
        (sum, item) => sum + item.monto,
        0
    );

    Swal.fire({
        title: "¿Pagar cuotas seleccionadas?",
        text: `Se pagarán ${
            cuotasSeleccionadas.length
        } cuotas por un total de ${formatearMoneda(montoTotal)}`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, pagar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear pagos para cada cuota seleccionada
            procesarPagosMultiples(cuotasSeleccionadas);
        }
    });
}

function procesarPagosMultiples(cuotasSeleccionadas) {
    const creditoId = $("#modalPagosCronograma").data("credito-id");

    if (!creditoId) {
        Swal.fire("Error", "No se ha seleccionado un crédito", "error");
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: "Procesando pagos...",
        text: "Por favor espera",
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        },
    });

    // Procesar cada pago secuencialmente
    procesarSiguientePago(creditoId, cuotasSeleccionadas, 0)
        .then(() => {
            Swal.fire({
                title: "¡Éxito!",
                text: "Todos los pagos se procesaron correctamente",
                icon: "success",
            }).then(() => {
                // Recargar información del crédito
                if (creditoId) {
                    verPagosCronograma(creditoId);
                }
            });
        })
        .catch((error) => {
            console.error("Error procesando pagos:", error);
            Swal.fire({
                title: "Error",
                text: "Hubo un error procesando algunos pagos",
                icon: "error",
            });
        });
}

function procesarSiguientePago(creditoId, cuotasSeleccionadas, index) {
    if (index >= cuotasSeleccionadas.length) {
        return Promise.resolve();
    }

    const cuota = cuotasSeleccionadas[index];

    return fetch("/api/leyma-creditos/registrar-pago", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({
            credito_id: creditoId,
            monto: cuota.monto,
            fecha_pago: new Date().toISOString().split("T")[0],
            metodo_pago: "efectivo",
            tipo_pago: "cuota_individual",
            cuotas_afectadas: cuota.cuota.toString(),
            observaciones: `Pago de cuota ${cuota.cuota} (pago múltiple)`,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                throw new Error(data.message || "Error al procesar pago");
            }
            return procesarSiguientePago(
                creditoId,
                cuotasSeleccionadas,
                index + 1
            );
        });
}

function actualizarResumenCronograma(cronograma, credito = null) {
    console.log("Actualizando resumen con cronograma:", cronograma);

    const pagosRealizados = cronograma.filter(
        (c) => c.estado === "Pagado"
    ).length;
    const pagosPendientes = cronograma.filter(
        (c) => c.estado === "Pendiente"
    ).length;
    const pagosVencidos = cronograma.filter(
        (c) => c.estado === "Vencido"
    ).length;

    // Calcular progreso evitando división por cero
    const progreso =
        cronograma.length > 0
            ? Math.round((pagosRealizados / cronograma.length) * 100)
            : 0;

    $("#resumen_pagos_realizados").text(pagosRealizados);
    $("#resumen_pagos_pendientes").text(pagosPendientes + pagosVencidos);
    $("#resumen_progreso").text(`${progreso}%`);

    // Si tenemos información del crédito, calcular monto restante
    if (credito) {
        calcularYMostrarMontoRestante(cronograma, credito);
    }
}

// Función helper común para crear/actualizar el alert de monto restante
function crearActualizarAlertMontoRestante(
    credito,
    montoRestante,
    montoTotal,
    sumaPagosReales
) {
    // Buscar el elemento del alert en el modal
    const modalContent = $("#modalPagosCronograma .modal-body");
    const montoRestanteElement = modalContent.find("#monto_restante_alert");

    if (montoRestante > 0.01) {
        if (montoRestanteElement.length === 0) {
            // Crear elemento si no existe
            modalContent.find("#monto_restante_alert_container").after(`
                <div class="alert alert-warning mt-3" id="monto_restante_alert">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2">💰 Monto Restante</h6>
                            <p class="mb-2">
                                El crédito tiene pagos asociados, pero hay un monto restante de
                                <strong>${formatearMoneda(
                                    montoRestante
                                )}</strong> por cobrar.
                            </p>
                            <small class="text-muted">
                                Total esperado: ${formatearMoneda(montoTotal)} |
                                Total pagado: ${formatearMoneda(
                                    sumaPagosReales
                                )}
                            </small>
                        </div>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="pagarMontoRestante(${
                                credito.id
                            }, ${montoRestante})">
                                <i class="fas fa-credit-card me-1"></i>Pagar Restante
                            </button>
                        </div>
                    </div>
                </div>
            `);
        } else {
            // Actualizar elemento existente
            montoRestanteElement
                .find("strong")
                .text(formatearMoneda(montoRestante));
            montoRestanteElement.find(".text-muted").html(`
                Total esperado: ${formatearMoneda(montoTotal)} |
                Total pagado: ${formatearMoneda(sumaPagosReales)}
            `);
            montoRestanteElement.find("p").html(`
                El crédito tiene pagos asociados, pero hay un monto restante de
                <strong>${formatearMoneda(montoRestante)}</strong> por cobrar.
            `);
            // Actualizar botón
            const btnPago = montoRestanteElement.find(".btn-success");
            btnPago.attr(
                "onclick",
                `pagarMontoRestante(${credito.id}, ${montoRestante})`
            );
        }
    } else {
        // Ocultar alert si no hay monto restante
        montoRestanteElement.remove();
    }
}

function calcularYMostrarMontoRestante(cronograma, credito) {
    // Usar la lógica correcta: verificar si TODAS las cuotas tienen pagos asociados
    const tienePagosAsociados = cronograma.every(
        (c) => c.pago_real !== null && c.pago_real !== undefined
    );

    if (tienePagosAsociados) {
        // Calcular suma de TODOS los pagos del crédito (usar monto_pagado del crédito)
        const sumaPagosReales = parseFloat(credito.monto_pagado) || 0;
        const montoTotal = parseFloat(credito.monto_total) || 0;
        const montoRestante = montoTotal - sumaPagosReales;

        // Usar la función helper común
        crearActualizarAlertMontoRestante(
            credito,
            montoRestante,
            montoTotal,
            sumaPagosReales
        );
    } else {
        // Ocultar alert si no todas las cuotas tienen pagos
        $("#modalPagosCronograma #monto_restante_alert").remove();
    }
}

// Funciones para el modal de pago de cuotas
function abrirModalPagoCuota(cuota, fecha, monto) {
    console.log("Abriendo modal de pago de cuota:", { cuota, fecha, monto });

    // Obtener el ID del crédito del modal unificado
    const creditoId = $("#modalPagosCronograma").data("credito-id");

    if (!creditoId) {
        Swal.fire("Error", "No se pudo identificar el crédito", "error");
        return;
    }

    // Llenar información de la cuota
    $("#info_numero_cuota").text(cuota);
    $("#info_fecha_programada").text(
        new Date(fecha).toLocaleDateString("es-AR")
    );
    $("#info_monto_programado").text(formatearMoneda(parseFloat(monto)));

    // Establecer fecha actual como predeterminada
    const hoy = new Date().toISOString().split("T")[0];
    $("#fecha_pago_cuota").val(hoy);

    // Establecer monto programado
    $("#monto_pago_cuota").val(parseFloat(monto).toFixed(2));

    // Guardar datos en el formulario
    $("#formPagoCuota").data("credito-id", creditoId);
    $("#formPagoCuota").data("monto-programado", parseFloat(monto));
    $("#formPagoCuota").data("cuota-numero", cuota);
    $("#formPagoCuota").data("credito-id-modal", creditoId);

    // Limpiar checkbox
    $("#pagar_monto_exacto").prop("checked", false);

    // Actualizar resumen
    actualizarResumenPagoCuota();

    // Mostrar modal
    $("#modalPagoCuota").modal("show");
}

function actualizarResumenPagoCuota() {
    const montoProgramado =
        parseFloat($("#formPagoCuota").data("monto-programado")) || 0;
    const montoAPagar = parseFloat($("#monto_pago_cuota").val()) || 0;
    const diferencia = montoAPagar - montoProgramado;

    $("#resumen_monto_programado").text(formatearMoneda(montoProgramado));
    $("#resumen_monto_a_pagar").text(formatearMoneda(montoAPagar));

    // Formatear diferencia con color según el caso
    const diferenciaElement = $("#resumen_diferencia");
    diferenciaElement.text(formatearMoneda(diferencia));

    if (diferencia > 0) {
        diferenciaElement
            .removeClass("text-success text-muted")
            .addClass("text-warning");
    } else if (diferencia < 0) {
        diferenciaElement
            .removeClass("text-warning text-muted")
            .addClass("text-info");
    } else {
        diferenciaElement
            .removeClass("text-warning text-info")
            .addClass("text-success");
    }
}

// Función para procesar saldo a favor aplicándolo a cuotas siguientes
async function procesarSaldoAFavor(
    creditoId,
    cuotaActual,
    saldoAFavor,
    fechaPago
) {
    console.log("procesarSaldoAFavor called with:", {
        creditoId,
        cuotaActual,
        saldoAFavor,
        fechaPago,
    });

    if (saldoAFavor <= 0.01) {
        console.log("No hay saldo a favor que procesar");
        return []; // No hay saldo a favor que procesar
    }

    try {
        console.log("Obteniendo información del crédito...");
        // Obtener información actualizada del crédito
        const credito = await cargarInformacionCredito(creditoId);
        console.log("Crédito obtenido:", credito);

        console.log("Generando cronograma...");
        // Generar cronograma actualizado
        const cronograma = generarCronograma(credito);
        console.log("Cronograma generado:", cronograma);

        // Debug: mostrar montos programados de cada cuota
        cronograma.forEach((cuota) => {
            console.log(
                `Cuota ${cuota.cuota}: monto_programado=${cuota.monto_programado}, ajuste=${cuota.ajuste}, diferencia_pago=${cuota.diferencia_pago}`
            );
        });

        // Encontrar cuotas siguientes no pagadas
        const cuotasSiguientes = cronograma
            .filter(
                (cuota) =>
                    parseInt(cuota.cuota) > cuotaActual &&
                    cuota.estado !== "Pagado"
            )
            .sort((a, b) => a.cuota - b.cuota);

        console.log("Cuotas siguientes no pagadas:", cuotasSiguientes);

        const cuotasPagadas = [];
        let saldoRestante = saldoAFavor;

        // Aplicar saldo a favor a las cuotas siguientes
        // Solo paga cuotas si el saldo restante cubre el monto completo
        console.log(
            "Procesando cuotas con saldo restante:",
            formatearMoneda(saldoRestante)
        );
        for (const cuota of cuotasSiguientes) {
            console.log(
                `Evaluando cuota ${cuota.cuota} - Estado: ${
                    cuota.estado
                } - Monto: ${formatearMoneda(cuota.monto_programado)}`
            );

            if (saldoRestante <= 0.01) {
                console.log("Saldo restante agotado, terminando proceso");
                break;
            }

            const montoNecesario = cuota.monto_programado;
            console.log(
                `Comparando: ${formatearMoneda(
                    saldoRestante
                )} >= ${formatearMoneda(montoNecesario)} ?`
            );

            if (saldoRestante >= montoNecesario) {
                // ✅ Saldo suficiente: pagar cuota completa
                console.log(
                    `✅ Pagando cuota ${cuota.cuota} con ${formatearMoneda(
                        montoNecesario
                    )}`
                );
                await registrarPagoCuotaAutomatico(
                    creditoId,
                    cuota.cuota,
                    montoNecesario,
                    fechaPago,
                    `Pago automático de cuota ${cuota.cuota} con saldo a favor de cuota ${cuotaActual}`
                );

                cuotasPagadas.push(cuota.cuota);
                saldoRestante -= montoNecesario;
                console.log(
                    `Saldo restante después del pago: ${formatearMoneda(
                        saldoRestante
                    )}`
                );
            } else {
                // ❌ Saldo insuficiente: NO paga esta cuota
                // Se omite para mantener integridad (no pagos parciales)
                console.log(
                    `❌ Saldo insuficiente para cuota ${
                        cuota.cuota
                    }: ${formatearMoneda(saldoRestante)} < ${formatearMoneda(
                        montoNecesario
                    )}`
                );
            }
        }

        console.log("Proceso completado. Cuotas pagadas:", cuotasPagadas);
        return cuotasPagadas;
    } catch (error) {
        console.error("Error al procesar saldo a favor:", error);
        return [];
    }
}

// Función para calcular cuántas cuotas se pueden pagar completamente con un monto dado
async function calcularCuotasQueSePuedenPagarCompletamente(
    creditoId,
    cuotaActual,
    montoDisponible
) {
    try {
        const credito = await cargarInformacionCredito(creditoId);
        const cronograma = generarCronograma(credito);

        // Lógica correcta: pagar cuota actual primero, luego adicionales con el restante
        const cuotasPagar = [];

        // 1. Calcular cuánto pagar de la cuota actual
        const cuotaActualData = cronograma.find(
            (c) => parseInt(c.cuota) === cuotaActual
        );

        let montoRestante = montoDisponible;
        let montoParaActual = 0;

        if (
            cuotaActualData &&
            cuotaActualData.estado !== "Pagado" &&
            cuotaActualData.monto_programado > 0
        ) {
            montoParaActual = Math.min(
                montoDisponible,
                cuotaActualData.monto_programado
            );
            montoRestante = montoDisponible - montoParaActual;

            cuotasPagar.push({
                cuota: cuotaActual,
                monto: montoParaActual,
            });

            console.log(
                `✅ Cuota actual ${cuotaActual} agregada con ${formatearMoneda(
                    montoParaActual
                )}. Restante: ${formatearMoneda(montoRestante)}`
            );
        }

        // 2. Usar el restante para cuotas adicionales completas
        console.log(
            `Buscando cuotas adicionales desde ${cuotaActual + 1} hasta ${
                credito.cantidad_cuotas
            } con restante: ${formatearMoneda(montoRestante)}`
        );

        for (let i = cuotaActual + 1; i <= credito.cantidad_cuotas; i++) {
            const cuota = cronograma.find((c) => parseInt(c.cuota) === i);
            console.log(`Buscando cuota ${i}:`, cuota);

            if (!cuota) {
                console.log(`Cuota ${i} no encontrada en cronograma`);
                continue;
            }

            if (cuota.estado === "Pagado") {
                console.log(`Cuota ${i} ya está pagada, omitiendo`);
                continue;
            }

            console.log(
                `Evaluando cuota ${i} con monto ${formatearMoneda(
                    cuota.monto_programado
                )} - Saldo restante: ${formatearMoneda(montoRestante)}`
            );

            // Solo considerar cuotas con monto programado mayor a 0
            if (cuota.monto_programado <= 0) {
                console.log(
                    `❌ Cuota ${i} tiene monto programado <= 0 (${cuota.monto_programado}), omitiendo`
                );
                continue;
            }

            if (montoRestante >= cuota.monto_programado) {
                cuotasPagar.push({
                    cuota: cuota.cuota,
                    monto: cuota.monto_programado,
                });
                montoRestante -= cuota.monto_programado;
                console.log(
                    `✅ Cuota adicional ${i} agregada. Saldo restante: ${formatearMoneda(
                        montoRestante
                    )}`
                );
            } else {
                // No hay suficiente para esta cuota, detener la búsqueda
                console.log(
                    `❌ No hay suficiente saldo para cuota ${i} (${formatearMoneda(
                        montoRestante
                    )} < ${formatearMoneda(cuota.monto_programado)})`
                );
                break;
            }
        }

        // Si queda dinero que no alcanza para una cuota completa siguiente,
        // agregarlo a la última cuota que se está pagando
        if (montoRestante > 0 && cuotasPagar.length > 0) {
            const ultimaCuota = cuotasPagar[cuotasPagar.length - 1];
            console.log(
                `💰 Agregando ${formatearMoneda(
                    montoRestante
                )} restantes a la última cuota ${ultimaCuota.cuota}`
            );
            ultimaCuota.monto += montoRestante;
            montoRestante = 0;
        }

        // Debug: mostrar información del crédito y cronograma
        console.log("Crédito:", credito);
        console.log("Cantidad de cuotas:", credito.cantidad_cuotas);
        console.log("Monto total:", credito.monto_total);
        console.log("Monto disponible:", montoDisponible);
        console.log("Cuota actual:", cuotaActual);

        // Debug: mostrar todas las cuotas del cronograma
        cronograma.forEach((c) => {
            console.log(
                `Cuota ${c.cuota}: estado=${c.estado}, monto_programado=${c.monto_programado}, ajuste=${c.ajuste}`
            );
        });

        // Si no se pudo pagar ninguna cuota adicional, entonces solo se puede pagar la cuota actual
        if (cuotasPagar.length === 0) {
            const cuotaActualData = cronograma.find(
                (c) => parseInt(c.cuota) === cuotaActual
            );
            const montoParaCuotaActual = Math.min(
                montoDisponible,
                cuotaActualData?.monto_programado || montoDisponible
            );
            console.log(
                "Solo se puede pagar la cuota actual con monto:",
                montoParaCuotaActual
            );
            return [{ cuota: cuotaActual, monto: montoParaCuotaActual }];
        }

        console.log("Cuotas finales a pagar:", cuotasPagar);
        return cuotasPagar;
    } catch (error) {
        console.error("Error calculando cuotas pagables:", error);
        return [{ cuota: cuotaActual, monto: montoDisponible }];
    }
}

// Función para procesar pagos múltiples de manera optimizada
async function procesarPagosMultiplesOptimizados(
    cuotasPagar,
    montoTotal,
    fechaPago
) {
    console.log("Procesando pagos múltiples optimizados:", cuotasPagar);

    const creditoId = $("#formPagoCuota").data("credito-id");
    const resultados = [];

    try {
        // Procesar cada pago secuencialmente
        for (const cuotaData of cuotasPagar) {
            console.log(
                `Procesando pago para cuota ${
                    cuotaData.cuota
                } con ${formatearMoneda(cuotaData.monto)}`
            );

            const formData = new FormData();
            formData.append("credito_id", creditoId.toString());
            formData.append("monto", cuotaData.monto.toString());
            formData.append("fecha_pago", fechaPago);
            formData.append("metodo_pago", "efectivo");
            formData.append("cuotas_afectadas", cuotaData.cuota.toString());
            formData.append("tipo_pago", "cuota_individual");
            formData.append(
                "observaciones",
                `Pago optimizado de cuota ${cuotaData.cuota} (${
                    cuotasPagar.length
                } cuotas con ${formatearMoneda(montoTotal)})`
            );

            const response = await fetch("/api/leyma-creditos/registrar-pago", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(
                    `Error en cuota ${cuotaData.cuota}: ${errorText}`
                );
            }

            const data = await response.json();
            if (data.success) {
                resultados.push(cuotaData.cuota);
                console.log(`✅ Cuota ${cuotaData.cuota} pagada exitosamente`);
            } else {
                throw new Error(
                    `Error registrando cuota ${cuotaData.cuota}: ${data.message}`
                );
            }
        }

        // Mostrar resultado
        const cuotaPrincipal = resultados[0]; // La primera cuota es la que se pagó manualmente
        const cuotasAdicionales = resultados.slice(1); // Las demás son adicionales

        let mensaje = `Se pagaron ${
            resultados.length
        } cuota(s): ${resultados.join(", ")} con ${formatearMoneda(
            montoTotal
        )}`;
        if (cuotasAdicionales.length > 0) {
            mensaje += `\n\nCuota principal: ${cuotaPrincipal}\nCuotas adicionales con saldo a favor: ${cuotasAdicionales.join(
                ", "
            )}`;
        }

        Swal.fire({
            title: "¡Pagos registrados!",
            text: mensaje,
            icon: "success",
            confirmButtonText: "Aceptar",
        }).then(() => {
            // Cerrar modal
            $("#modalPagoCuota").modal("hide");

            // Recargar información
            if (creditoId) {
                verPagosCronograma(creditoId);
            }

            // Recargar tabla principal y estadísticas
            tableCreditos.ajax.reload();
            cargarEstadisticas();
        });

        return resultados;
    } catch (error) {
        console.error("Error procesando pagos múltiples:", error);
        Swal.fire("Error", `Error procesando pagos: ${error.message}`, "error");
        return [];
    }
}

// Función auxiliar para registrar pagos automáticos de cuotas
async function registrarPagoCuotaAutomatico(
    creditoId,
    cuotaNumero,
    monto,
    fechaPago,
    observaciones
) {
    console.log("registrarPagoCuotaAutomatico called with:", {
        creditoId,
        cuotaNumero,
        monto,
        fechaPago,
        observaciones,
    });

    // Validar que el monto sea mayor a 0
    if (monto <= 0) {
        console.error(`❌ No se puede registrar pago con monto <= 0: ${monto}`);
        throw new Error(`Monto inválido para cuota ${cuotaNumero}: ${monto}`);
    }

    const formData = new FormData();

    formData.append("credito_id", creditoId.toString());
    formData.append("monto", monto.toString());
    formData.append("fecha_pago", fechaPago);
    formData.append("metodo_pago", "efectivo");
    formData.append("cuotas_afectadas", cuotaNumero.toString());
    formData.append("tipo_pago", "cuota_individual");
    formData.append("observaciones", observaciones);

    console.log("Datos a enviar:", Object.fromEntries(formData));

    try {
        const response = await fetch("/api/leyma-creditos/registrar-pago", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        console.log("Response status:", response.status);
        console.log("Response headers:", Object.fromEntries(response.headers));

        if (!response.ok) {
            const errorText = await response.text();
            console.error("Error response:", errorText);
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }

        const data = await response.json();
        console.log("Pago automático registrado:", data);

        if (!data.success) {
            throw new Error(
                data.message || "Error al registrar pago automático"
            );
        }

        return data;
    } catch (error) {
        console.error("Error en registrarPagoCuotaAutomatico:", error);
        throw error;
    }
}

async function guardarPagoCuota() {
    const formData = new FormData($("#formPagoCuota")[0]);
    const creditoId = $("#formPagoCuota").data("credito-id");
    const cuotaNumero = parseInt($("#formPagoCuota").data("cuota-numero"));
    const montoProgramado =
        parseFloat($("#formPagoCuota").data("monto-programado")) || 0;

    if (!creditoId) {
        Swal.fire("Error", "No se ha seleccionado un crédito", "error");
        return;
    }

    // Validar campos requeridos
    const montoAPagar = parseFloat($("#monto_pago_cuota").val()) || 0;
    const fechaPago = $("#fecha_pago_cuota").val();

    if (montoAPagar <= 0) {
        Swal.fire("Error", "El monto debe ser mayor a 0", "error");
        return;
    }

    if (!fechaPago) {
        Swal.fire("Error", "Debe seleccionar una fecha de pago", "error");
        return;
    }

    // Calcular cuántas cuotas se pueden pagar completamente
    const cuotasQueSePuedenPagar =
        await calcularCuotasQueSePuedenPagarCompletamente(
            creditoId,
            cuotaNumero,
            montoAPagar
        );

    console.log(
        "Cuotas que se pueden pagar completamente:",
        cuotasQueSePuedenPagar
    );

    // Si se puede pagar más de una cuota, procesar pagos múltiples
    if (cuotasQueSePuedenPagar.length > 1) {
        console.log("Procesando pagos múltiples para optimizar...");
        await procesarPagosMultiplesOptimizados(
            cuotasQueSePuedenPagar,
            montoAPagar,
            fechaPago
        );
        return; // Salir de la función después de procesar pagos múltiples
    }

    // Si solo se puede pagar la cuota actual, proceder normalmente
    const saldoAFavor = montoAPagar - montoProgramado;

    // Agregar datos adicionales
    formData.append("credito_id", creditoId.toString());
    formData.append("cuotas_afectadas", cuotaNumero.toString());
    formData.append("tipo_pago", "cuota_individual");
    formData.append(
        "observaciones",
        `Pago de cuota ${cuotaNumero} - ${
            $("#observaciones_pago_cuota").val() || ""
        }`
    );

    // Mostrar loading
    const submitBtn = $("#formPagoCuota button[type='submit']");
    const originalText = submitBtn.html();
    submitBtn
        .html('<i class="fas fa-spinner fa-spin me-1"></i>Registrando...')
        .prop("disabled", true);

    console.log("Procesando pago único para cuota", cuotaNumero);
    console.log("Datos del pago manual:", Object.fromEntries(formData));
    console.log("Monto a pagar:", montoAPagar);
    console.log("Monto programado:", montoProgramado);
    console.log("Saldo a favor calculado:", saldoAFavor);

    try {
        // Registrar el pago de la cuota actual
        const response = await fetch("/api/leyma-creditos/registrar-pago", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        console.log("Response status pago manual:", response.status);
        console.log(
            "Response headers pago manual:",
            Object.fromEntries(response.headers)
        );

        if (!response.ok) {
            const errorText = await response.text();
            console.error("Error response pago manual:", errorText);
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }

        const data = await response.json();
        console.log("Respuesta pago manual:", data);

        if (data.success) {
            console.log(
                "Pago principal registrado exitosamente. Procesando saldo a favor..."
            );
            console.log("Saldo a favor calculado:", saldoAFavor);

            // Procesar saldo a favor si existe
            const cuotasAdicionalesPagadas = await procesarSaldoAFavor(
                creditoId,
                cuotaNumero,
                saldoAFavor,
                fechaPago
            );

            console.log(
                "Cuotas adicionales pagadas:",
                cuotasAdicionalesPagadas
            );

            // Preparar mensaje de éxito
            let mensajeExito =
                data.message ||
                "El pago de la cuota ha sido registrado correctamente";
            if (cuotasAdicionalesPagadas.length > 0) {
                mensajeExito += `\n\nAdemás, se pagaron ${
                    cuotasAdicionalesPagadas.length
                } cuota(s) adicional(es) con el saldo a favor: ${cuotasAdicionalesPagadas.join(
                    ", "
                )}`;
            }

            Swal.fire({
                title: "¡Pago registrado!",
                text: mensajeExito,
                icon: "success",
                confirmButtonText: "Aceptar",
            }).then(() => {
                // Cerrar modal
                $("#modalPagoCuota").modal("hide");

                // Recargar el modal unificado
                if (creditoId) {
                    verPagosCronograma(creditoId);
                }

                // Recargar tabla principal y estadísticas
                tableCreditos.ajax.reload();
                cargarEstadisticas();

                // Si el modal unificado está abierto, refrescarlo
                const $modalUnificado = $("#modalPagosCronograma");
                const unificadoAbierto =
                    $modalUnificado.is(":visible") ||
                    $modalUnificado.hasClass("show");
                const unificadoCreditoId = $modalUnificado.data("credito-id");

                if (unificadoAbierto && unificadoCreditoId) {
                    // Recargar información del crédito sin reinicializar completamente
                    fetch(`/api/leyma-creditos/creditos/${unificadoCreditoId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            // Actualizar información del crédito
                            $("#info_pagado_total_unificado").text(
                                `${formatearMoneda(
                                    data.monto_pagado || 0
                                )} / ${formatearMoneda(data.monto_total || 0)}`
                            );
                            $("#info_cantidad_cuotas_unificado").text(
                                data.cantidad_cuotas || "—"
                            );
                            $("#info_tipo_credito_unificado").text(
                                data.tipo_pago || "—"
                            );

                            // Resetear estado y recargar tablas
                            resetearEstadoTablas();
                            setTimeout(() => {
                                inicializarTablaPagos(unificadoCreditoId);
                                const cronogramaGenerado =
                                    generarCronograma(data);
                                actualizarResumenCronograma(
                                    cronogramaGenerado,
                                    data
                                );
                            }, 100);
                        })
                        .catch((error) => {
                            console.error(
                                "Error al recargar información del crédito:",
                                error
                            );
                        });
                }
            });
        } else {
            Swal.fire(
                "Error",
                data.message || "Error al registrar el pago",
                "error"
            );
        }
    } catch (error) {
        console.error("Error:", error);
        Swal.fire("Error", "Error de conexión al registrar el pago", "error");
    } finally {
        // Restaurar botón
        submitBtn.html(originalText).prop("disabled", false);
    }
}

// Exponer funciones globales necesarias para HTML inline
$(document).ready(function () {
    window.pagarMontoRestante = pagarMontoRestante;
    window.calcularMontoFinal = calcularMontoFinal;
});
