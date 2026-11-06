# DELITEX Confecciones · Sistema de gestión

Prototipo web del **Sistema de gestión para DELITEX Confecciones**, empresa que confecciona ropa deportiva personalizada.
Plantilla administrativa construida con **HTML, CSS, JavaScript y Bootstrap 5.3.8 personalizado con Sass**.

- **Datos:** todos son ficticios. No hay base de datos ni servidor: la información se guarda en el navegador (`localStorage`) para poder seguir un pedido de una pantalla a otra. Desde **Configuración → Datos** se vuelven a cargar los datos iniciales.
- **Usuario de prueba:** `mquispe` · contraseña `delitex123`.

---

## 1. Estructura del proyecto

```
PrototipoDelitex/
├── .vscode/settings.json     ← Live Sass Compiler (savePath "/css")
├── css/custom.css            ← CSS compilado (no se edita a mano)
├── images/                   ← favicon e ilustración del acceso
├── js/
│   ├── datos.js              ← datos de ejemplo y almacenamiento local
│   └── app.js                ← lógica de todas las pantallas
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
| **Pedidos** | Lista de pedidos con nuevo, ver detalle, modificar y eliminar. El registro se hace en una **ventana aparte** (modal): cliente por DNI, prenda, tela, color, tallas, estampado, fecha de entrega, precio, adelanto y saldo. **Verifica el stock de insumos al instante.** |
| **Seguimiento** | Busca por **DNI o código** y muestra todos los pasos del pedido con sus fechas y pagos. |
| **Producción** | Genera la **orden de trabajo** (descuenta el stock automáticamente) y avanza las etapas: corte → costura → estampado → acabado → listo. Incluye la ficha de fabricación imprimible. |
| **Insumos** | Inventario de telas, hilos y accesorios: nuevo, modificar y eliminar (en ventana aparte). |
| **Almacén** | Movimientos de stock: **registrar ingreso o salida** en ventana aparte, con filtros por tipo y categoría. |
| **Alertas de stock** | Insumos con stock bajo o crítico y los pedidos afectados. |
| **Pagos** | Pedidos con saldo e historial de pagos. **Registrar pago** (ventana aparte) buscando por **DNI o código**, y emite el recibo. |
| **Entregas** | Pedidos listos y entregas realizadas. **Registrar entrega** (ventana aparte) que **autoriza la salida del almacén solo con el pago completo**. |
| **Reportes** | 8 reportes por mosaico (clientes, pedidos, pedidos del día, producción, insumos utilizados, inventario, pagos y prendas entregadas) con filtros por fecha y estado, resumen y exportación a **CSV** o **PDF**. |
| **Usuarios** | Mantenimiento de usuarios y roles. |
| **Configuración** | Datos de la empresa, precios y consumo de tela por prenda, nivel crítico de inventario, mi cuenta y restablecer datos. |

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
2. Abre la carpeta `PrototipoDelitex` en VS Code y, en la terminal (**Ctrl + ñ**), ejecuta:
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

---

*Grupo 5 · Sistema Web para DELITEX Confecciones.*
