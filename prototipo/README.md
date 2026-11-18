# DELITEX Confecciones · Sistema de gestión

Prototipo web del **Sistema de gestión para DELITEX Confecciones**, empresa que confecciona ropa deportiva personalizada.
Plantilla administrativa construida con **HTML, CSS, JavaScript y Bootstrap 5.3.8 personalizado con Sass**.

- **Datos:** todos son ficticios. No hay base de datos ni servidor: la información se guarda en el navegador (`localStorage`) para poder seguir un pedido de una pantalla a otra. Desde **Configuración → Datos** se vuelven a cargar los datos iniciales.
- **Inicio de sesión con tres roles** (Dueña, Administradora y Confeccionista). Ver la sección [8. Roles, permisos y seguridad](#8-roles-permisos-y-seguridad).

| Rol | Usuario | Correo | Contraseña | Página inicial |
|---|---|---|---|---|
| Dueña (cuenta principal) | `mquispe` | maria.quispe@delitex.pe | `delitex123` | Dashboard general |
| Administradora | `lrojas` | lucia.rojas@delitex.pe | `delitex123` | Dashboard operativo |
| Confeccionista | `jtaipe` | jorge.taipe@delitex.pe | `delitex123` | Producción |
| Confeccionista | `rcampos` | rocio.campos@delitex.pe | `delitex123` | Producción |
| Administradora (inactiva) | `calva` | carmen.alva@delitex.pe | `delitex123` | — (no puede entrar) |

> Son **cuentas de prueba** (aparecen con la etiqueta «Prueba» en Usuarios). No son credenciales de producción.

---

## 1. Estructura del proyecto

```
prototipo/
├── .vscode/settings.json     ← Live Sass Compiler (savePath "/css")
├── css/custom.css            ← CSS compilado (no se edita a mano)
├── images/                   ← logo (logo-delitex.svg), emblema (logo-emblema.svg) y favicon
├── js/
│   ├── datos.js              ← datos de ejemplo y almacenamiento local
│   ├── auth.js               ← inicio de sesión, sesión, roles y permisos (se carga en el <head>)
│   ├── app.js                ← lógica de todas las pantallas
│   └── ficha.js              ← ficha de producción (vista, impresión y PDF)
├── sass/custom.scss          ← estilos (aquí se trabaja)
├── index.html                ← inicio de sesión
├── registro.html             ← crear cuenta
├── recuperar.html            ← recuperar contraseña
├── restablecer.html          ← nueva contraseña
├── dashboard.html            ← menú principal y resumen
├── clientes.html
├── pedidos.html
├── seguimiento.html
├── produccion.html
├── insumos.html
├── almacen.html
├── alertas.html
├── pagos.html
├── entregas.html
├── reportes.html
├── usuarios.html
├── configuracion.html
├── package.json
└── README.md
```

---

## 2. Pantallas y flujo del pedido

| Pantalla | Qué permite |
|---|---|
| **Inicio de sesión / Registro / Recuperar / Restablecer** | Acceso al sistema, creación de cuenta y cambio de contraseña. |
| **Dashboard** | Menú principal con accesos, indicadores, pedidos recientes, avance de producción, alertas de inventario y botón **Nuevo pedido** (modal). |
| **Clientes** | Registrar, **buscar por DNI**, modificar y eliminar clientes. Accesos a nuevo pedido y seguimiento. |
| **Pedidos** | Lista de pedidos con nuevo, ver detalle, modificar y eliminar. El registro se hace en una **ventana aparte** (modal): cliente por DNI y **una o varias prendas** (cada una con su tela, color, tallas, estampado, precio y observaciones), fecha de entrega, adelanto y saldo. **Verifica el stock de insumos al instante**, sumando todas las prendas.  Botón **Generar ficha de producción**: muestra la ficha con los datos que se van ingresando y permite **imprimirla o descargarla en PDF** antes o después de registrar. El **adelanto** se escribe como texto (se guarda el monto exacto, acepta coma o punto) y debe cubrir el **adelanto mínimo** de Configuración. |
| **Seguimiento** | Busca por **DNI o código** y muestra todos los pasos del pedido con sus fechas y pagos. |
| **Producción** | Genera la **orden de trabajo** (descuenta el stock automáticamente) y avanza las etapas: corte → costura → estampado → acabado → listo. La orden de trabajo **requiere adelanto**. Incluye la ficha de producción (imprimir / PDF). |
| **Insumos** | Inventario de telas, hilos y accesorios: nuevo, modificar y eliminar (en ventana aparte). |
| **Almacén** | Movimientos de stock: **registrar ingreso o salida** en ventana aparte, con filtros por tipo y categoría. |
| **Alertas de stock** | Insumos con stock bajo o crítico y los pedidos afectados. |
| **Pagos** | Pedidos con saldo e historial de pagos. **Registrar pago** (ventana aparte) buscando por **DNI o código**, y emite el recibo. |
| **Entregas** | Pedidos listos y entregas realizadas. **Registrar entrega** (ventana aparte) que **autoriza la salida del almacén solo con el pago completo**. |
| **Reportes** | 8 reportes por mosaico (clientes, pedidos, pedidos del día, producción, insumos utilizados, inventario, pagos y prendas entregadas) con filtros por fecha y estado, resumen y exportación a **CSV** o **PDF**. |
| **Usuarios** | Mantenimiento de cuentas: identificador, nombre, usuario, correo, contraseña (cifrada), rol y estado. |
| **Configuración** | Datos de la empresa, precios y consumo de tela por prenda, nivel crítico de inventario, **seguridad (roles y permisos)**, mi cuenta y restablecer datos. Solo la dueña. |

**Pasos de un pedido:**
1. Registro del pedido (con verificación de stock).
2. Adelanto recibido.
3. Orden de trabajo generada y descuento de insumos.
4. Corte.
5. Costura.
6. Estampado (si corresponde).
7. Acabado.
8. Listo para entrega.
9. Pago completo.
10. Entregado.

---

## 3. Ver el prototipo

`css/custom.css` ya está compilado, así que se puede ver de inmediato:

- En VS Code, clic derecho sobre `index.html` → **Open with Live Server** (recomendado), o
- Doble clic en `index.html`.

Se necesita internet para Google Fonts, Bootstrap Icons y el JavaScript de Bootstrap (CDN).

---

## 4. Configurar las herramientas

1. Instala **Node.js (LTS)**, **Visual Studio Code** y las extensiones **Live Server** y **Live Sass Compiler** (Glenn Marks).
2. Abre la carpeta `prototipo` en VS Code y, en la terminal (**Ctrl + ñ**), ejecuta:
   ```bash
   npm install
   ```
   Esto instala `bootstrap@5.3.8` en `node_modules/`.
3. Abre `sass/custom.scss` y pulsa **Watch Sass** en la barra inferior. Cada vez que guardes, se regenera `css/custom.css`.
4. Cuando ya exista `node_modules`, puedes cambiar en cada HTML el script de Bootstrap del CDN por la copia local:
   ```html
   <script src="./node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
   ```

> Los avisos **DEPRECATION WARNING** de Sass sobre `@import` son normales.

---

## 5. Personalización con Sass

Variables definidas **antes** del `@import` de Bootstrap en `sass/custom.scss`:

| Variable | Valor |
|---|---|
| `$primary` | `#9BBDE0` azul pastel |
| `$dark` | `#526681` azul pizarra (sidebar) |
| `$body-bg` | `#F8F5F0` blanco cálido |
| `$font-family-sans-serif` | `"Nunito"` (Google Fonts) |
| `$border-radius` | `.75rem` |

Colores complementarios: lavanda `#E8E0F4`, verde salvia `#DDEDE5`, amarillo suave `#F7E9BD` y rosa empolvado `#F3DDE1`, disponibles con las clases `soft-*`, `bg-*` y los botones `btn-amarillo`, `btn-lavanda` y `btn-salvia`.

---

## 6. Opcional: CSS reducido con PurgeCSS

```bash
npm run build
```

Genera `css/reducido.css` revisando todos los HTML y los archivos de `js/`. Para usarlo, cambia `./css/custom.css` por `./css/reducido.css` en los HTML y vuelve a ejecutar el comando cada vez que cambies estilos.

---

## 7. Errores frecuentes

| Síntoma | Solución |
|---|---|
| `npm.ps1 no se puede cargar…` | Usa **Command Prompt** en la terminal de VS Code, o ejecuta `Set-ExecutionPolicy -Scope CurrentUser RemoteSigned` en PowerShell y reinicia VS Code. |
| `Can't find stylesheet to import` | Falta `node_modules`: ejecuta `npm install`. |
| Cambié una variable y no pasa nada | La variable debe estar **arriba** del `@import`. |
| Los datos no son los iniciales | **Configuración → Datos → Restablecer datos**. |
| «La cuenta fue modificada fuera del sistema» | Se cambió el rol o el estado directamente en el navegador. La dueña puede corregirla en **Usuarios** (o usar **Restablecer datos**). |
| Olvidé la contraseña de la dueña | En la demo el enlace de recuperación de cuentas privilegiadas no se muestra en pantalla (necesita correo real). Borra los datos del sitio en el navegador para volver a las cuentas de prueba. |

---

## 8. Roles, permisos y seguridad

### Qué puede hacer cada rol

| | Dueña | Administradora | Confeccionista |
|---|---|---|---|
| Página inicial | Dashboard general | Dashboard operativo | Producción |
| Dashboard, Reportes, Clientes, Pedidos, Pagos, Entregas, Almacén, Alertas | ✔ | ✔ | ✖ |
| Producción | ✔ todas las órdenes | ✔ todas las órdenes: genera OT y asigna confeccionista | ✔ solo **sus** órdenes asignadas: ficha y avance de etapas |
| Seguimiento | ✔ | ✔ | ✔ solo sus pedidos, sin precios ni pagos |
| Insumos | ✔ | ✔ | Solo consulta |
| Usuarios | ✔ cualquier cuenta (la principal no se elimina) | Solo cuentas de confeccionista, si la dueña lo permite (Configuración → Seguridad) | ✖ |
| Configuración (crítica) | ✔ | ✖ | ✖ |
| Mi cuenta (nombre, correo y contraseña propios) | En Configuración | Ventana «Mi perfil» | Ventana «Mi perfil» |

- La tabla de permisos está en un solo lugar: `PERMISOS` en `js/auth.js`. El menú lateral, los mosaicos del dashboard, los botones y cada operación (guardar, eliminar, pagar, avanzar etapa…) consultan esa tabla.
- Si alguien escribe la URL de un módulo sin permiso (por ejemplo `pedidos.html`, `usuarios.html`, `pagos.html` o `configuracion.html` como confeccionista), la página no se muestra y aparece **«No tienes permisos para acceder a este módulo»**.
- El rol no se elige al iniciar sesión: está en la cuenta. El **registro público** crea siempre una confeccionista **inactiva**, que la dueña o la administradora activan desde Usuarios.
- La cuenta principal (dueña) es única: nadie más puede editarla, eliminarla, desactivarla ni quitarle el rol.

### Medidas incluidas en el prototipo

- Contraseñas guardadas como hash SHA-256 iterado con sal (nunca en texto plano).
- Sesión con token aleatorio; el navegador no guarda el rol. Al cerrar sesión el token se invalida (no sirve copiarlo y volver a ponerlo). La sesión vence tras 8 horas sin uso y se cierra en todas las pestañas.
- Cambiar el rol, el estado o la contraseña de una cuenta cierra sus sesiones abiertas.
- Cada cuenta lleva un sello de integridad: si se cambia el rol desde el `localStorage` o la consola, la sesión se cierra y la cuenta queda bloqueada.
- Bloqueo de 5 minutos tras 5 intentos fallidos de inicio de sesión.
- Recuperación de contraseña con enlace de un solo uso que vence en 15 minutos.

### ⚠ Lo que necesita un backend para ser seguro en producción

Todo lo anterior se ejecuta **en el navegador**, donde el usuario puede leer y cambiar el código y los datos. Sirve para la demostración, pero **la seguridad del navegador por sí sola no es suficiente**. Para usar el sistema con datos reales hace falta un servidor que:

1. **Guarde los datos y las cuentas en una base de datos**, no en `localStorage`.
2. **Valide las credenciales en el servidor** y cifre las contraseñas con bcrypt o Argon2 (no SHA-256).
3. **Maneje la sesión con una cookie `HttpOnly`, `Secure` y `SameSite`** emitida por el servidor (o tokens firmados con una clave que nunca llegue al navegador). El sello de integridad de este prototipo usa una clave visible en el código: no protege de alguien con conocimientos técnicos.
4. **Compruebe el rol y el permiso en cada petición** (crear pedido, registrar pago, avanzar etapa, crear usuario…). Ocultar botones o bloquear páginas en el navegador es solo una ayuda visual.
5. **Envíe el enlace de recuperación por correo** y limite los intentos de inicio de sesión desde el servidor.
6. Use **HTTPS** y registre una bitácora de accesos y cambios de permisos.

### Cómo comprobar los permisos

1. Abre `index.html` (con Live Server). Si quieres partir de cero: **Configuración → Datos → Restablecer datos** (como dueña) o borra los datos del sitio.
2. **Dueña** (`mquispe`): entra al *Dashboard general*; el menú muestra los 13 módulos. Abre **Configuración → Seguridad** y **Usuarios**: puede crear administradoras y confeccionistas.
3. **Administradora** (`lrojas`): entra al *Dashboard operativo*. Registra un pedido, un pago o una entrega. En **Producción** genera una orden de trabajo y elige la confeccionista. En **Usuarios** solo puede modificar confeccionistas (la dueña aparece «Sin permiso»). Escribe `configuracion.html` en la barra: acceso restringido.
4. **Confeccionista** (`jtaipe`): entra directo a **Producción** y ve solo sus órdenes asignadas. Pulsa el botón de la etapa siguiente para avanzar una orden y abre su ficha de producción. En **Insumos** solo consulta.
5. Como confeccionista, escribe en la barra `pedidos.html`, `usuarios.html`, `pagos.html`, `clientes.html` y `configuracion.html`: todas muestran «No tienes permisos para acceder a este módulo».
6. Navega entre páginas: la sesión y el menú se mantienen. Pulsa **Cerrar sesión** y luego el botón «Atrás» o escribe `produccion.html`: vuelve al inicio de sesión.
7. Con la sesión de la confeccionista abierta, en la consola (F12) cambia su rol a «Dueña» dentro de `localStorage` (`delitex-datos-v2`) y recarga: la sesión se cierra con el aviso de cambio no autorizado.

---

*Grupo 5 · Sistema Web para DELITEX Confecciones.*
