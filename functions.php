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

// Cambiar texto "Hemos terminado de procesar tu pedido" por "Tu pedido está en proceso de confirmación de pago"
add_filter( 'gettext', 'cambiar_texto_pedido_completado_wc', 20, 3 );

function cambiar_texto_pedido_completado_wc( $translated, $text, $domain ) {
    if ( $domain !== 'woocommerce' ) {
        return $translated;
    }

    // Cambiar texto en inglés (si el sitio usa traducciones desde inglés)
    if ( $text === 'We have finished processing your order.' ) {
        return 'Tu pedido está en proceso de confirmación de pago.';
    }
    
    // Cambiar texto en español (traducción por defecto de WooCommerce)
    if ( $translated === 'Hemos terminado de procesar tu pedido.' ) {
        return 'Tu pedido está en proceso de confirmación de pago.';
    }

    return $translated;
}