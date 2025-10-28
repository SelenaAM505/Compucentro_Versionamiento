 document.addEventListener("DOMContentLoaded", () => {
  const selectCurso = document.getElementById("curso_preferencia");
  const selectJornada = document.getElementById("jornada");
  const btnCancelar = document.getElementById("btn-cancelar");

  // 1) Cargar cursos
  fetch("../../Backend/src/PHP/obtener_cursos.php")
    .then(res => res.json())
    .then(data => {
      selectCurso.innerHTML = `<option value="" disabled selected hidden></option>`;
      data.forEach(c => {
        selectCurso.innerHTML += `<option value="${c.id_curso}">${c.nombre}</option>`;
      });
    });

  // 2) Cuando el usuario elige un curso → cargar jornadas
  selectCurso.addEventListener("change", () => {
    const idCurso = selectCurso.value;

    fetch(`../../Backend/src/PHP/obtener_jornadas.php?curso=${idCurso}`)
      .then(res => res.json())
      .then(data => {
        selectJornada.innerHTML = `<option value="" disabled selected hidden></option>`;
        data.forEach(j => {
          selectJornada.innerHTML += `<option value="${j.id_jornada}">${j.nombre}</option>`;
        });
      });
  });

  // 3) Función del botón CANCELAR (ya dentro del DOMContentLoaded)
btnCancelar.addEventListener('click', (e) => {
  e.stopPropagation(); // detiene cualquier validación o evento padre
  e.preventDefault(); // evita validaciones "required" del form

  const form = document.querySelector('form');
  form.reset(); // limpia valores

  // Limpia visualmente los select y labels flotantes
  document.querySelectorAll('.input-group input, .input-group select, .input-group textarea').forEach(campo => {
    campo.value = ''; // limpia valor
    campo.blur(); // quita foco
  });

  // Limpia manualmente selects para que vuelvan a su estado original
  document.getElementById('curso_preferencia').selectedIndex = 0;
  document.getElementById('jornada').selectedIndex = 0;
  document.getElementById('genero').selectedIndex = 0;

  // Pequeña confirmación visual opcional
  const mensaje = document.createElement('div');
  mensaje.textContent = "🧹 Formulario limpiado correctamente.";
  mensaje.style.background = "#083B70";
  mensaje.style.color = "#fff";
  mensaje.style.padding = "10px";
  mensaje.style.borderRadius = "8px";
  mensaje.style.textAlign = "center";
  mensaje.style.marginTop = "15px";
  mensaje.style.animation = "fadeOut 2s forwards";
  document.querySelector('.form-container').appendChild(mensaje);
  setTimeout(() => mensaje.remove(), 2000);
});
});