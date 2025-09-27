document.getElementById("formAddCliente")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

const form = e.target;
const formData = new FormData(form);

// limpiar estados previos
form.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
[...form.elements].forEach(el => el.classList.remove("is-invalid"));

try {
  Swal.fire({
    title: "Cargando...",
    html: "Por favor espere",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  const response = await fetch("/clientes", {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
      "Accept": "application/json",
    },
    body: formData,
  });

  // Intentar obtener JSON sin romper si no hay cuerpo o no es JSON
  let data = null;
  const ct = response.headers.get("content-type") || "";
  if (ct.includes("application/json")) {
    const text = await response.text();
    if (text) {
      try { data = JSON.parse(text); } catch {}
    }
  }

  if (response.ok) {
    Swal.close();
    Swal.fire({
      title: "¡Éxito!",
      text: "Cliente agregado correctamente",
      icon: "success",
      confirmButtonText: "OK",
      confirmButtonColor: "#28a745",
    });
    form.reset();
    const modal = bootstrap.Modal.getInstance(document.getElementById("addClient"));
    modal?.hide();
    $("#clientes_table").DataTable().ajax.reload(null, false);
    if (typeof actualizarSelectClientes === "function") actualizarSelectClientes();
  } else {
    Swal.close();

    const errors = data?.errors || {};
    for (const [key, messages] of Object.entries(errors)) {
      const input = form.querySelector(`[name="${key}"]`);
      if (input) {
        input.classList.add("is-invalid");
        const feedback = document.createElement("div");
        feedback.className = "invalid-feedback";
        feedback.innerText = Array.isArray(messages) ? messages.join(", ") : String(messages);
        input.after(feedback);
      }
    }

    if (!Object.keys(errors).length) {
      Swal.fire({
        title: "Error",
        text: data?.message || `Error ${response.status}`,
        icon: "error",
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
}
  });

