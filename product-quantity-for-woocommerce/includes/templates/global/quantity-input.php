<?php
/**
 * Product quantity inputs // Drop down by Algoritmika
 *
 * @version 1.6.2
 * @since   1.6.0
 * @todo    [dev] (important) re-check new template in WC 3.6
 * @todo    [dev] (important) dropdown: variable products (#12304) (check "validate on add to cart") (i.e. without fallbacks)
 * @todo    [dev] (important) dropdown: maybe fallback min & step?
 * @todo    [dev] (important) dropdown: "exact (i.e. fixed)" quantity
 *
 * wc_version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( $max_value && $min_value === $max_value ) {
	?>
	<div class="quantity hidden">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
	</div>
	<?php
} elseif ( ( ! empty( $max_value ) || 0 != ( $max_value_fallback = get_option( 'alg_wc_pq_qty_dropdown_max_value_fallback', 0 ) ) ) && ! empty( $step ) ) { // dropdown
	if ( empty( $max_value ) ) {
		$max_value = $max_value_fallback;
	}
	?>
	<div class="quantity">
		<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></label>
		<select
			id="<?php echo esc_attr( $input_id ); ?>"
			class="qty"
			name="<?php echo esc_attr( $input_name ); ?>"
			title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>" >
			<?php
			$values = array();
			for ( $i = $min_value; $i <= $max_value; $i = $i + $step ) {
				$values[] = $i;
			}
			if ( ! empty( $input_value ) && ! in_array( $input_value, $values ) ) {
				$values[] = $input_value;
			}
			asort( $values );
			$label_template_singular = get_option( 'alg_wc_pq_qty_dropdown_label_template_singular', '%qty%' );
			$label_template_plural   = get_option( 'alg_wc_pq_qty_dropdown_label_template_plural',   '%qty%' );
			foreach ( $values as $value ) {
				?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $input_value ); ?>><?php
					echo str_replace( '%qty%', $value, ( $value < 2 ? $label_template_singular : $label_template_plural ) ); ?></option><?php
			}
			?>
		</select>
	</div>
	<?php
} else { // WC default
	/* translators: %s: Quantity. */
	$labelledby = ! empty( $args['product_name'] ) ? sprintf( __( '%s quantity', 'woocommerce' ), strip_tags( $args['product_name'] ) ) : '';
	?>
	<div class="quantity">
		<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></label>
		<input
			type="number"
			id="<?php echo esc_attr( $input_id ); ?>"
			class="input-text qty text"
			step="<?php echo esc_attr( $step ); ?>"
			min="<?php echo esc_attr( $min_value ); ?>"
			max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( $input_value ); ?>"
			title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
			size="4"
			pattern="<?php echo esc_attr( $pattern ); ?>"
			inputmode="<?php echo esc_attr( $inputmode ); ?>"
			aria-labelledby="<?php echo esc_attr( $labelledby ); ?>" />
	</div>
	<?php
}
