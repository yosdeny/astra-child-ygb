=====================================================================
README - TEMA HIJO ASTRA
Módulo: Combustibles mínimo 10 + Modo WhatsApp híbrido
=====================================================================

---------------------------------------------------------------------
1. QUÉ HACE ESTE CÓDIGO
---------------------------------------------------------------------

Este código agrega una lógica especial para productos de la categoría
"combustibles" y un modo opcional de WhatsApp por producto.

A) Para productos de la categoría "combustibles" SIN modo WhatsApp:

   - Se aplica cantidad mínima de 10 unidades por línea de producto.
   - El selector de cantidad muestra 10 directamente.
   - El usuario puede aumentar la cantidad.
   - El usuario no puede bajar de 10 en el carrito.
   - El usuario puede eliminar el producto usando cantidad 0.
   - Si un carrito antiguo tiene 1, 2, 3, etc., se normaliza a 10.
   - Se valida también en checkout como respaldo.

B) Para productos CON modo WhatsApp activado:

   - No se aplica el mínimo de 10.
   - No se permite compra online.
   - Se oculta el formulario normal de añadir al carrito.
   - Se muestra un botón de WhatsApp.
   - Si el producto tiene stock, el botón WhatsApp está activo.
   - Si el producto no tiene stock, el botón WhatsApp aparece gris
     y deshabilitado.

C) Restauración de stock:

   - Si un pedido se cancela, el código intenta restaurar stock
     usando el meta "_reduced_stock".
   - Solo restaura si el item tiene marcado que redujo stock.

---------------------------------------------------------------------
2. DÓNDE ESTÁ EL CÓDIGO
---------------------------------------------------------------------

Archivo principal:

   /wp-content/themes/nombre-del-tema-hijo/functions.php

Todo el bloque funcional debe estar dentro de:

   if ( class_exists( 'WooCommerce' ) ) {
       ...
   }

El tema hijo también carga su CSS mediante:

   child_theme_configurator_css()

No agregar el código dos veces. Si se reemplaza el functions.php,
debe sustituirse completo, no pegar debajo del anterior.

---------------------------------------------------------------------
3. CONFIGURACIÓN PRINCIPAL EN FUNCTIONS.PHP
---------------------------------------------------------------------

Buscar estas funciones al inicio del bloque de combustibles.

A) Cantidad mínima:

   function combustibles_min_qty() {
       return 10;
   }

   Cambiar 10 por el mínimo que se desee.

   Ejemplo para mínimo 5:

   function combustibles_min_qty() {
       return 5;
   }

B) Slug de la categoría:

   function combustibles_category_slug() {
       return 'combustibles';
   }

   Cambiar "combustibles" por el slug real de la categoría si fuera
   diferente.

   El slug se ve en:
   Productos > Categorías > editar categoría > Slug.

C) Número de WhatsApp:

   function combustibles_whatsapp_number() {
       return '5355555555';
   }

   Reemplazar por el número real.

   Reglas:
   - Código de país incluido.
   - Sin signo +.
   - Sin espacios.
   - Sin guiones.
   - Solo números.

   Ejemplo correcto Cuba:

   return '5355555555';

---------------------------------------------------------------------
4. ACTIVAR MODO WHATSAPP EN UN PRODUCTO
---------------------------------------------------------------------

Para activar el modo WhatsApp en un producto:

   1. Ir a Productos.
   2. Editar el producto deseado.
   3. Ir a Datos del producto.
   4. Buscar la pestaña General.
   5. Marcar la casilla:

      Modo WhatsApp (híbrido)

   6. Guardar / Actualizar producto.

Cuando esta casilla está marcada:

   - El producto no se puede comprar online.
   - No se le aplica mínimo de 10.
   - Se muestra el botón WhatsApp.

Si la casilla no aparece por usar el nuevo editor de productos de
WooCommerce, se puede crear manualmente un campo personalizado:

   Nombre del campo:

      _combustible_whatsapp_mode

   Valor:

      yes

Para desactivarlo:

   Cambiar el valor a:

      no

   o borrar el campo.

---------------------------------------------------------------------
5. TEXTOS MODIFICABLES
---------------------------------------------------------------------

Los textos están en functions.php dentro del bloque de combustibles.

A) Aviso en producto combustible sin WhatsApp:

   Buscar:

   'Este producto se vende en bloques de %1$d unidades. Puedes aumentar la cantidad, pero no bajar de %1$d.'

   Está dentro de:

   combustibles_render_aviso_producto()

B) Botón WhatsApp activo:

   Buscar:

   'Pedir por WhatsApp'

   Está dentro de:

   combustibles_render_whatsapp_button()

C) Botón WhatsApp sin stock:

   Buscar:

   'Sin stock - Pedir por WhatsApp'

   Está dentro de:

   combustibles_render_whatsapp_button()

D) Mensaje automático enviado por WhatsApp:

   Buscar:

   'Hola, estoy interesado en "%1$s" (ID %2$d). %3$s'

   Está dentro de:

   combustibles_render_whatsapp_button()

E) Error al bajar cantidad en carrito:

   Buscar:

   'Los productos de la categoría "Combustibles" no pueden bajar de %1$d unidades.'

   Está dentro de:

   combustibles_bloquear_bajada_en_carrito()
   combustibles_validar_carrito_en_checkout()

F) Error si hay producto WhatsApp en carrito:

   Buscar:

   'Este producto se gestiona por WhatsApp y no puede comprarse online. Elimínalo del carrito para continuar.'

   Está dentro de:

   combustibles_validar_carrito_en_checkout()

G) Traducción de "Billing details":

   Buscar:

   translate_text_optimized()

   Cambiar:

   'Datos del destinatario en CUBA'

   por el texto deseado.

---------------------------------------------------------------------
6. CSS MODIFICABLE
---------------------------------------------------------------------

El CSS está en functions.php dentro de:

   combustibles_inline_css()

También se puede mover al archivo:

   /wp-content/themes/nombre-del-tema-hijo/style.css

Clases principales:

   .aviso-minimo-combustibles
   Contiene el aviso amarillo de mínimo.

   .combustibles-whatsapp-wrap
   Contenedor del botón WhatsApp.

   .combustibles-whatsapp-button
   Botón WhatsApp activo.

   .combustibles-whatsapp-button[disabled]
   .combustibles-whatsapp-button:disabled
   Botón WhatsApp deshabilitado por falta de stock.

   .combustibles-whatsapp-mode div.product form.cart
   Oculta formulario de compra en modo WhatsApp.

   .ast-cat-combustibles .quantity .minus
   Oculta botón de disminuir en producto combustible.

   tr.ast-qty-minima-combustibles .quantity .minus
   Oculta botón de disminuir en filas del carrito.

Para cambiar color del botón WhatsApp:

   background: #25d366;

Para cambiar color del botón WhatsApp deshabilitado:

   background: #cfcfcf;

---------------------------------------------------------------------
7. COMPORTAMIENTO EN PRODUCTO SIN STOCK
---------------------------------------------------------------------

Producto normal combustible sin stock:

   - WooCommerce no debería permitir compra.
   - El botón normal puede no mostrarse o mostrarse como agotado.

Producto con modo WhatsApp sin stock:

   - Se muestra botón WhatsApp.
   - El botón aparece gris.
   - El botón queda deshabilitado.
   - Muestra texto:

     Sin stock - Pedir por WhatsApp

Si se desea que el botón WhatsApp siga activo aunque no haya stock,
eliminar esta condición dentro de combustibles_render_whatsapp_button():

   if ( $product && ! $product->is_in_stock() ) {
       ...
       return;
   }

---------------------------------------------------------------------
8. MODO DE USO PARA CLIENTE / ADMINISTRADOR
---------------------------------------------------------------------

Para comprador:

   1. Entra a un producto de combustibles.
   2. Si el producto es compra normal, ve cantidad mínima 10.
   3. Puede aumentar la cantidad.
   4. No puede bajar de 10.
   5. Si elimina el producto, puede hacerlo con cantidad 0 o botón
      de eliminar.
   6. Si el producto es WhatsApp, ve botón WhatsApp.
   7. Si el producto WhatsApp está sin stock, el botón aparece gris.

Para administrador:

   1. Editar producto.
   2. Marcar o desmarcar "Modo WhatsApp (híbrido)".
   3. Guardar.
   4. Limpiar caché si hay plugin de caché.
   5. Verificar stock si se usa botón WhatsApp deshabilitado.

---------------------------------------------------------------------
9. VERIFICACIÓN DESPUÉS DE INSTALAR / MODIFICAR
---------------------------------------------------------------------

Después de guardar cambios:

   1. Limpiar caché del sitio.
   2. Limpiar caché del navegador.
   3. Probar en modo incógnito.
   4. Probar producto combustible normal.
   5. Probar producto combustible con WhatsApp activo.
   6. Probar producto combustible sin stock y WhatsApp activo.
   7. Probar carrito.
   8. Probar checkout.

Pruebas recomendadas:

   - Añadir producto combustible normal.
   - Verificar que se añade con 10.
   - Intentar bajar a 9 en carrito.
   - Verificar que muestra error.
   - Bajar a 0 o eliminar.
   - Verificar que sí se elimina.
   - Añadir producto WhatsApp.
   - Verificar que no se puede añadir al carrito.
   - Verificar botón WhatsApp visible.
   - Poner producto WhatsApp sin stock.
   - Verificar botón WhatsApp gris deshabilitado.

---------------------------------------------------------------------
10. SOLUCIÓN DE PROBLEMAS
---------------------------------------------------------------------

Problema: El mínimo no se aplica.

Verificar:

   - El producto pertenece a la categoría con slug correcto.
   - El slug configurado coincide exactamente.
   - El producto no tiene activo Modo WhatsApp.
   - El producto no está marcado como "Vendido individualmente".
   - No hay otro plugin modificando cantidad.
   - Se limpió caché.

---------------------------------------------------------------------

Problema: El botón WhatsApp no aparece.

Verificar:

   - El producto tiene activo Modo WhatsApp.
   - El código está bien pegado en functions.php.
   - No hay error fatal en el sitio.
   - La estructura del producto en Astra incluye botón / add to cart.
   - No hay CSS externo ocultando el botón.
   - Se limpió caché.

---------------------------------------------------------------------

Problema: El botón WhatsApp aparece activo aunque no haya stock.

Verificar:

   - Está usando la versión actual de
     combustibles_render_whatsapp_button().
   - La función contiene esta condición:

     if ( $product && ! $product->is_in_stock() )

   - El producto tiene estado de inventario "Agotado".

---------------------------------------------------------------------

Problema: El botón WhatsApp aparece gris pero el producto sí tiene stock.

Verificar:

   - Estado del inventario del producto.
   - Si es variable, revisar stock de variaciones.
   - Si hay plugin de stock externo modificando estado.

---------------------------------------------------------------------

Problema: El producto WhatsApp aún se puede comprar.

Verificar:

   - El campo _combustible_whatsapp_mode tiene valor yes.
   - El filtro woocommerce_is_purchasable no está siendo sobrescrito
     por otro plugin.
   - No hay caché de servidor.
   - El producto no es externo con URL de compra independiente.

---------------------------------------------------------------------

Problema: El carrito deja bajar de 10.

Verificar:

   - El producto pertenece a la categoría configurada.
   - El producto no tiene modo WhatsApp activo.
   - WooCommerce está usando carrito clásico, no bloque de carrito.
   - No hay plugin de carrito personalizado.
   - El filtro woocommerce_update_cart_validation está activo.

Nota importante:

   Si se usa el bloque de carrito de WooCommerce / Gutenberg, algunos
   filtros pueden no comportarse igual que en el carrito clásico.
   Para máxima compatibilidad, usar shortcode/carrito clásico.

---------------------------------------------------------------------

Problema: El stock no se restaura al cancelar pedido.

Verificar:

   - El pedido tiene productos con control de stock.
   - El item tiene meta _reduced_stock.
   - No hay plugin externo gestionando stock.
   - El estado del pedido pasa exactamente a "cancelled".

---------------------------------------------------------------------
11. LIMITACIONES / NOTAS IMPORTANTES
---------------------------------------------------------------------

1. El mínimo es por línea de producto.

   Ejemplo:
   Si un cliente añade dos combustibles distintos, quedará:

      10 + 10 = 20

   No es un mínimo global de categoría.

   Si se desea mínimo global sumando todos los combustibles, la
   lógica debe cambiarse.

2. El modo WhatsApp desactiva la compra online del producto.

   No está pensado para mostrar WhatsApp y compra normal al mismo
   tiempo.

3. El botón WhatsApp depende del stock status.

   Si el producto está marcado como "Agotado", el botón WhatsApp se
   deshabilita.

4. El campo WhatsApp puede no aparecer en el nuevo editor de
   productos.

   En ese caso usar campo personalizado:

      _combustible_whatsapp_mode = yes

5. No duplicar funciones.

   Si se pega el código dos veces, el sitio puede romper por
   redeclaración de funciones.

---------------------------------------------------------------------
12. RESUMEN RÁPIDO DE DONDE MODIFICAR
---------------------------------------------------------------------

Cantidad mínima:
   functions.php > combustibles_min_qty()

Categoría:
   functions.php > combustibles_category_slug()

Número WhatsApp:
   functions.php > combustibles_whatsapp_number()

Activar WhatsApp por producto:
   Producto > Datos del producto > General > Modo WhatsApp (híbrido)

Desactivar compra online + mostrar WhatsApp:
   Automático al activar Modo WhatsApp.

Textos:
   functions.php > bloque combustibles.

CSS:
   functions.php > combustibles_inline_css()
   o style.css del tema hijo.

Traducción checkout:
   functions.php > translate_text_optimized()

Restauración stock:
   functions.php > forzar_restaurar_stock_al_cancelar()