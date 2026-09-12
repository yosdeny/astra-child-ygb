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

// Cambiar el asunto del correo para pedido completado
add_filter( 'woocommerce_email_subject_customer_completed_order', 'cambiar_asunto_pedido_completado', 10, 2 );

function cambiar_asunto_pedido_completado( $subject, $order ) {
    $site_title = get_bloginfo( 'name' );
    return '¡Tu pedido en ' . $site_title . ' está en proceso de confirmación de pago!';
}

// Cambiar el encabezado del correo para pedido completado
add_filter( 'woocommerce_email_heading_customer_completed_order', 'cambiar_encabezado_pedido_completado', 10, 2 );

function cambiar_encabezado_pedido_completado( $heading, $order ) {
    return '¡Tu pedido está en proceso de confirmación de pago!';
}

// =================================================================
// SOLUCIÓN PARA EL AGUJERO DE FACTURAS EN ESTADO COMPLETADO
// =================================================================

// 1. Cambiar visualmente el estado "Completado" a "Prefactura" en todas las vistas
add_filter( 'woocommerce_order_status_label', 'custom_change_completed_label_to_prefactura', 10, 1 );
function custom_change_completed_label_to_prefactura( $label ) {
    if ( $label === 'Completed' || $label === 'Completado' ) {
        return 'Prefactura';
    }
    return $label;
}

// 2. Cambiar el nombre del estado completado en el admin y frontend
add_filter( 'wc_order_statuses', 'custom_rename_completed_status', 10, 1 );
function custom_rename_completed_status( $statuses ) {
    if ( isset( $statuses['wc-completed'] ) ) {
        $statuses['wc-completed'] = __( 'Prefactura', 'woocommerce' );
    }
    return $statuses;
}

// 3. Cambiar texto del botón "Ver Factura" a "Ver Prefactura" en frontend
add_filter( 'my_account_my_orders_actions', 'change_invoice_button_text_frontend', 10, 2 );
function change_invoice_button_text_frontend( $actions, $order ) {
    if ( $order->has_status( 'completed' ) ) {
        foreach ( $actions as $key => $action ) {
            if ( strpos( $action['name'], 'Factura' ) !== false || strpos( $action['name'], 'Invoice' ) !== false ) {
                $actions[$key]['name'] = 'Ver Prefactura';
            }
        }
    }
    return $actions;
}

// 4. Forzar que siempre se use el título PREFACTURA independientemente del estado
add_filter( 'wpo_wcpdf_document_title', 'force_prefactura_title_always', 10, 2 );
function force_prefactura_title_always( $title, $document = null ) {
    // Si el segundo argumento es un objeto documento, verificar su tipo
    if ( is_object( $document ) && method_exists( $document, 'get_type' ) ) {
        if ( $document->get_type() === 'invoice' ) {
            return __( 'PREFACTURA', 'astra' );
        }
    }
    // También verificar por el título original
    if ( strpos( $title, 'Factura' ) !== false || strpos( $title, 'Invoice' ) !== false ) {
        return __( 'PREFACTURA', 'astra' );
    }
    return $title;
}