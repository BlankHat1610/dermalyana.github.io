/**
 * alg-wc-pq-price-by-qty.js
 *
 * @version 1.6.2
 * @since   1.6.1
 */

function alg_wc_pq_update_price_by_qty( e, qty = null ) {
	var data = {
		'action'        : 'alg_wc_pq_update_price_by_qty',
		'alg_wc_pq_qty' : ( null !== qty ? qty : jQuery( this ).val() ),
		'alg_wc_pq_id'  : alg_wc_pq_update_price_by_qty_object.product_id,
	};
	jQuery.ajax( {
		type   : 'POST',
		url    : alg_wc_pq_update_price_by_qty_object.ajax_url,
		data   : data,
		success: function( response ) {
			jQuery( 'p.price' ).html( response );
		},
	} );
}

jQuery( document ).ready( function() {
	alg_wc_pq_update_price_by_qty( false, jQuery( '[name="quantity"]' ).val() );
	jQuery( '[name="quantity"]' ).on( 'input change', alg_wc_pq_update_price_by_qty );
} );
