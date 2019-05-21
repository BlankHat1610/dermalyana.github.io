<?php
/**
 * Product Quantity for WooCommerce - Core Class
 *
 * @version 1.6.2
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PQ_Core' ) ) :

class Alg_WC_PQ_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.6.2
	 * @since   1.0.0
	 * @todo    [fix] mini-cart number of items for decimal qty
	 * @todo    [dev] (maybe) bundle products (#12493)
	 * @todo    [dev] implement `is_any_section_enabled()`
	 * @todo    [dev] code refactoring: split this into separate files (e.g. `class-alg-wc-pq-checker.php`, `class-alg-wc-pq-scripts.php`)
	 * @todo    [feature] quantity per category (and/or tag) (i.e. not per individual products) (#11792)
	 * @todo    [feature] implement `force_js_check_exact_qty()`
	 * @todo    [feature] add "treat variable as simple" option
	 * @todo    [feature] quantities by user roles (#12680)
	 */
	function __construct() {
		if ( 'yes' === get_option( 'alg_wc_pq_enabled', 'yes' ) ) {
			if (
				'yes' === get_option( 'alg_wc_pq_max_section_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_pq_min_section_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_pq_exact_qty_allowed_section_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_pq_exact_qty_disallowed_section_enabled', 'no' )
			) {
				add_action( 'woocommerce_checkout_process',                                    array( $this, 'check_order_quantities' ) );
				add_action( 'woocommerce_before_cart',                                         array( $this, 'check_order_quantities' ) );
				if ( 'yes' === get_option( 'alg_wc_pq_stop_from_seeing_checkout', 'no' ) ) {
					add_action( 'wp',                                                          array( $this, 'stop_from_seeing_checkout' ), PHP_INT_MAX );
				}
			}
			// Min/max
			if ( 'yes' === get_option( 'alg_wc_pq_max_section_enabled', 'no' ) || 'yes' === get_option( 'alg_wc_pq_min_section_enabled', 'no' ) ) {
				add_filter( 'woocommerce_available_variation',                                 array( $this, 'set_quantity_input_min_max_variation' ), PHP_INT_MAX, 3 );
				if ( 'yes' === get_option( 'alg_wc_pq_min_section_enabled', 'no' ) ) {
					add_filter( 'woocommerce_quantity_input_min',                              array( $this, 'set_quantity_input_min' ), PHP_INT_MAX, 2 );
				}
				if ( 'yes' === get_option( 'alg_wc_pq_max_section_enabled', 'no' ) ) {
					add_filter( 'woocommerce_quantity_input_max',                              array( $this, 'set_quantity_input_max' ), PHP_INT_MAX, 2 );
				}
				// Force on archives
				if ( 'disabled' != ( $this->force_on_loop = get_option( 'alg_wc_pq_force_on_loop', 'disabled' ) ) ) {
					add_filter( 'woocommerce_loop_add_to_cart_args',                           array( $this, 'force_qty_on_loop' ), PHP_INT_MAX, 2 );
				}
			}
			// Step
			if ( 'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ) {
				add_filter( 'woocommerce_quantity_input_step',                                 array( $this, 'set_quantity_input_step' ), PHP_INT_MAX, 2 );
			}
			// Scripts
			add_action( 'wp_enqueue_scripts',                                                  array( $this, 'enqueue_scripts' ) );
			// For cart & for `input_value`
			add_filter( 'woocommerce_quantity_input_args',                                     array( $this, 'set_quantity_input_args' ), PHP_INT_MAX, 2 );
			// Decimal qty
			if ( 'yes' === get_option( 'alg_wc_pq_decimal_quantities_enabled', 'no' ) ) {
				add_action( 'init',                                                            array( $this, 'float_stock_amount' ), PHP_INT_MAX );
			}
			// Styling
			if ( '' != get_option( 'alg_wc_pq_qty_input_style', '' ) ) {
				add_action( 'wp_head',                                                         array( $this, 'style_qty_input' ), PHP_INT_MAX );
			}
			// "Add to cart" validation
			if ( 'notice' === get_option( 'alg_wc_pq_add_to_cart_validation', 'disable' ) ) {
				add_filter( 'woocommerce_add_to_cart_validation',                              array( $this, 'validate_on_add_to_cart' ), PHP_INT_MAX, 3 );
			} elseif ( 'correct' === get_option( 'alg_wc_pq_add_to_cart_validation', 'disable' ) ) {
				add_filter( 'woocommerce_add_to_cart_quantity',                                array( $this, 'correct_on_add_to_cart' ), PHP_INT_MAX, 2 );
			}
			// Qty rounding
			if ( 'no' != ( $this->round_on_add_to_cart = get_option( 'alg_wc_pq_round_on_add_to_cart', 'no' ) ) ) {
				add_filter( 'woocommerce_add_to_cart_quantity',                                array( $this, 'round_on_add_to_cart' ), PHP_INT_MAX, 2 );
			}
			// Dropdown
			if ( 'yes' === get_option( 'alg_wc_pq_qty_dropdown', 'no' ) ) {
				add_filter( 'wc_get_template',                                                 array( $this, 'replace_quantity_input_template' ), PHP_INT_MAX, 5 );
			}
			// Shortcodes
			require_once( 'class-alg-wc-pq-shortcodes.php' );
			// Quantity info on single product page
			if ( 'yes' === get_option( 'alg_wc_pq_qty_info_on_single_product', 'no' ) ) {
				add_action( 'woocommerce_single_product_summary',                              array( $this, 'output_qty_info_on_single_product' ), 31 );
			}
			// Quantity info on archives
			if ( 'yes' === get_option( 'alg_wc_pq_qty_info_on_loop', 'no' ) ) {
				add_action( 'woocommerce_after_shop_loop_item',                                array( $this, 'output_qty_info_on_loop' ), 11 );
			}
			// Admin columns
			require_once( 'class-alg-wc-pq-admin.php' );
			// Price by Qty
			if ( 'yes' === get_option( 'alg_wc_pq_qty_price_by_qty_enabled', 'no' ) ) {
				add_action( 'wp_ajax_'        . 'alg_wc_pq_update_price_by_qty',               array( $this, 'ajax_price_by_qty' ) );
				add_action( 'wp_ajax_nopriv_' . 'alg_wc_pq_update_price_by_qty',               array( $this, 'ajax_price_by_qty' ) );
			}
		}
		// Qty info content data
		$this->qty_info_content_desc = __( 'You can use HTML and/or shortcodes here.', 'product-quantity-for-woocommerce' ) . ' ' .
			sprintf( __( 'Available shortcodes: %s.', 'product-quantity-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
					'[alg_wc_pq_min_product_qty]',
					'[alg_wc_pq_max_product_qty]',
					'[alg_wc_pq_product_qty_step]',
				) ) . '</code>' );
		$this->qty_info_default_content = '<p>' .
				'[alg_wc_pq_min_product_qty before="Minimum quantity is <strong>" after="</strong><br>"]' .
				'[alg_wc_pq_max_product_qty before="Maximum quantity is <strong>" after="</strong><br>"]' .
				'[alg_wc_pq_product_qty_step before="Step is <strong>" after="</strong><br>"]' .
			'</p>';
	}

	/**
	 * round_on_add_to_cart.
	 *
	 * @version 1.6.2
	 * @since   1.6.2
	 * @todo    [feature] (maybe) add `precision` option
	 */
	function round_on_add_to_cart( $quantity, $product_id ) {
		$func = $this->round_on_add_to_cart;
		return $func( $quantity );
	}

	/**
	 * force_qty_on_loop.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function force_qty_on_loop( $args, $product ) {
		$args['quantity'] = ( 'min' === $this->force_on_loop ?
			$this->set_quantity_input_min( $args['quantity'], $product ) : $this->set_quantity_input_max( $args['quantity'], $product ) );
		return $args;
	}

	/**
	 * output_qty_info_on_single_product.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 * @todo    [dev] (important) info: position & priority (same for `loop`)
	 * @todo    [dev] info: variations (same for `loop`)
	 */
	function output_qty_info_on_single_product() {
		echo do_shortcode( get_option( 'alg_wc_pq_qty_info_on_single_product_content', $this->qty_info_default_content ) );
	}

	/**
	 * output_qty_info_on_loop.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function output_qty_info_on_loop() {
		echo do_shortcode( get_option( 'alg_wc_pq_qty_info_on_loop_content', $this->qty_info_default_content ) );
	}

	/**
	 * replace_quantity_input_template.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function replace_quantity_input_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ( 'global/quantity-input.php' === $template_name ) {
			return alg_wc_pq()->plugin_path() . '/includes/templates/global/quantity-input.php';
		}
		return $located;
	}

	/**
	 * get_cart_item_quantities.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_cart_item_quantities( $product_id = 0, $quantity = 0 ) {
		if ( ! isset( WC()->cart ) ) {
			$cart_item_quantities = array();
		} else {
			$cart_item_quantities = WC()->cart->get_cart_item_quantities();
			if ( empty( $cart_item_quantities ) || ! is_array( $cart_item_quantities ) ) {
				$cart_item_quantities = array();
			}
		}
		if ( 0 != $product_id ) {
			if ( ! isset( $cart_item_quantities[ $product_id ] ) ) {
				$cart_item_quantities[ $product_id ] = 0;
			}
			$cart_item_quantities[ $product_id ] += $quantity;
		}
		return $cart_item_quantities;
	}

	/**
	 * correct_on_add_to_cart.
	 *
	 * @version 1.5.0
	 * @since   1.4.0
	 * @todo    [fix] (important) fix "X products have been added to your cart." notice
	 * @todo    [dev] (important) (maybe) "Exact quantities" should be executed first? (same in `validate_on_add_to_cart()`, `stop_from_seeing_checkout()` and `check_order_quantities()`)
	 */
	function correct_on_add_to_cart( $quantity, $product_id ) {
		// Min & Max
		foreach ( array( 'min', 'max' ) as $min_or_max ) {
			if ( 'yes' === get_option( 'alg_wc_pq_' . $min_or_max . '_section_enabled', 'no' ) ) {
				if ( ! isset( $cart_item_quantities ) ) {
					$cart_item_quantities = $this->get_cart_item_quantities( $product_id, $quantity );
					$cart_total_quantity  = array_sum( $cart_item_quantities );
					$cart_item_quantity   = $cart_item_quantities[ $product_id ];
				}
				// Cart total quantity
				if ( ! $this->check_min_max_cart_total_qty( $min_or_max, $cart_total_quantity ) ) {
					return ( $this->get_min_max_cart_total_qty( $min_or_max ) - ( $cart_total_quantity - $quantity ) );
				}
				// Per item quantity
				if ( ! $this->check_product_min_max( $product_id, $min_or_max, $cart_item_quantity ) ) {
					return $this->get_product_qty_min_max( $product_id, 0, $min_or_max );
				}
			}
		}
		// Step
		if ( 'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ) {
			if ( $quantity != ( $fixed_qty = $this->check_product_step( $product_id, $quantity, true ) ) ) {
				return $fixed_qty;
			}
		}
		// Exact quantities
		foreach ( array( 'allowed', 'disallowed' ) as $allowed_or_disallowed ) {
			if ( 'yes' === get_option( 'alg_wc_pq_exact_qty_' . $allowed_or_disallowed . '_section_enabled', 'no' ) ) {
				if ( $quantity != ( $fixed_qty = $this->check_product_exact_qty( $product_id, $allowed_or_disallowed, $quantity, true ) ) ) {
					return $fixed_qty;
				}
			}
		}
		return $quantity;
	}

	/**
	 * validate_on_add_to_cart.
	 *
	 * @version 1.5.0
	 * @since   1.4.0
	 * @todo    [dev] (maybe) separate messages for min/max (i.e. different from "cart" messages)?
	 */
	function validate_on_add_to_cart( $passed, $product_id, $quantity ) {
		// Min & Max
		foreach ( array( 'min', 'max' ) as $min_or_max ) {
			if ( 'yes' === get_option( 'alg_wc_pq_' . $min_or_max . '_section_enabled', 'no' ) ) {
				if ( ! isset( $cart_item_quantities ) ) {
					$cart_item_quantities = $this->get_cart_item_quantities( $product_id, $quantity );
					$cart_total_quantity  = array_sum( $cart_item_quantities );
					$cart_item_quantity   = $cart_item_quantities[ $product_id ];
				}
				// Cart total quantity
				if ( ! $this->check_min_max_cart_total_qty( $min_or_max, $cart_total_quantity ) ) {
					$this->print_message( $min_or_max . '_cart_total_quantity', false, $this->get_min_max_cart_total_qty( $min_or_max ), $cart_total_quantity );
					return false;
				}
				// Per item quantity
				if ( ! $this->check_product_min_max( $product_id, $min_or_max, $cart_item_quantity ) ) {
					$this->print_message( $min_or_max . '_per_item_quantity', false, $this->get_product_qty_min_max( $product_id, 0, $min_or_max ), $cart_item_quantity, $product_id );
					return false;
				}
			}
		}
		// Step
		if ( 'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ) {
			if ( ! $this->check_product_step( $product_id, $quantity ) ) {
				$this->print_message( 'step_quantity', false, $this->get_product_qty_step( $product_id ), $quantity, $product_id );
				return false;
			}
		}
		// Exact quantities
		foreach ( array( 'allowed', 'disallowed' ) as $allowed_or_disallowed ) {
			if ( 'yes' === get_option( 'alg_wc_pq_exact_qty_' . $allowed_or_disallowed . '_section_enabled', 'no' ) ) {
				if ( ! $this->check_product_exact_qty( $product_id, $allowed_or_disallowed, $quantity ) ) {
					$this->print_message( 'exact_qty_' . $allowed_or_disallowed, false, $this->get_product_exact_qty( $product_id, $allowed_or_disallowed ), $quantity, $product_id );
					return false;
				}
			}
		}
		// Passed
		return $passed;
	}

	/**
	 * style_qty_input.
	 *
	 * @version 1.6.0
	 * @since   1.3.0
	 */
	function style_qty_input() {
		echo '<style>' . 'input.qty,select.qty{' . get_option( 'alg_wc_pq_qty_input_style', '' ) . '}</style>';
	}

	/**
	 * float_stock_amount.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function float_stock_amount() {
		remove_filter( 'woocommerce_stock_amount', 'intval' );
		add_filter(    'woocommerce_stock_amount', 'floatval' );
	}

	/**
	 * set_quantity_input_args.
	 *
	 * @version 1.4.0
	 * @since   1.2.0
	 * @todo    [dev] re-check do we really need to set `step` here?
	 */
	function set_quantity_input_args( $args, $product ) {
		if ( 'yes' === get_option( 'alg_wc_pq_min_section_enabled', 'no' ) ) {
			$args['min_value']   = $this->set_quantity_input_min(  $args['min_value'], $product );
		} elseif ( 'yes' === get_option( 'alg_wc_pq_force_cart_min_enabled', 'no' ) ) {
			$args['min_value']   = 1;
		}
		if ( 'yes' === get_option( 'alg_wc_pq_max_section_enabled', 'no' ) ) {
			$args['max_value']   = $this->set_quantity_input_max(  $args['max_value'], $product );
		}
		if ( 'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ) {
			$args['step']        = $this->set_quantity_input_step( $args['step'],      $product );
		}
		if ( 'disabled' != ( $force_on_single = get_option( 'alg_wc_pq_force_on_single', 'disabled' ) ) && is_product() ) {
			$args['input_value'] = ( 'min' === $force_on_single ?
				$this->set_quantity_input_min( $args['min_value'], $product ) : $this->set_quantity_input_max( $args['max_value'], $product ) );
		}
		return $args;
	}

	/**
	 * get_product_qty_step.
	 *
	 * @version 1.6.1
	 * @since   1.1.0
	 */
	function get_product_qty_step( $product_id, $default_step = 0 ) {
		if ( 'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ) {
			if ( 'yes' === apply_filters( 'alg_wc_pq', 'no', 'quantity_step_per_product' ) && 0 != ( $step_per_product = apply_filters( 'alg_wc_pq', 0, 'quantity_step_per_product_value', array( 'product_id' => $product_id ) ) ) ) {
				return apply_filters( 'alg_wc_pq_get_product_qty_step', $step_per_product, $product_id );
			} else {
				return apply_filters( 'alg_wc_pq_get_product_qty_step', ( 0 != ( $step_all_products = get_option( 'alg_wc_pq_step', 0 ) ) ? $step_all_products : $default_step ), $product_id );
			}
		} else {
			return $default_step;
		}
	}

	/**
	 * set_quantity_input_step.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function set_quantity_input_step( $step, $_product ) {
		return $this->get_product_qty_step( $this->get_product_id( $_product ), $step );
	}

	/**
	 * enqueue_scripts.
	 *
	 * @version 1.6.2
	 * @since   1.0.0
	 * @todo    [dev] (important) (maybe) `force_js_check_min_max()` should go *before* the `force_js_check_step()`?
	 * @todo    [feature] `'force_on_add_to_cart' => ( 'yes' === get_option( 'alg_wc_pq_variation_force_on_add_to_cart', 'no' ) )`
	 * @todo    [feature] (maybe) `force_on_add_to_cart` for simple products
	 * @todo    [feature] (maybe) make this optional (for min/max quantities)
	 */
	function enqueue_scripts() {
		// Variable products
		if ( ( $_product = wc_get_product( get_the_ID() ) ) && $_product->is_type( 'variable' ) ) {
			$quantities_options = array(
				'reset_to_min'         => ( 'reset_to_min' === get_option( 'alg_wc_pq_variation_change', 'do_nothing' ) ),
				'reset_to_max'         => ( 'reset_to_max' === get_option( 'alg_wc_pq_variation_change', 'do_nothing' ) ),
			);
			$product_quantities = array();
			foreach ( $_product->get_available_variations() as $variation ) {
				$product_quantities[ $variation['variation_id'] ] = array(
					'min_qty' => $variation['min_qty'],
					'max_qty' => $variation['max_qty'],
					'step'    => $this->get_product_qty_step( $variation['variation_id'], 1 ),
				);
			}
			wp_enqueue_script(  'alg-wc-pq-variable', trailingslashit( alg_wc_pq()->plugin_url() ) . 'includes/js/alg-wc-pq-variable.js', array( 'jquery' ), alg_wc_pq()->version, true );
			wp_localize_script( 'alg-wc-pq-variable', 'product_quantities', $product_quantities );
			wp_localize_script( 'alg-wc-pq-variable', 'quantities_options', $quantities_options );
		}
		// Price by qty
		if ( ( $_product = wc_get_product( get_the_ID() ) ) && $_product->is_type( 'simple' ) && ( 'yes' === get_option( 'alg_wc_pq_qty_price_by_qty_enabled', 'no' ) ) ) {
			wp_enqueue_script(  'alg-wc-pq-price-by-qty', trailingslashit( alg_wc_pq()->plugin_url() ) . 'includes/js/alg-wc-pq-price-by-qty.js', array( 'jquery' ), alg_wc_pq()->version, true );
			wp_localize_script( 'alg-wc-pq-price-by-qty', 'alg_wc_pq_update_price_by_qty_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'product_id' => get_the_ID() ) );
		}
		// Force JS step check
		if ( 'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ) {
			$force_check_step_periodically = ( 'yes' === get_option( 'alg_wc_pq_force_js_check_step_periodically', 'no' ) );
			$force_check_step_on_change    = ( 'yes' === get_option( 'alg_wc_pq_force_js_check_step', 'no' ) );
			if ( $force_check_step_periodically || $force_check_step_on_change ) {
				wp_enqueue_script(  'alg-wc-pq-force-step-check', trailingslashit( alg_wc_pq()->plugin_url() ) . 'includes/js/alg-wc-pq-force-step-check.js', array( 'jquery' ), alg_wc_pq()->version, true );
				wp_localize_script( 'alg-wc-pq-force-step-check', 'force_step_check_options', array(
					'force_check_step_periodically'    => $force_check_step_periodically,
					'force_check_step_on_change'       => $force_check_step_on_change,
					'force_check_step_periodically_ms' => get_option( 'alg_wc_pq_force_js_check_period_ms', 1000 ),
				) );
			}
		}
		// Force JS min/max check
		if ( 'yes' === get_option( 'alg_wc_pq_max_section_enabled', 'no' ) || 'yes' === get_option( 'alg_wc_pq_min_section_enabled', 'no' ) ) {
			$force_check_min_max_periodically = ( 'yes' === get_option( 'alg_wc_pq_force_js_check_min_max_periodically', 'no' ) );
			$force_check_min_max_on_change    = ( 'yes' === get_option( 'alg_wc_pq_force_js_check_min_max', 'no' ) );
			if ( $force_check_min_max_periodically || $force_check_min_max_on_change ) {
				wp_enqueue_script(  'alg-wc-pq-force-min-max-check', trailingslashit( alg_wc_pq()->plugin_url() ) . 'includes/js/alg-wc-pq-force-min-max-check.js', array( 'jquery' ), alg_wc_pq()->version, true );
				wp_localize_script( 'alg-wc-pq-force-min-max-check', 'force_min_max_check_options', array(
					'force_check_min_max_periodically'    => $force_check_min_max_periodically,
					'force_check_min_max_on_change'       => $force_check_min_max_on_change,
					'force_check_min_max_periodically_ms' => get_option( 'alg_wc_pq_force_js_check_period_ms', 1000 ),
				) );
			}
		}
		// Qty rounding
		if ( 'no' != ( $round_with_js_func = get_option( 'alg_wc_pq_round_with_js', 'no' ) ) ) {
			wp_enqueue_script(  'alg-wc-pq-force-rounding', trailingslashit( alg_wc_pq()->plugin_url() ) . 'includes/js/alg-wc-pq-force-rounding.js', array( 'jquery' ), alg_wc_pq()->version, true );
			wp_localize_script( 'alg-wc-pq-force-rounding', 'force_rounding_options', array(
				'round_with_js_func' => $round_with_js_func,
			) );
		}
	}

	/**
	 * ajax_price_by_qty.
	 *
	 * @version 1.6.1
	 * @since   1.6.1
	 * @todo    [dev] non-simple products (i.e. variable, grouped etc.)
	 * @todo    [dev] customizable position (instead of the price; after the price, before the price etc.) (#12668) (NB: maybe do not display for qty=1)
	 * @todo    [dev] add option to disable "price by qty" on initial screen (i.e. before qty input was changed)
	 * @todo    [dev] (maybe) add sale price
	 * @todo    [dev] (maybe) add optional "in progress" message (for slow servers)
	 */
	function ajax_price_by_qty( $param ) {
		if ( isset( $_POST['alg_wc_pq_qty'] ) && '' !== $_POST['alg_wc_pq_qty'] && ! empty( $_POST['alg_wc_pq_id'] ) ) {
			$placeholders = array(
				'%price%'   => wc_price( wc_get_price_to_display( wc_get_product( $_POST['alg_wc_pq_id'] ), array( 'qty' => $_POST['alg_wc_pq_qty'] ) ) ),
				'%qty%'     => $_POST['alg_wc_pq_qty'],
			);
			echo str_replace( array_keys( $placeholders ), $placeholders,
				get_option( 'alg_wc_pq_qty_price_by_qty_template', __( '%price% for %qty% pcs.', 'product-quantity-for-woocommerce' ) ) );
		}
		die();
	}

	/**
	 * get_product_id.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_product_id( $_product ) {
		if ( ! isset( $this->is_wc_version_below_3 ) ) {
			$this->is_wc_version_below_3 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );
		}
		if ( ! $_product || ! is_object( $_product ) ) {
			return 0;
		}
		if ( $this->is_wc_version_below_3 ) {
			return ( isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->id;
		} else {
			return $_product->get_id();
		}
	}

	/**
	 * get_product_qty_min_max.
	 *
	 * @version 1.6.1
	 * @since   1.0.0
	 */
	function get_product_qty_min_max( $product_id, $default, $min_or_max ) {
		if ( 'yes' === get_option( 'alg_wc_pq_' . $min_or_max . '_section_enabled', 'no' ) ) {
			// Check if "Sold individually" is enabled for the product
			$product = wc_get_product( $product_id );
			if ( $product && $product->is_sold_individually() ) {
				return $default;
			}
			// Per product
			if ( 'yes' === apply_filters( 'alg_wc_pq', 'no', 'per_item_quantity_per_product', array( 'min_or_max' => $min_or_max ) ) ) {
				if ( 0 != ( $value = apply_filters( 'alg_wc_pq', 'no', 'per_item_quantity_per_product_value', array( 'min_or_max' => $min_or_max, 'product_id' => $product_id ) ) ) ) {
					return apply_filters( 'alg_wc_pq_get_product_qty_' . $min_or_max, $value, $product_id );
				}
			}
			// All products
			if ( 0 != ( $value = get_option( 'alg_wc_pq_' . $min_or_max . '_per_item_quantity', 0 ) ) ) {
				return apply_filters( 'alg_wc_pq_get_product_qty_' . $min_or_max, $value, $product_id );
			}
		}
		return $default;
	}

	/**
	 * set_quantity_input_min_max_variation.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function set_quantity_input_min_max_variation( $args, $_product, $_variation ) {
		$variation_id = $this->get_product_id( $_variation );
		$args['min_qty'] = $this->get_product_qty_min_max( $variation_id, $args['min_qty'], 'min' );
		$args['max_qty'] = $this->get_product_qty_min_max( $variation_id, $args['max_qty'], 'max' );
		$_max = $_variation->get_max_purchase_quantity();
		if ( -1 != $_max && $args['min_qty'] > $_max ) {
			$args['min_qty'] = $_max;
		}
		if ( -1 != $_max && $args['max_qty'] > $_max ) {
			$args['max_qty'] = $_max;
		}
		if ( $args['min_qty'] < 0 ) {
			$args['min_qty'] = '';
		}
		if ( $args['max_qty'] < 0 ) {
			$args['max_qty'] = '';
		}
		return $args;
	}

	/**
	 * set_quantity_input_min_or_max.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 * @todo    [dev] (important) rename this (and probably some other `set_...()` functions)
	 */
	function set_quantity_input_min_or_max( $qty, $_product, $min_or_max ) {
		if ( ! $_product->is_type( 'variable' ) ) {
			$value = $this->get_product_qty_min_max( $this->get_product_id( $_product ), $qty, $min_or_max );
			$_max  = $_product->get_max_purchase_quantity();
			return ( -1 == $_max || $value < $_max ? $value : $_max );
		} else {
			return $qty;
		}
	}

	/**
	 * set_quantity_input_min.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 */
	function set_quantity_input_min( $qty, $_product ) {
		return $this->set_quantity_input_min_or_max( $qty, $_product, 'min' );
	}

	/**
	 * set_quantity_input_max.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 */
	function set_quantity_input_max( $qty, $_product ) {
		return $this->set_quantity_input_min_or_max( $qty, $_product, 'max' );
	}

	/**
	 * stop_from_seeing_checkout.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 */
	function stop_from_seeing_checkout() {
		if ( ! isset( WC()->cart ) ) {
			return;
		}
		if ( ! is_checkout() ) {
			return;
		}
		$cart_item_quantities = WC()->cart->get_cart_item_quantities();
		if ( empty( $cart_item_quantities ) || ! is_array( $cart_item_quantities ) ) {
			return;
		}
		$cart_total_quantity = array_sum( $cart_item_quantities );
		// Max quantity
		if ( 'yes' === get_option( 'alg_wc_pq_max_section_enabled', 'no' ) ) {
			if ( ! $this->check_min_max( 'max', $cart_item_quantities, $cart_total_quantity, false, true ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}
		// Min quantity
		if ( 'yes' === get_option( 'alg_wc_pq_min_section_enabled', 'no' ) ) {
			if ( ! $this->check_min_max( 'min', $cart_item_quantities, $cart_total_quantity, false, true ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}
		// Step
		if ( 'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ) {
			if ( ! $this->check_step( $cart_item_quantities, false, true ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}
		// Exact quantities
		foreach ( array( 'allowed', 'disallowed' ) as $allowed_or_disallowed ) {
			if ( 'yes' === get_option( 'alg_wc_pq_exact_qty_' . $allowed_or_disallowed . '_section_enabled', 'no' ) ) {
				if ( ! $this->check_exact_qty( $allowed_or_disallowed, $cart_item_quantities, false, true ) ) {
					wp_safe_redirect( wc_get_cart_url() );
					exit;
				}
			}
		}
	}

	/**
	 * check_order_quantities.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 * @todo    [dev] code refactoring min/max (same in `stop_from_seeing_checkout()`)
	 */
	function check_order_quantities() {
		if ( ! isset( WC()->cart ) ) {
			return;
		}
		$cart_item_quantities = WC()->cart->get_cart_item_quantities();
		if ( empty( $cart_item_quantities ) || ! is_array( $cart_item_quantities ) ) {
			return;
		}
		$cart_total_quantity = array_sum( $cart_item_quantities );
		$_is_cart = is_cart();
		// Max quantity
		if ( 'yes' === get_option( 'alg_wc_pq_max_section_enabled', 'no' ) ) {
			$this->check_min_max( 'max', $cart_item_quantities, $cart_total_quantity, $_is_cart, false );
		}
		// Min quantity
		if ( 'yes' === get_option( 'alg_wc_pq_min_section_enabled', 'no' ) ) {
			$this->check_min_max( 'min', $cart_item_quantities, $cart_total_quantity, $_is_cart, false );
		}
		// Step
		if ( 'yes' === get_option( 'alg_wc_pq_step_section_enabled', 'no' ) ) {
			$this->check_step( $cart_item_quantities, $_is_cart, false );
		}
		// Exact quantities
		foreach ( array( 'allowed', 'disallowed' ) as $allowed_or_disallowed ) {
			if ( 'yes' === get_option( 'alg_wc_pq_exact_qty_' . $allowed_or_disallowed . '_section_enabled', 'no' ) ) {
				$this->check_exact_qty( $allowed_or_disallowed, $cart_item_quantities, $_is_cart, false );
			}
		}
	}

	/**
	 * print_message.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 * @todo    [dev] step: more replaced values in message (e.g. `%lower_valid_quantity%`, `%higher_valid_quantity%` )
	 */
	function print_message( $message_type, $_is_cart, $required_quantity, $total_quantity, $_product_id = 0 ) {
		if ( $_is_cart ) {
			if ( 'no' === get_option( 'alg_wc_pq_cart_notice_enabled', 'no' ) ) {
				return;
			}
		}
		switch ( $message_type ) {
			case 'max_cart_total_quantity':
				$replaced_values = array(
					'%max_cart_total_quantity%' => $required_quantity,
					'%cart_total_quantity%'     => $total_quantity,
				);
				$message_template = get_option( 'alg_wc_pq_max_cart_total_message',
					__( 'Maximum allowed order quantity is %max_cart_total_quantity%. Your current order quantity is %cart_total_quantity%.', 'product-quantity-for-woocommerce' ) );
				break;
			case 'min_cart_total_quantity':
				$replaced_values = array(
					'%min_cart_total_quantity%' => $required_quantity,
					'%cart_total_quantity%'     => $total_quantity,
				);
				$message_template = get_option( 'alg_wc_pq_min_cart_total_message',
					__( 'Minimum allowed order quantity is %min_cart_total_quantity%. Your current order quantity is %cart_total_quantity%.', 'product-quantity-for-woocommerce' ) );
				break;
			case 'max_per_item_quantity':
				$_product = wc_get_product( $_product_id );
				$replaced_values = array(
					'%max_per_item_quantity%' => $required_quantity,
					'%item_quantity%'         => $total_quantity,
					'%product_title%'         => $_product->get_title(),
				);
				$message_template = get_option( 'alg_wc_pq_max_per_item_message',
					__( 'Maximum allowed quantity for %product_title% is %max_per_item_quantity%. Your current item quantity is %item_quantity%.', 'product-quantity-for-woocommerce' ) );
				break;
			case 'min_per_item_quantity':
				$_product = wc_get_product( $_product_id );
				$replaced_values = array(
					'%min_per_item_quantity%' => $required_quantity,
					'%item_quantity%'         => $total_quantity,
					'%product_title%'         => $_product->get_title(),
				);
				$message_template = get_option( 'alg_wc_pq_min_per_item_message',
					__( 'Minimum allowed quantity for %product_title% is %min_per_item_quantity%. Your current item quantity is %item_quantity%.', 'product-quantity-for-woocommerce' ) );
				break;
			case 'step_quantity':
				$_product = wc_get_product( $_product_id );
				$replaced_values = array(
					'%quantity_step%'         => $required_quantity,
					'%quantity%'              => $total_quantity,
					'%product_title%'         => $_product->get_title(),
				);
				$message_template = get_option( 'alg_wc_pq_step_message',
					__( 'Quantity step for %product_title% is %quantity_step%. Your current quantity is %quantity%.', 'product-quantity-for-woocommerce' ) );
				break;
			case 'exact_qty_allowed':
				$_product = wc_get_product( $_product_id );
				$replaced_values = array(
					'%allowed_quantity%'      => implode( ', ', array_map( 'trim', explode( ',', $required_quantity ) ) ),
					'%quantity%'              => $total_quantity,
					'%product_title%'         => $_product->get_title(),
				);
				$message_template = get_option( 'alg_wc_pq_exact_qty_allowed_message',
					__( 'Allowed quantity for %product_title% is %allowed_quantity%. Your current quantity is %quantity%.', 'product-quantity-for-woocommerce' ) );
				break;
			case 'exact_qty_disallowed':
				$_product = wc_get_product( $_product_id );
				$replaced_values = array(
					'%disallowed_quantity%'   => implode( ', ', array_map( 'trim', explode( ',', $required_quantity ) ) ),
					'%quantity%'              => $total_quantity,
					'%product_title%'         => $_product->get_title(),
				);
				$message_template = get_option( 'alg_wc_pq_exact_qty_disallowed_message',
					__( 'Disallowed quantity for %product_title% is %disallowed_quantity%. Your current quantity is %quantity%.', 'product-quantity-for-woocommerce' ) );
				break;
		}
		$_notice = str_replace( array_keys( $replaced_values ), array_values( $replaced_values ), $message_template );
		if ( $_is_cart ) {
			wc_print_notice( $_notice, 'notice' );
		} else {
			wc_add_notice( $_notice, 'error' );
		}
	}

	/**
	 * get_min_max_cart_total_qty.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_min_max_cart_total_qty( $min_or_max ) {
		return get_option( 'alg_wc_pq_' . $min_or_max . '_cart_total_quantity', 0 );
	}

	/**
	 * check_min_max_cart_total_qty.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function check_min_max_cart_total_qty( $min_or_max, $cart_total_quantity ) {
		if ( 0 != ( $min_or_max_cart_total_quantity = $this->get_min_max_cart_total_qty( $min_or_max ) ) ) {
			if (
				( 'max' === $min_or_max && $cart_total_quantity > $min_or_max_cart_total_quantity ) ||
				( 'min' === $min_or_max && $cart_total_quantity < $min_or_max_cart_total_quantity )
			) {
				return false;
			}
		}
		return true;
	}

	/**
	 * check_product_min_max.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function check_product_min_max( $product_id, $min_or_max, $quantity ) {
		if ( 0 != ( $product_min_max = $this->get_product_qty_min_max( $product_id, 0, $min_or_max ) ) ) {
			if (
				( 'max' === $min_or_max && $quantity > $product_min_max ) ||
				( 'min' === $min_or_max && $quantity < $product_min_max )
			) {
				return false;
			}
		}
		return true;
	}

	/**
	 * check_min_max.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function check_min_max( $min_or_max, $cart_item_quantities, $cart_total_quantity, $_is_cart, $_return ) {
		// Cart total quantity
		if ( ! $this->check_min_max_cart_total_qty( $min_or_max, $cart_total_quantity ) ) {
			if ( $_return ) {
				return false;
			} else {
				$this->print_message( $min_or_max . '_cart_total_quantity', $_is_cart, $this->get_min_max_cart_total_qty( $min_or_max ), $cart_total_quantity );
			}
		}
		// Per item quantity
		foreach ( $cart_item_quantities as $product_id => $cart_item_quantity ) {
			if ( ! $this->check_product_min_max( $product_id, $min_or_max, $cart_item_quantity ) ) {
				if ( $_return ) {
					return false;
				} else {
					$this->print_message( $min_or_max . '_per_item_quantity', $_is_cart, $this->get_product_qty_min_max( $product_id, 0, $min_or_max ), $cart_item_quantity, $product_id );
				}
			}
		}
		// Passed
		if ( $_return ) {
			return true;
		}
	}

	/**
	 * get_product_exact_qty.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 * @todo    [feature] (maybe) total qty of item in cart
	 * @todo    [feature] (maybe) total items in cart
	 */
	function get_product_exact_qty( $product_id, $allowed_or_disallowed, $default_exact_qty = '' ) {
		if ( 'yes' === get_option( 'alg_wc_pq_exact_qty_' . $allowed_or_disallowed . '_section_enabled', 'no' ) ) {
			if (
				'yes' === apply_filters( 'alg_wc_pq', 'no', 'exact_qty_per_product', array( 'allowed_or_disallowed' => $allowed_or_disallowed ) ) &&
				'' !== ( $exact_qty_per_product = apply_filters( 'alg_wc_pq', '', 'exact_qty_per_product_value', array( 'product_id' => $product_id, 'allowed_or_disallowed' => $allowed_or_disallowed ) ) )
			) {
				return $exact_qty_per_product;
			} else {
				return ( '' !== ( $exact_qty_all_products = get_option( 'alg_wc_pq_exact_qty_' . $allowed_or_disallowed, '' ) ) ? $exact_qty_all_products : $default_exact_qty );
			}
		} else {
			return $default_exact_qty;
		}
	}

	/**
	 * check_product_exact_qty.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 * @todo    [dev] (important) rethink qty correction on `disallowed`
	 * @todo    [dev] (important) check if all `$product_exact_qty` elements are `is_numeric()`
	 * @todo    [dev] (important) check if possible float and int comparison works properly in `abs( $quantity - $closest ) > abs( $item - $quantity )`
	 * @todo    [feature] allow/disallow quantities: power of 2
	 * @todo    [feature] (maybe) allow/disallow quantities: power of custom value
	 */
	function check_product_exact_qty( $product_id, $allowed_or_disallowed, $quantity, $do_fix = false ) {
		$product_exact_qty = $this->get_product_exact_qty( $product_id, $allowed_or_disallowed );
		if ( '' != $product_exact_qty ) {
			$product_exact_qty = array_map( 'trim', explode( ',', $product_exact_qty ) );
			sort( $product_exact_qty );
			$is_valid = ( 'allowed' === $allowed_or_disallowed ? in_array( $quantity, $product_exact_qty ) : ! in_array( $quantity, $product_exact_qty ) );
			if ( ! $do_fix ) {
				return $is_valid;
			} elseif ( ! $is_valid ) {
				if ( 'allowed' === $allowed_or_disallowed ) {
					$closest = null;
					foreach ( $product_exact_qty as $item ) {
						if ( $closest === null || abs( $quantity - $closest ) > abs( $item - $quantity ) ) {
							$closest = $item;
						}
					}
					return ( null !== $closest ? $closest : $quantity );
				} else { // 'disallowed'
					while ( true ) {
						$quantity++;
						if ( ! in_array( $quantity, $product_exact_qty ) ) {
							return $quantity;
						}
					}
				}
			}
		}
		return ( ! $do_fix ? true : $quantity );
	}

	/**
	 * check_exact_qty.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function check_exact_qty( $allowed_or_disallowed, $cart_item_quantities, $_is_cart, $_return ) {
		foreach ( $cart_item_quantities as $product_id => $cart_item_quantity ) {
			if ( ! $this->check_product_exact_qty( $product_id, $allowed_or_disallowed, $cart_item_quantity ) ) {
				if ( $_return ) {
					return false;
				} else {
					$this->print_message( 'exact_qty_' . $allowed_or_disallowed, $_is_cart, $this->get_product_exact_qty( $product_id, $allowed_or_disallowed ), $cart_item_quantity, $product_id );
				}
			}
		}
		// Passed
		if ( $_return ) {
			return true;
		}
	}

	/**
	 * check_product_step.
	 *
	 * @version 1.4.1
	 * @since   1.4.0
	 * @todo    [dev] `$multiplier` should be calculated automatically according to the `$qty_step_settings` value (same in `force_js_check_step()`)
	 */
	function check_product_step( $product_id, $quantity, $do_fix = false ) {
		$product_qty_step = $this->get_product_qty_step( $product_id );
		if ( 0 != $product_qty_step ) {
			$min_value = $this->get_product_qty_min_max( $product_id, 0, 'min' );
			if ( 'yes' === get_option( 'alg_wc_pq_decimal_quantities_enabled', 'no' ) ) {
				$multiplier         = floatval( 1000000 );
				$_min_value         = intval( round( floatval( $min_value )        * $multiplier ) );
				$_quantity          = intval( round( floatval( $quantity )         * $multiplier ) );
				$_product_qty_step  = intval( round( floatval( $product_qty_step ) * $multiplier ) );
			} else {
				$_min_value         = $min_value;
				$_quantity          = $quantity;
				$_product_qty_step  = $product_qty_step;
			}
			$_quantity = $_quantity - $_min_value;
			$_reminder = $_quantity % $_product_qty_step;
			$is_valid  = ( 0 == $_reminder );
			if ( ! $do_fix ) {
				return $is_valid;
			} elseif ( ! $is_valid ) {
				$quantity = $_quantity + ( $_reminder * 2 >= $_product_qty_step ? $_product_qty_step : 0 ) - $_reminder + $_min_value;
				if ( 'yes' === get_option( 'alg_wc_pq_decimal_quantities_enabled', 'no' ) ) {
					$quantity = $quantity / $multiplier;
				}
				return $quantity;
			}
		}
		return ( ! $do_fix ? true : $quantity );
	}

	/**
	 * check_step.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 * @todo    [dev] (maybe) force `min` in cart to `1` (as it may be zero now)
	 */
	function check_step( $cart_item_quantities, $_is_cart, $_return ) {
		foreach ( $cart_item_quantities as $product_id => $cart_item_quantity ) {
			if ( ! $this->check_product_step( $product_id, $cart_item_quantity ) ) {
				if ( $_return ) {
					return false;
				} else {
					$this->print_message( 'step_quantity', $_is_cart, $this->get_product_qty_step( $product_id ), $cart_item_quantity, $product_id );
				}
			}
		}
		// Passed
		if ( $_return ) {
			return true;
		}
	}

}

endif;

return new Alg_WC_PQ_Core();
