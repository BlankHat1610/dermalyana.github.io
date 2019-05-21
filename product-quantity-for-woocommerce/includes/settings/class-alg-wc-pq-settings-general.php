<?php
/**
 * Product Quantity for WooCommerce - General Section Settings
 *
 * @version 1.6.2
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PQ_Settings_General' ) ) :

class Alg_WC_PQ_Settings_General extends Alg_WC_PQ_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'product-quantity-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.6.2
	 * @since   1.0.0
	 * @todo    [dev] maybe split settings into subsections (i.e. "Display Options", "Quantity Dropdown" etc.)
	 * @todo    [feature] Force initial quantity on single product page - add "Custom value" option
	 * @todo    [feature] Force initial quantity on single product page - per product
	 */
	function get_settings() {
		$main_settings = array(
			array(
				'title'    => __( 'Product Quantity Options', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_options',
			),
			array(
				'title'    => __( 'Product Quantity for WooCommerce', 'product-quantity-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'product-quantity-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_pq_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_options',
			),
		);
		$general_settings = array(
			array(
				'title'    => __( 'General Options', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_general_options',
			),
			array(
				'title'    => __( 'Decimal quantities', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'Save settings after enabling this option, so you could enter decimal quantities in step, min and/or max quantity options.', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_decimal_quantities_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Force initial quantity on single product page', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_force_on_single',
				'default'  => 'disabled',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'disabled' => __( 'Do not force', 'product-quantity-for-woocommerce' ),
					'min'      => __( 'Force to min quantity', 'product-quantity-for-woocommerce' ),
					'max'      => __( 'Force to max quantity', 'product-quantity-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Force initial quantity on archives', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_force_on_loop',
				'default'  => 'disabled',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'disabled' => __( 'Do not force', 'product-quantity-for-woocommerce' ),
					'min'      => __( 'Force to min quantity', 'product-quantity-for-woocommerce' ),
					'max'      => __( 'Force to max quantity', 'product-quantity-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Force minimum quantity', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Will force all minimum quantities to %s.', 'product-quantity-for-woocommerce' ), '<code>1</code>' ) . ' ' .
					__( 'This includes cart items, grouped products etc.', 'product-quantity-for-woocommerce' ) . ' ' .
					__( 'Ignored if "Minimum quantity" section is enabled.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_force_cart_min_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( '"Add to cart" validation', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_add_to_cart_validation',
				'default'  => 'disable',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'disable' => __( 'Do not validate', 'product-quantity-for-woocommerce' ),
					'notice'  => __( 'Validate and add notices', 'product-quantity-for-woocommerce' ),
					'correct' => __( 'Validate and auto-correct quantities', 'product-quantity-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Enable cart notices', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_cart_notice_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Block checkout page', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'Stops customer from reaching the <strong>checkout</strong> page on wrong quantities.', 'product-quantity-for-woocommerce' ) . ' ' .
					__( 'Customer will be redirected to the <strong>cart</strong> page.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_stop_from_seeing_checkout',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'On variation change (variable products)', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_variation_change',
				'default'  => 'do_nothing',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'do_nothing'   => __( 'Do nothing', 'product-quantity-for-woocommerce' ),
					'reset_to_min' => __( 'Reset to min quantity', 'product-quantity-for-woocommerce' ),
					'reset_to_max' => __( 'Reset to max quantity', 'product-quantity-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Quantity input style', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'Ignored if empty.', 'product-quantity-for-woocommerce' ),
				'desc'     => sprintf( __( 'E.g.: %s', 'product-quantity-for-woocommerce' ), '<code>width: 100px !important; max-width: 100px !important;</code>' ),
				'id'       => 'alg_wc_pq_qty_input_style',
				'default'  => '',
				'type'     => 'textarea',
				'css'      => 'width:100%;',
				'alg_wc_pq_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_general_options',
			),
			array(
				'title'    => __( 'Quantity Dropdown Options', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_qty_dropdown_options',
			),
			array(
				'title'    => __( 'Quantity as dropdown', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'Will replace standard WooCommerce quantity number input with dropdown.', 'product-quantity-for-woocommerce' ) . '<br>' .
					__( 'Please note that <strong>maximum quantity</strong> value must be set for the product (either via "Maximum Quantity" section or e.g. by setting maximum available product stock quantity or with "Max value fallback" option below).', 'product-quantity-for-woocommerce' ) . '<br>' .
					__( 'Also please note that currently quantity as dropdown will <strong>always use "Max value fallback" option for variable products</strong> and it will <strong>ignore exact (i.e. fixed) quantity</strong> section settings for all products.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_qty_dropdown',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Dropdown label template', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Singular.', 'product-quantity-for-woocommerce' ) . ' ' . $this->message_replaced_values( array( '%qty%' ) ) . ' ' .
					sprintf( __( 'For example try %s', 'product-quantity-for-woocommerce' ),  '<code>%qty% item</code>' ),
				'id'       => 'alg_wc_pq_qty_dropdown_label_template_singular',
				'default'  => '%qty%',
				'type'     => 'text',
			),
			array(
				'desc'     => __( 'Plural.', 'product-quantity-for-woocommerce' ) . ' ' . $this->message_replaced_values( array( '%qty%' ) ) . ' ' .
					sprintf( __( 'For example try %s', 'product-quantity-for-woocommerce' ),  '<code>%qty% items</code>' ),
				'id'       => 'alg_wc_pq_qty_dropdown_label_template_plural',
				'default'  => '%qty%',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Max value fallback', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'Will be used if no maximum quantity is set for the product and always for variable products.', 'product-quantity-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_qty_dropdown_max_value_fallback',
				'default'  => 0,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 0, 'step' => $this->get_qty_step_settings() ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_qty_dropdown_options',
			),
			array(
				'title'    => __( 'Quantity Info Options', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_qty_info_options',
			),
			array(
				'title'    => __( 'Single product page', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_qty_info_on_single_product',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'alg_wc_pq_qty_info_on_single_product_content',
				'desc'     => alg_wc_pq()->core->qty_info_content_desc,
				'default'  => alg_wc_pq()->core->qty_info_default_content,
				'type'     => 'textarea',
				'css'      => 'width:100%;',
				'alg_wc_pq_raw' => true,
			),
			array(
				'title'    => __( 'Archives', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_qty_info_on_loop',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'id'       => 'alg_wc_pq_qty_info_on_loop_content',
				'desc'     => alg_wc_pq()->core->qty_info_content_desc,
				'default'  => alg_wc_pq()->core->qty_info_default_content,
				'type'     => 'textarea',
				'css'      => 'width:100%;',
				'alg_wc_pq_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_qty_info_options',
			),
			array(
				'title'    => __( 'Price by Quantity Options', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Please note that this section will only work for simple type products.', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_qty_price_by_qty_options',
			),
			array(
				'title'    => __( 'Price by quantity', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_qty_price_by_qty_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Template', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'You can use HTML here.', 'product-quantity-for-woocommerce' ),
				'desc'     => sprintf( __( 'Placeholders: %s.', 'product-quantity-for-woocommerce' ),
					'<code>' . implode( '</code>, <code>', array( '%price%', '%qty%' ) ) . '</code>' ),
				'id'       => 'alg_wc_pq_qty_price_by_qty_template',
				'default'  => __( '%price% for %qty% pcs.', 'product-quantity-for-woocommerce' ),
				'type'     => 'textarea',
				'css'      => 'width:100%;',
				'alg_wc_pq_raw' => true,
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_qty_price_by_qty_options',
			),
			array(
				'title'    => __( 'Rounding Options', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_rounding_options',
			),
			array(
				'title'    => __( 'Round on add to cart', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'Makes sense only if "Decimal quantities" option is enabled.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_round_on_add_to_cart',
				'default'  => 'no',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'no'    => __( 'Do not round', 'product-quantity-for-woocommerce' ),
					'round' => __( 'Round', 'product-quantity-for-woocommerce' ),
					'ceil'  => __( 'Round up', 'product-quantity-for-woocommerce' ),
					'floor' => __( 'Round down', 'product-quantity-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Round with JavaScript', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_round_with_js',
				'default'  => 'no',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'no'    => __( 'Do not round', 'product-quantity-for-woocommerce' ),
					'round' => __( 'Round', 'product-quantity-for-woocommerce' ),
					'ceil'  => __( 'Round up', 'product-quantity-for-woocommerce' ),
					'floor' => __( 'Round down', 'product-quantity-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_rounding_options',
			),
		);
		$advanced_settings = array(
			array(
				'title'    => __( 'Advanced Options', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_advanced_options',
			),
			array(
				'title'    => __( 'Force JS check (on change)', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Min/max quantity', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_force_js_check_min_max',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Quantity step', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_force_js_check_step',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Force JS check (periodically)', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Min/max quantity', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_force_js_check_min_max_periodically',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Quantity step', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_force_js_check_step_periodically',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Period (ms)', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_force_js_check_period_ms',
				'default'  => 1000,
				'type'     => 'number',
				'custom_attributes' => array( 'min' => 100 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_advanced_options',
			),
		);
		$admin_settings = array(
			array(
				'title'    => __( 'Admin Options', 'product-quantity-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_pq_admin_options',
			),
			array(
				'title'    => __( 'Admin columns', 'product-quantity-for-woocommerce' ),
				'desc'     => __( 'Enable', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'Will add quantity columns to admin products list.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_admin_columns_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Columns', 'product-quantity-for-woocommerce' ),
				'desc_tip' => __( 'Leave blank to add all available columns.', 'product-quantity-for-woocommerce' ),
				'id'       => 'alg_wc_pq_admin_columns',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => array(
					'alg_wc_pq_min_qty'  => __( 'Min Qty', 'product-quantity-for-woocommerce' ),
					'alg_wc_pq_max_qty'  => __( 'Max Qty', 'product-quantity-for-woocommerce' ),
					'alg_wc_pq_qty_step' => __( 'Qty Step', 'product-quantity-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_pq_admin_options',
			),
		);
		return array_merge( $main_settings, $general_settings, $advanced_settings, $admin_settings );
	}

}

endif;

return new Alg_WC_PQ_Settings_General();
