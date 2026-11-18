document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const escritorio = window.matchMedia('(min-width: 992px)');
  const btnMenu = document.getElementById('btnMenu');
  const fondo = document.getElementById('sidebarBackdrop');
  const fechaHoy = document.getElementById('fechaHoy');

  document.querySelectorAll('[data-ver-clave]').forEach((boton) => {
    boton.addEventListener('click', () => {
      const input = document.getElementById(boton.dataset.verClave);
      const visible = input.type === 'text';
      input.type = visible ? 'password' : 'text';
      boton.innerHTML = `<i class="bi ${visible ? 'bi-eye-slash' : 'bi-eye'}"></i>`;
    });
  });

  if (fechaHoy) {
    const fecha = new Date().toLocaleDateString('es-PE', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    fechaHoy.textContent = fecha.charAt(0).toUpperCase() + fecha.slice(1);
  }

  if (!btnMenu) return;

  const cerrarMenu = () => body.classList.remove('sidebar-open');

  btnMenu.addEventListener('click', () => {
    body.classList.toggle(escritorio.matches ? 'sidebar-collapsed' : 'sidebar-open');
  });
  fondo?.addEventListener('click', cerrarMenu);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarMenu(); });
  escritorio.addEventListener('change', cerrarMenu);
});
