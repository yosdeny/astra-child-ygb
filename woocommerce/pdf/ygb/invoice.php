<?php
/**
 * Plantilla de factura / pre-factura - formato SERVI Gloriari
 * Título dinámico: PRE-FACTURA si el pedido está en estados iniciales,
 *                  FACTURA en cualquier otro estado.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

do_action( 'wpo_wcpdf_before_document', $this->type, $this->order );

/* ------------------------------------------------------------------
   DETECCIÓN DE TÍTULO DINÁMICO
   ------------------------------------------------------------------ */
$order_status    = $this->order->get_status();
$is_pre_invoice  = in_array( $order_status, array( 'pending', 'on-hold', 'processing' ), true );

$doc_title       = $is_pre_invoice ? __( 'PREFACTURA', 'astra' ) : __( 'FACTURA', 'woocommerce-pdf-invoices-packing-slips' );
$number_label    = $is_pre_invoice ? __( 'No. PreFactura:', 'astra' ) : __( 'No. Factura:', 'astra' );
/* ------------------------------------------------------------------ */

/* Detecta si el pedido contiene productos de la categoría "combustibles" */
$has_combustibles = false;

foreach ( $this->order->get_items() as $item_check ) {
	$product_id = $item_check->get_product_id();

	if ( $product_id && has_term( 'combustibles', 'product_cat', $product_id ) ) {
		$has_combustibles = true;
		break;
	}
}

/* Número de pedido y fecha en formato dd/mm/yyyy */
$order_number = $this->order->get_order_number();
$order_date   = $this->order->get_date_created() ? $this->order->get_date_created()->date_i18n( 'd/m/Y' ) : '';

/* Campos del checkout */
$billing_rows = array(
	__( 'Nombre:', 'astra' )    => trim( $this->order->get_billing_first_name() . ' ' . $this->order->get_billing_last_name() ),
	__( 'Dirección:', 'astra' ) => $this->order->get_billing_address_1(),
	__( 'Teléfono:', 'astra' )  => $this->order->get_billing_phone(),
	__( 'Provincia:', 'astra' ) => $this->order->get_billing_state(),
	__( 'Municipio:', 'astra' ) => $this->order->get_billing_city(),
	__( 'NIT/CI:', 'astra' )    => $this->order->get_meta( 'billing_ci' ),
	__( 'Email:', 'astra' )     => $this->order->get_billing_email(),
);

/* La fila "Recogida" solo se añade si hay combustibles en la factura */
if ( $has_combustibles ) {
	$billing_rows[ __( 'Recogida:', 'astra' ) ] = __( 'Servi Gloriari. Circunvalación Sur Extremo Oeste, municipio y provincia de Ciego de Ávila', 'astra' );
	$billing_rows[ __( 'Nota:', 'astra' ) ] = __( '¡Tu pedido está en proceso de confirmación de pago!', 'astra' );
}
?>

<div class="page">

	<!-- CABECERA -->
	<table class="head">
		<tr>
			<td class="head-left">
				<?php
				$pdf_shop_name = method_exists( $this, 'get_shop_name' ) ? $this->get_shop_name() : '';

				$raw_address   = method_exists( $this, 'get_shop_address' ) ? $this->get_shop_address() : '';
				$raw_address   = preg_replace( '/<br\s*\/?>/i', "\n", $raw_address );
				$address_lines = array_values( array_filter( array_map( 'trim', explode( "\n", wp_strip_all_tags( $raw_address ) ) ) ) );

				if ( '' !== $pdf_shop_name && ! empty( $address_lines ) && 0 === strcasecmp( $address_lines[0], $pdf_shop_name ) ) {
					array_shift( $address_lines );
				}
				?>

				<?php if ( method_exists( $this, 'has_header_logo' ) && $this->has_header_logo() ) : ?>
					<div class="shop-logo"><?php $this->header_logo(); ?></div>
					<?php if ( '' !== $pdf_shop_name ) : ?>
						<div class="shop-name-text"><?php echo esc_html( $pdf_shop_name ); ?></div>
					<?php endif; ?>
				<?php else : ?>
					<h2 class="shop-name"><?php echo esc_html( $pdf_shop_name ); ?></h2>
				<?php endif; ?>

				<?php foreach ( $address_lines as $line ) : ?>
					<div class="shop-store-line"><?php echo esc_html( $line ); ?></div>
				<?php endforeach; ?>
			</td>
			<td class="head-right">
				<h1 class="document-title"><?php echo esc_html( $doc_title ); ?></h1>
				<div class="invoice-number"><?php echo esc_html( $number_label ); ?> <?php echo esc_html( $order_number ); ?></div>
				<div class="invoice-date"><?php echo esc_html( $order_date ); ?></div>
			</td>
		</tr>
	</table>

	<!-- DATOS DE FACTURACIÓN -->
	<table class="billing-details">
		<tbody>
			<?php foreach ( $billing_rows as $label => $value ) : ?>
				<?php if ( '' !== trim( (string) $value ) ) : ?>
				<tr>
					<th><?php echo esc_html( $label ); ?></th>
					<td><?php echo esc_html( $value ); ?></td>
				</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>

	<!-- PRODUCTOS -->
	<table class="order-data">
		<thead>
			<tr>
				<th class="product"><?php esc_html_e( 'Producto', 'astra' ); ?></th>
				<th class="quantity"><?php esc_html_e( 'Cantidad', 'astra' ); ?></th>
				<th class="price"><?php esc_html_e( 'Precio', 'astra' ); ?></th>
				<th class="total"><?php esc_html_e( 'Importe', 'astra' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $this->order->get_items() as $item_id => $item ) : ?>
				<?php
				$qty        = (int) $item->get_quantity();
				$line_total = (float) $item->get_subtotal();
				$unit_price = $qty > 0 ? $line_total / $qty : $line_total;
				?>
				<tr>
					<td class="product"><?php echo esc_html( $item->get_name() ); ?></td>
					<td class="quantity"><?php echo esc_html( $qty ); ?></td>
					<td class="price"><?php echo wp_kses_post( wc_price( $unit_price ) ); ?></td>
					<td class="total"><?php echo wp_kses_post( wc_price( $line_total ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr class="total-row">
				<td colspan="3" class="total-label"><?php esc_html_e( 'Total', 'astra' ); ?></td>
				<td class="total-value"><?php echo wp_kses_post( $this->order->get_formatted_order_total() ); ?></td>
			</tr>
		</tfoot>
	</table>

</div>

<?php do_action( 'wpo_wcpdf_after_document', $this->type, $this->order ); ?>