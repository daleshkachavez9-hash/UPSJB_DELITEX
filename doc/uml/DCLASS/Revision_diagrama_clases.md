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
| Producto «se vende en» DetalleVenta | 1 — 0..* | Asociación | `detail_sales.product_id` | `restrictOnDelete()` |
| Producto «se solicita en» DetallePedido | 0..1 — 0..* | Asociación opcional | `order_details.product_id` (opcional) | `nullOnDelete()` |
| Producto «se controla en» Inventario | 1 — 0..* | Asociación | `inventories.product_id` | `restrictOnDelete()` |

En DetallePedido el producto es opcional porque un pedido personalizado puede describir una prenda que no está registrada en el catálogo (talla, color y descripción del pedido).

## 3. Orden de creación de las tablas por niveles

Una tabla es de nivel N cuando todas las tablas a las que apunta son de niveles menores. Las tablas se nombran en plural y en inglés, igual que en las migraciones del proyecto.

| Clase | Tabla | Claves foráneas | Apunta a | Nivel |
|---|---|---|---|---|
| Rol | `roles` | — | — | 1 |
| Cliente | `customers` | — | — | 1 |
| Producto | `products` | — | — | 1 |
| Usuario | `users` (ya existe) | `role_id` | `roles` | 2 |
| Pedido | `orders` | `customer_id` | `customers` | 2 |
| Inventario | `inventories` | `product_id` | `products` | 2 |
| DetallePedido | `order_details` | `order_id`, `product_id` | `orders`, `products` | 3 |
| Entrega | `deliveries` | `order_id` | `orders` | 3 |
| Pago | `payments` | `order_id` | `orders` | 3 |
| Venta | `sales` | `customer_id`, `user_id`, `order_id` | `customers`, `users`, `orders` | 3 |
| Reporte | `reports` | `user_id` | `users` | 3 |
| DetalleVenta | `detail_sales` | `sale_id`, `product_id` | `sales`, `products` | 4 |

La tabla `users` la crea Laravel y su migración se ejecuta antes que todas. Los atributos propios del diagrama (`role_id` y `dni`) se agregan con una migración de modificación (`--table=users`) que se ejecuta cuando `roles` ya existe. El atributo `nombre` del diagrama corresponde a la columna `name`.
