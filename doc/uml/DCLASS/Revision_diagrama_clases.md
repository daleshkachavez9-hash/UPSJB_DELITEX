# Revisión del diagrama de clases – DELITEX Confecciones

Antes de traducir el diagrama de clases a migraciones y modelos Eloquent, se revisó con los criterios de la guía de la semana 11 (Paso 3). Un error en el diagrama se convierte en un error en la base de datos, por lo que cada observación se corrigió en el archivo `DC_Delitex.mdj` antes de continuar con la implementación.

## 1. Observaciones y correcciones

| N.° | En el diagrama | Problema | Corrección |
|---|---|---|---|
| 1 | `total`, `igv`, `precioUnitario`, `subtotal` y `monto` de tipo `double` | `double` redondea en binario y descuadra los montos. El dinero debe guardarse con precisión exacta. | `Decimal` → `DECIMAL(10,2)` |
| 2 | `calcularTotal()`, `calcularSubtotal()` y `calcularSaldo()` devuelven `double` | Devuelven montos de dinero. | Retorno `Decimal` |
| 3 | `Venta.fecha: Date` | Una venta ocurre a una hora concreta; `Date` pierde la hora. | `DateTime` |
| 4 | `Producto.cantidad`, `Inventario.stockActual` y `stockMinimo` de tipo `double` | Las telas se miden en metros y admiten decimales, pero sin errores de redondeo. | `Decimal` |
| 5 | `Pedido 1 — 1..* Pago` | `1..*` obliga a que todo pedido tenga un pago desde que se registra. | `0..*` |
| 6 | `Pedido 1 — 1 Entrega` | Un pedido recién registrado todavía no se entrega. | `0..1` |
| 7 | Venta — DetalleVenta y Pedido — DetallePedido como asociaciones simples | El detalle no existe sin su venta o su pedido: es una relación todo-parte. | Composición (rombo negro) |
| 8 | DetalleVenta y DetallePedido sin relación con Producto | Cada detalle indica qué producto se vende o se solicita, y `validarStock()` necesita el producto. | `Producto 1 — 0..* DetalleVenta` y `Producto 0..1 — 0..* DetallePedido` |
| 9 | Cuatro asociaciones llamadas «realiza» y dos «genera» | Los nombres repetidos dificultan la lectura. | «solicita», «adquiere», «registra», «recibe», «origina» |
| 10 | `IniciarSesion()` y `eleminarpago()` | Los métodos se escriben en camelCase y sin errores de escritura. | `iniciarSesion()` y `eliminarPago()` |
| 11 | `buscar(): cliente` y `buscar(): void` | El método devuelve el registro encontrado. | `buscar(): Cliente` y `buscar(): Producto` |
| 12 | `verificarStock(): void`, `generarAlerta(): void` y `consultarStock(): void` | Un método que verifica o consulta debe devolver el resultado. | `verificarStock(): bool`, `generarAlerta(): bool` y `consultarStock(): Decimal` |
| 13 | Tipos `date` y `Void` en minúscula o mayúscula indistinta | Falta uniformidad en la notación. | `Date` y `void` |
| 14 | Multiplicidades y nombres escritos como cuadros de texto | No formaban parte del modelo, y en el explorador del proyecto quedaban cinco asociaciones duplicadas (Venta — Venta, Pago — Entrega y repeticiones de Venta — Usuario y Pedido — Venta). | Multiplicidades y nombres registrados en cada asociación; duplicados eliminados |
| 15 | `Producto.categoria: String` | La categoría se escribía como texto en cada producto: se podían registrar «Tela», «Telas» o «tela» como categorías distintas y no había dónde guardar su descripción. Observación del docente en la revisión del proyecto. | Clase `Categoria` (nombre único y descripción) con la asociación `Categoria 1 — 0..* Producto` |
| 16 | El diagrama no tenía una clase para la producción | El sistema es para un taller de confecciones y el menú ya tenía el módulo Producción, pero no había dónde registrar las etapas (corte, confección, acabado) por las que pasa un pedido ni quién las realiza. | Clase `Produccion` (etapa, fechas, estado y observación) con `Pedido 1 — 0..* Produccion` «pasa por» y `Usuario 1 — 0..* Produccion` «realiza» |
| 17 | `generarReporte(): void` | El método arma el texto del reporte que se guarda en `contenido`. | `generarReporte(): String` |
| 18 | `Producto 1 — 0..* DetalleVenta` | Cuando un pedido entregado origina su venta (comprobante), sus prendas pasan al detalle de la venta, y una prenda personalizada no está en el catálogo. Con multiplicidad 1 no se podía registrar. | `Producto 0..1 — 0..* DetalleVenta`, igual que en DetallePedido |
| 19 | `Producto 1 — 0..* Inventario` | El inventario es el registro de control de cada producto (stock actual, stock mínimo y alerta). Con varios registros por producto no se sabía cuál descontar al vender ni cuál usar para la alerta. | `Producto 1 — 0..1 Inventario` e índice único en `inventories.product_id` |
| 20 | `Cliente.dni: String` | Los clubes deportivos, colegios y empresas que encargan uniformes se identifican con RUC (11 dígitos), no con DNI. | `tipoDocumento: String` (DNI o RUC) y `numeroDocumento: String` |
| 21 | `Categoria` sin tipo | DELITEX confecciona y vende prendas; las telas, hilos y accesorios son insumos del taller y no se venden. No había cómo distinguirlos. | `Categoria.tipo: String` (prenda o insumo): ventas y pedidos solo ofrecen prendas |
| 22 | `Rol` sin permisos | Todos los usuarios entraban a todos los módulos, por ejemplo una confeccionista podía eliminar usuarios. El prototipo ya definía qué módulos ve cada rol. | `Rol.modulos: String[]` y `permite(): bool` |
| 23 | `Pago.estado` con valores parcial / completo guardados una sola vez | Si se anulaba un pago, el estado de los otros quedaba desactualizado. | El estado es `adelanto` o `cancelacion` y se vuelve a calcular con `Pedido.actualizarEstadoPagos()` al registrar o anular un pago y al cambiar el total |
| 24 | DetallePedido solo con talla, color, cantidad y precio | El taller necesita saber qué prenda confeccionar, en qué tela y con qué estampado; el prototipo ya generaba una ficha de producción con esos datos y un dibujo de cada prenda. | `DetallePedido` con `tipoPrenda`, `tela`, `estampado`, `detalleEstampado` y `observacion` |
| 25 | Pedido sin imagen | El cliente suele enviar su logo, escudo o diseño, y el taller lo necesita a la vista. | `Pedido.imagenReferencia: String` (ruta de la imagen en el disco public, como la imagen del producto) |

Los atributos `idCliente`, `idVenta`, `idRol`, etc. se conservan en el diagrama. En Laravel todas las tablas usan una sola columna `id`, creada con `$table->id()`, y las claves foráneas se nombran `singular_id`.

## 2. Asociaciones del diagrama corregido

| Asociación | Multiplicidad | Tipo | Clave foránea | Regla de borrado |
|---|---|---|---|---|
| Rol «asigna» Usuario | 1 — 0..* | Asociación | `users.role_id` | `restrictOnDelete()` |
| Cliente «solicita» Pedido | 1 — 0..* | Asociación | `orders.customer_id` | `restrictOnDelete()` |
| Cliente «adquiere» Venta | 1 — 0..* | Asociación | `sales.customer_id` | `restrictOnDelete()` |
| Usuario «registra» Venta | 1 — 0..* | Asociación | `sales.user_id` | `restrictOnDelete()` |
| Usuario «genera» Reporte | 1 — 0..* | Asociación | `reports.user_id` | `restrictOnDelete()` |
| Pedido «tiene» DetallePedido | 1 ◆— 1..* | Composición | `order_details.order_id` | `cascadeOnDelete()` |
| Pedido «recibe» Pago | 1 — 0..* | Asociación | `payments.order_id` | `restrictOnDelete()` |
| Pedido «entrega» Entrega | 1 — 0..1 | Asociación 1 — 1 | `deliveries.order_id` (única) | `restrictOnDelete()` |
| Pedido «origina» Venta | 1 — 0..1 | Asociación opcional | `sales.order_id` (única, opcional) | `nullOnDelete()` |
| Venta «contiene» DetalleVenta | 1 ◆— 1..* | Composición | `detail_sales.sale_id` | `cascadeOnDelete()` |
| Producto «se vende en» DetalleVenta | 0..1 — 0..* | Asociación opcional | `detail_sales.product_id` (opcional) | `restrictOnDelete()` |
| Producto «se solicita en» DetallePedido | 0..1 — 0..* | Asociación opcional | `order_details.product_id` (opcional) | `nullOnDelete()` |
| Producto «se controla en» Inventario | 1 — 0..1 | Asociación 1 — 1 | `inventories.product_id` (única) | `restrictOnDelete()` |
| Categoria «clasifica» Producto | 1 — 0..* | Asociación | `products.category_id` | `restrictOnDelete()` |
| Pedido «pasa por» Produccion | 1 — 0..* | Asociación | `productions.order_id` | `restrictOnDelete()` |
| Usuario «realiza» Produccion | 1 — 0..* | Asociación | `productions.user_id` | `restrictOnDelete()` |

En DetallePedido el producto es opcional porque un pedido personalizado puede describir una prenda que no está registrada en el catálogo (talla, color y descripción del pedido). Por la misma razón también es opcional en DetalleVenta: la venta que origina un pedido copia sus prendas. Las ventas directas siempre llevan un producto del catálogo, porque descuentan su stock.

## 3. Orden de creación de las tablas por niveles

Una tabla es de nivel N cuando todas las tablas a las que apunta son de niveles menores. Las tablas se nombran en plural y en inglés, igual que en las migraciones del proyecto.

| Clase | Tabla | Claves foráneas | Apunta a | Nivel |
|---|---|---|---|---|
| Rol | `roles` | — | — | 1 |
| Cliente | `customers` | — | — | 1 |
| Categoria | `categories` | — | — | 1 |
| Usuario | `users` (ya existe) | `role_id` | `roles` | 2 |
| Producto | `products` | `category_id` | `categories` | 2 |
| Pedido | `orders` | `customer_id` | `customers` | 2 |
| Inventario | `inventories` | `product_id` | `products` | 3 |
| DetallePedido | `order_details` | `order_id`, `product_id` | `orders`, `products` | 3 |
| Entrega | `deliveries` | `order_id` | `orders` | 3 |
| Pago | `payments` | `order_id` | `orders` | 3 |
| Venta | `sales` | `customer_id`, `user_id`, `order_id` | `customers`, `users`, `orders` | 3 |
| Reporte | `reports` | `user_id` | `users` | 3 |
| Produccion | `productions` | `order_id`, `user_id` | `orders`, `users` | 3 |
| DetalleVenta | `detail_sales` | `sale_id`, `product_id` | `sales`, `products` | 4 |

La tabla `users` la crea Laravel y su migración se ejecuta antes que todas. Los atributos propios del diagrama (`role_id` y `dni`) se agregan con una migración de modificación (`--table=users`) que se ejecuta cuando `roles` ya existe. El atributo `nombre` del diagrama corresponde a la columna `name`.

La tabla `categories` se agregó después de las demás. Como `products` apunta a ella, su migración debe ejecutarse antes: por eso el archivo `2026_11_04_215100_create_categories_table.php` lleva una fecha anterior a la de `create_products_table`. Si se dejara la fecha del día en que se creó, `migrate:fresh` fallaría con el error 1824 (Failed to open the referenced table 'categories'), el mismo caso del reto de la migración fuera de orden. La imagen de los productos, en cambio, se agregó con una migración de modificación (`add_image_to_products_table`), porque no depende de otra tabla.

## 4. Reglas de negocio que se cumplen en el sistema

- La etapa del pedido no se cambia a mano: pasa a «En producción» al iniciar su producción, a «Terminado» cuando terminan todos sus trabajos y a «Entregado» al confirmar la entrega.
- Un pedido solo se entrega cuando está terminado y pagado por completo (`confirmarEntrega()`).
- `Producto.cantidad` e `Inventario.stockActual` guardan el mismo dato porque así está en el diagrama; el sistema los mantiene iguales con `ajustarStock()` (ventas y anulaciones) y al editar el producto o su inventario.
- El stock bajo se decide con el stock mínimo de cada inventario (`generarAlerta()`), tanto en Productos como en las alertas.
- Las fechas del pedido y del pago no pueden ser futuras, y un pago no puede ser anterior a su pedido.
- Cada usuario entra solo a los módulos de su rol, solo puede asignar roles con los mismos permisos que el suyo o menos, no puede cambiar su propio rol ni eliminarse, y nadie puede quitarle a su propio rol el acceso a Roles.
- La ficha de producción de cada pedido junta las filas de la misma prenda (tipo, color, tela y estampado) en un cuadro de tallas, dibuja cada prenda con el color de su tela y muestra la imagen de referencia. Se imprime o se guarda en PDF desde el navegador.
