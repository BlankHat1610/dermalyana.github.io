<?php
/**
 * Product Quantity for WooCommerce - Fixed Section Settings
 *
 * @version 1.6.0
 * @since   1.6.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PQ_Settings_Fixed' ) ) :

class Alg_WC_PQ_Settings_Fixed extends Alg_WC_PQ_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function __construct() {
		$this->id   = 'fixed';
		$this->desc = __( 'Fixed Quantity', 'product-quantity-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Exact (i.e. Fixed) Quantity Options', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_exact_qty_options',
			),
			array(
				'title'    => __( 'Allowed quantity', 'product-quantity-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'product-quantity-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_pq_exact_qty_allowed_section_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'All products', 'product-quantity-for-woocommerce' ) . ': ' .
					sprintf( __( 'Allowed quantities as comma separated list, e.g.: %s.', 'product-quantity-for-woocommerce' ), '<code>3,7,9</code>' ),
				'desc_tip' => __( 'Ignored if empty.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_exact_qty_allowed',
				'default'  => '',
				'type'     => 'text',
				'css'      => 'width:100%;',
			),
			array(
				'desc'     => __( 'Per product', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'This will add meta box to each product\'s edit page.', 'product-quantity-for-woocommerce' ) .
					apply_filters( 'alg_wc_pq', '<br>' . sprintf( 'You will need %s to set per product allowed quantities.',
						'<a target="_blank" href="https://wpfactory.com/item/product-quantity-for-woocommerce/">' . 'Product Quantity for WooCommerce Pro' . '</a>' ), 'settings' ),
				'id'       => 'alg_wc_pq_exact_qty_allowed_per_product_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_pq', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'desc'     => __( 'Allowed quantity message.', 'product-quantity-for-woocommerce' ) . ' ' .
					$this->message_replaced_values( array( '%product_title%', '%allowed_quantity%', '%quantity%' ) ),
				'desc_tip' => __( 'Message to be displayed to customer on wrong allowed quantities.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_exact_qty_allowed_message',
				'default'  => __( 'Allowed quantity for %product_title% is %allowed_quantity%. Your current quantity is %quantity%.', 'product-quantity-for-woocommerce' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;',
				'alg_wc_pq_raw' => true,
			),
			array(
				'title'    => __( 'Disallowed quantity', 'product-quantity-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'product-quantity-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_pq_exact_qty_disallowed_section_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'All products', 'product-quantity-for-woocommerce' ) . ': ' .
					sprintf( __( 'Disallowed quantities as comma separated list, e.g.: %s.', 'product-quantity-for-woocommerce' ), '<code>3,7,9</code>' ),
				'desc_tip' => __( 'Ignored if empty.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_exact_qty_disallowed',
				'default'  => '',
				'type'     => 'text',
				'css'      => 'width:100%;',
			),
			array(
				'desc'     => __( 'Per product', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'This will add meta box to each product\'s edit page.', 'product-quantity-for-woocommerce' ) .
					apply_filters( 'alg_wc_pq', '<br>' . sprintf( 'You will need %s to set per product disallowed quantities.',
						'<a target="_blank" href="https://wpfactory.com/item/product-quantity-for-woocommerce/">' . 'Product Quantity for WooCommerce Pro' . '</a>' ), 'settings' ),
				'id'       => 'alg_wc_pq_exact_qty_disallowed_per_product_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'custom_attributes' => apply_filters( 'alg_wc_pq', array( 'disabled' => 'disabled' ), 'settings' ),
			),
			array(
				'desc'     => __( 'Disallowed quantity message.', 'product-quantity-for-woocommerce' ) . ' ' .
					$this->message_replaced_values( array( '%product_title%', '%disallowed_quantity%', '%quantity%' ) ),
				'desc_tip' => __( 'Message to be displayed to customer on wrong disallowed quantities.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_exact_qty_disallowed_message',
				'default'  => __( 'Disallowed quantity for %product_title% is %disallowed_quantity%. Your current quantity is %quantity%.', 'product-quantity-for-woocommerce' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;',
				'alg_wc_pq_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_exact_qty_options',
			),
		);
	}

}

endif;

return new Alg_WC_PQ_Settings_Fixed();
