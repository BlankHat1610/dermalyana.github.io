/**
 * alg-wc-pq-variable.js
 *
 * @version 1.5.0
 * @since   1.0.0
 * @todo    [dev] (maybe) `jQuery('[name=quantity]').val('0')` on `jQuery.isEmptyObject(product_quantities[variation_id])` (i.e. instead of `return`)
 */

function check_qty(){
	var variation_id = jQuery('[name=variation_id]').val();
	if (0 == variation_id) {
		return;
	}
	if (jQuery.isEmptyObject(product_quantities[variation_id])){
		return;
	}
	// Step
	var step = parseFloat(product_quantities[variation_id]['step']);
	if (0 != step) {
		jQuery('[name=quantity]').attr('step',step);
	}
	// Min/max
	var current_qty = jQuery('[name=quantity]').val();
	if (quantities_options['reset_to_min']){
		jQuery('[name=quantity]').val(product_quantities[variation_id]['min_qty']);
	} else if (quantities_options['reset_to_max']){
		jQuery('[name=quantity]').val(product_quantities[variation_id]['max_qty']);
	} else if (current_qty < parseFloat(product_quantities[variation_id]['min_qty'])){
		jQuery('[name=quantity]').val(product_quantities[variation_id]['min_qty']);
	} else if (current_qty > parseFloat(product_quantities[variation_id]['max_qty'])){
		jQuery('[name=quantity]').val(product_quantities[variation_id]['max_qty']);
	}
}

jQuery(document).ready(function(){
	jQuery('[name=variation_id]').on('change',check_qty);
});
