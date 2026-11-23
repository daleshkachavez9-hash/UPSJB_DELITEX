// Ventanas de búsqueda (partials/modal-buscar): filtran la tabla mientras se escribe
// y avisan qué registro se eligió con el evento "buscador:elegido".
document.addEventListener('DOMContentLoaded', () => {
  // Sin tildes y en minúsculas: "Huaman" encuentra "Huamán"
  const normalizar = (texto) => texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();

  document.querySelectorAll('.modal-buscar').forEach((modal) => {
    const input = modal.querySelector('.buscar-texto');
    const filas = [...modal.querySelectorAll('tbody tr')];
    const vacio = modal.querySelector('.buscar-vacio');

    const filtrar = () => {
      const buscado = normalizar(input.value);
      let visibles = 0;
      filas.forEach((fila) => {
        const coincide = normalizar(fila.dataset.texto).includes(buscado);
        fila.classList.toggle('d-none', !coincide);
        if (coincide) visibles++;
      });
      vacio.classList.toggle('d-none', visibles > 0);
    };

    input.addEventListener('input', filtrar);

    // Cada vez que se abre, empieza con el buscador vacío y listo para escribir
    modal.addEventListener('shown.bs.modal', () => {
      input.value = '';
      filtrar();
      input.focus();
    });

    modal.addEventListener('click', (e) => {
      const boton = e.target.closest('.buscar-elegir');
      if (!boton) return;
      const fila = boton.closest('tr');
      modal.dispatchEvent(new CustomEvent('buscador:elegido', { bubbles: true, detail: { ...fila.dataset } }));
      bootstrap.Modal.getInstance(modal)?.hide();
    });
  });
});
