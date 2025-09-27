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
// ✅ Y su CSS
import "bootstrap-icons/font/bootstrap-icons.css";

import SlimSelect from "slim-select";
import currency from "currency.js";

$("#select_clientes_factura").change(function (e) {
    const clienteId = $(this).val();

    if (!clienteId) return;

    fetch(`/clientes/${clienteId}`)
        .then((res) => {
            if (!res.ok) throw new Error("Error al obtener cliente");
            return res.json();
        })
        .then((cliente) => {
            // Recorremos todos los campos con class .campo_factura
            $(".campo_factura").each(function () {
                const name = $(this).attr("name");

                if (cliente.hasOwnProperty(name)) {
                    $(this).val(cliente[name]).trigger("change");
                }
            });
        })
        .catch((err) => {
            console.error(err);

            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del cliente.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        });
});
$(document).on("input change", ".campo_factura", function () {
    const name = $(this).attr("name");
    let value = $(this).val();

    // Si es textarea, reemplazar saltos de línea por <br>
    if ($(this).is("textarea")) {
        value = value.replace(/\n/g, "<br>");
    }

    // Buscar dentro de #factura_html todos los elementos con el mismo name
    $('#factura_html [name="' + name + '"]').each(function () {
        $(this).html(value); // usar .html() para que <br> sea interpretado
    });
});

document
    .getElementById("cantidad_articulo")
    .addEventListener("input", function () {
        if (this.value < 1) {
            this.setCustomValidity("Debe ser un número mayor que 0");
        } else {
            this.setCustomValidity("");
        }
    });
$("#select_articulos_factura").change(function (e) {
    const clienteId = $(this).val();

    if (!clienteId) return;

    fetch(`/articulos/${clienteId}`)
        .then((res) => {
            if (!res.ok) throw new Error("Error al obtener artículo");
            return res.json();
        })
        .then((cliente) => {
            // Recorremos todos los campos con class .campo_factura
            $("#nombre_articulo").val(cliente.nombre).trigger("change");
            $("#descuento_articulo").val(cliente.descuento).trigger("change");
            $("#precio_unitario_articulo")
                .val(cliente.precio)
                .trigger("change");
            $("#descripcion_articulo")
                .val(cliente.descripcion)
                .trigger("change");
            $("#btn_add_articulo").data("id", clienteId);
            $("#btn_add_articulo").data("stock", cliente.stock);
        })
        .catch((err) => {
            console.error(err);
            Swal.fire({
                title: "Error",
                text: "Error al cargar los datos del cliente.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
        });
});

const tabla = $("#tabla_articulos_factura").DataTable({
    columns: [
        {
            data: null,
            render: function (data, type, row) {
                return `
                    <h5 class="mt-0 mb-1 fs-14">${row.nombre}</h5>
                    <p class="mb-0 text-muted">${row.descripcion ?? ""}</p>
                `;
            },
        },
        { data: "cantidad" },
        {
            data: "precio", // precio unitario SIN impuesto ni descuento
            render: function (data) {
                return currency(data || 0, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format();
            },
            className: "text-end",
        },

        // NUEVA COLUMNA: descuento %
        {
            data: "descuento",
            render: (data) => `${(parseFloat(data) || 0).toFixed(2)}%`,
            className: "text-end",
            // si no querés mostrarla, descomenta:
            // visible: false,
        },

        {
            data: "impuesto",
            render: (data) => `${(parseFloat(data) || 0).toFixed(2)}%`,
            className: "text-end",
        },
        {
            data: null,
            render: function (data, type, row) {
                const precio = parseFloat(row.precio) || 0; // base sin imp ni desc
                const cantidad = parseFloat(row.cantidad) || 0;
                const impuesto = parseFloat(row.impuesto) || 0; // %
                const descuento = parseFloat(row.descuento) || 0; // %

                // Base con descuento aplicado
                const baseConDescuento = precio * (1 - descuento / 100);

                // Total = (base con descuento * cantidad) * (1 + impuesto%)
                const total =
                    baseConDescuento * cantidad * (1 + impuesto / 100);

                return currency(total, {
                    separator: ".",
                    decimal: ",",
                    precision: 2,
                }).format();
            },
            className: "text-end",
        },

        {
            data: null,
            orderable: false,
            searchable: false,
            className: "text-start",
            render: function (data, type, row) {
                return `
          <div class="dropdown">
            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              Acciones <i class="las la-angle-down ms-1"></i>
            </button>
            <div class="dropdown-menu">
            
              <a type="button" class="dropdown-item btn_editar_articulo_factura" data-id="${row.id_articulo}">Editar</a>
              <a type="button" class="dropdown-item text-danger btn_eliminar_articulo_factura"   data-id="${row.id_articulo}">Eliminar</a>
            </div>
          </div>
        `;
            },
        },
    ],
    responsive: true,
    processing: true,
    paging: false,
    pageLength: 10,
    language: {
        url: "/js/datatables/i18n/es-ES.json",
    },
    drawCallback: function () {
        let baseImponible = 0; // suma de bases (precio*cant) ya con descuento
        let impuestosAgregados = 0; // total de impuestos
        let totalFinal = 0; // base + impuestos

        this.api()
            .rows({ page: "current" })
            .data()
            .each(function (row) {
                const precio = parseFloat(row.precio) || 0;
                const cantidad = parseFloat(row.cantidad) || 0;
                const impuesto = parseFloat(row.impuesto) || 0; // %
                const descuento = parseFloat(row.descuento) || 0; // %

                // Subtotal base con descuento
                const subtotalBase = precio * (1 - descuento / 100) * cantidad;

                // Impuesto sobre esa base
                const impuestoMonto = subtotalBase * (impuesto / 100);

                // Total de la línea
                const total = subtotalBase + impuestoMonto;

                baseImponible += subtotalBase;
                impuestosAgregados += impuestoMonto;
                totalFinal += total;
            });

        $("#base_imponible").text(
            currency(baseImponible, {
                separator: ".",
                decimal: ",",
                precision: 2,
            }).format()
        );
        $("#impuestos_agregados").text(
            currency(impuestosAgregados, {
                separator: ".",
                decimal: ",",
                precision: 2,
            }).format()
        );
        $("#total_final").text(
            currency(totalFinal, {
                separator: ".",
                decimal: ",",
                precision: 2,
            }).format()
        );
    },
});

const tablaArticulos = $("#tabla_articulos_factura").DataTable();

$("#btn_add_articulo").on("click", function (e) {
    e.preventDefault();
    const id_articulo = $("#btn_add_articulo").data("id");
    const stock = parseInt($("#btn_add_articulo").data("stock"));
    const nombre = $("#nombre_articulo").val();
    const cantidad = parseInt($("#cantidad_articulo").val());
    const precioInput = parseFloat($("#precio_unitario_articulo").val());
    const impuestoInput = parseFloat($("#impuesto_articulo").val());
    const descuentoInput = parseFloat($("#descuento_articulo").val()) || 0; // Nuevo campo
    const descripcion = $("#descripcion").val();

    if (!nombre || isNaN(cantidad) || isNaN(precioInput)) {
        Swal.fire({
            title: "Error",
            text: "Por favor, completa los campos obligatorios.",
            icon: "error",
            confirmButtonText: "OK",
            confirmButtonColor: "#dc3545",
        });
        return;
    }
    if (cantidad > stock) {
        Swal.fire({
            title: "Stock insuficiente",
            text: `Solo hay ${stock} unidades disponibles en stock.`,
            icon: "warning",
            confirmButtonText: "OK",
            confirmButtonColor: "#ffc107",
        });
        return;
    }

    // Precio unitario sin impuestos
    let precioUnitario = currency(precioInput);

    // Aplicar descuento sobre precio unitario
    const descuentoMonto = precioUnitario.multiply(descuentoInput / 100);
    const precioConDescuento = precioUnitario.subtract(descuentoMonto);

    // Impuesto aplicado por unidad (sobre precio con descuento)
    const impuestoMonto = precioConDescuento.multiply(impuestoInput / 100);

    // Precio final unitario (descuento + impuesto aplicado)
    const precioFinalUnitario = precioConDescuento.add(impuestoMonto);

    // Total = precio final * cantidad
    const total = precioFinalUnitario.multiply(cantidad);

    const nuevoArticulo = {
        id_articulo: id_articulo,
        nombre,
        cantidad,
        precio: precioUnitario.value, // precio original sin impuesto ni descuento
        descuento: descuentoInput.toFixed(2),
        impuesto: impuestoInput.toFixed(2),
        descripcion,
        total: total.value,
    };

    tablaArticulos.row.add(nuevoArticulo).draw();

    // Limpiar campos
    $("#nombre_articulo").val("");
    $("#cantidad_articulo").val(1);
    $("#precio_unitario_articulo").val("");
    $("#impuesto_articulo").val("0.00");
    $("#descuento_articulo").val("0.00");
    $("#descripcion").val("");
});

let botonPresionado = null;

// Capturar el botón clickeado
document.querySelectorAll(".btn_guardar").forEach((btn) => {
    btn.addEventListener("click", function () {
        botonPresionado = this;
    });
});
document
    .getElementById("formAddFactura")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const enviar = botonPresionado?.dataset.enviar || "0";
        formData.append("enviar", enviar);
        // 1. Obtener los datos de la tabla
        const tabla = $("#tabla_articulos_factura").DataTable();
        const datosTabla = tabla.rows().data().toArray(); // array de objetos

        // 2. Agregar los datos de la tabla como JSON en el FormData
        formData.append("articulos", JSON.stringify(datosTabla));

        // 3. Mostrar el contenido del FormData (opcional, para debug)
        console.log("▶ FormData completo:");
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        try {
            Swal.fire({
                title: "Cargando...",
                html: "Por favor espere",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            const response = await fetch("/facturas", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                Swal.close();
                Swal.fire({
                    title: "¡Éxito!",
                    text: "Factura creada correctamente",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745",
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.reset();
                        window.location.href = "./facturas";
                    }
                });

                // Ocultar modal si aplica
            } else {
                Swal.close();
                // Limpiar clases de error previas
                for (const input of form.elements) {
                    input.classList.remove("is-invalid");
                    // Quitar mensajes previos si existen
                    const feedback = input.nextElementSibling;
                    if (
                        feedback &&
                        feedback.classList.contains("invalid-feedback")
                    ) {
                        feedback.remove();
                    }
                }

                // Mostrar errores recibidos del backend
                if (data.errors) {
                    for (const key in data.errors) {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add("is-invalid");
                            let feedback = document.createElement("div");
                            feedback.classList.add("invalid-feedback");
                            feedback.innerText = data.errors[key].join(", ");
                            input.after(feedback);
                        }
                    }
                } else {
                    // Mostrar error general si no hay errores específicos
                    Swal.fire({
                        title: "Error",
                        text:
                            data.message ||
                            "Hubo un error al enviar el formulario",
                        icon: "error",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#dc3545",
                    });
                }
            }
        } catch (error) {
            Swal.fire({
                title: "Error",
                text: "Hubo un problema al procesar la solicitud",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#dc3545",
            });
            console.error(error);
        }
    });
const tablaArticulosFactura = $("#tabla_articulos_factura").DataTable();

// Helper para borrar por ID aunque la fila no esté visible en la página actual
function removeRowById(dt, id) {
    const idx = dt
        .rows((i, data) => String(data.id_articulo) === String(id))
        .indexes();
    if (idx.length) {
        dt.rows(idx).remove().draw(false);
    }
}

// EDITAR: setea el select con el id y elimina la fila
$(document).on("click", ".btn_editar_articulo_factura", function (e) {
    e.preventDefault();
    const id = $(this).data("id");
    if (!id) return;
    console.log(tablaArticulosFactura.data().toArray());
    // Setear el select (si usás select2, igual sirve .trigger('change'))
    const $select = $("#select_articulos_factura");
    $select.val(String(id)).trigger("change");

    // Eliminar la fila del DataTable
    removeRowById(tablaArticulosFactura, id);
});

// ELIMINAR: solo elimina la fila
$(document).on("click", ".btn_eliminar_articulo_factura", function (e) {
    e.preventDefault();
    const id = $(this).data("id");
    if (!id) return;
    console.log(tablaArticulosFactura.data().toArray());
    removeRowById(tablaArticulosFactura, id);
});
