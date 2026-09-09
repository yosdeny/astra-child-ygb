<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// BEGIN ENQUEUE PARENT ACTION
if ( ! function_exists( 'chld_thm_cfg_locale_css' ) ) :
	function chld_thm_cfg_locale_css( $uri ) {
		if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) ) {
			$uri = get_template_directory_uri() . '/rtl.css';
		}
		return $uri;
	}
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( ! function_exists( 'child_theme_configurator_css' ) ) :
	function child_theme_configurator_css() {
		wp_enqueue_style(
			'chld_thm_cfg_child',
			trailingslashit( get_stylesheet_directory_uri() ) . 'style.css',
			array( 'astra-theme-css', 'woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-general' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );
// END ENQUEUE PARENT ACTION

// Personalizar texto del email de pedido completado
// Cambiar el encabezado principal del email
add_filter( 'woocommerce_email_heading_customer_completed_order', 'customizar_encabezado_pedido_completado', 10, 2 );

function customizar_encabezado_pedido_completado( $heading, $order ) {
    return "Tu pedido está en proceso de confirmación de pago";
}

// Cambiar el contenido adicional del email
add_filter( 'woocommerce_email_additional_content_customer_completed_order', 'customizar_texto_pedido_completado', 10, 2 );

function customizar_texto_pedido_completado( $additional_content, $order ) {
    // Aquí puedes poner tu texto personalizado.
    // También puedes usar variables como el nombre del cliente.
    $customer_name = $order->get_billing_first_name();
    $additional_content = "¡Hola $customer_name! ¡Muchas gracias por confiar en nosotros!";

    return $additional_content;
}