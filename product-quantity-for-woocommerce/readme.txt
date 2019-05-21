=== Product Quantity for WooCommerce ===
Contributors: algoritmika, anbinder
Tags: woocommerce, woo commerce, product, quantity
Requires at least: 4.4
Tested up to: 5.2
Stable tag: 1.6.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Manage product quantity in WooCommerce, beautifully.

== Description ==

**Product Quantity for WooCommerce** plugin lets you take full control of product order quantities in WooCommerce.

= Main Features =

* set **minimum** products order quantities (**cart total** quantity or **per item** quantity),
* set **maximum** products order quantities (**cart total** quantity or **per item** quantity),
* set product **quantity step**,
* enable **decimal quantities** in WooCommerce,
* replace standard WooCommerce quantity number input with **dropdown**,
* set **exact (i.e. fixed) allowed or disallowed quantities** (as comma separated list).

= More Features =

* **customize messages** your customer sees,
* enable/disable **cart notices**,
* enable/disable **add to cart quantity validation and correction**,
* optionally stop customer from **reaching the checkout page** on wrong quantities,
* add product **quantity info** on single product and/or archive pages,
* display **price by quantity** in real-time,
* **force initial quantity on single product and/or archive pages** to either min or max quantity,
* set quantity **input style**,
* display quantity **admin columns** in products list.

= Premium Version =

[Pro version](https://wpfactory.com/item/product-quantity-for-woocommerce/) also allows you to set minimum, maximum quantities, quantity step and exact allowed/disallowed quantities options on **per product basis** (i.e. different for each product).

= Feedback =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Visit the [Product Quantity for WooCommerce plugin page](https://wpfactory.com/item/product-quantity-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Product Quantity".

== Changelog ==

= 1.6.3 - 15/05/2019 =
* Dev - Fallback method added for checking if WooCommerce plugin is active (fixes the issue in case if WooCommerce is installed not in the default `woocommerce` directory).

= 1.6.2 - 14/05/2019 =
* Dev - "Rounding Options" options added.
* Dev - Quantity Dropdown - "Max value fallback" option added (and dropdown can now also be enabled for *variable* products).
* Dev - Advanced Options - Force JS check (periodically) - "Period (ms)" option added.
* Dev - Price by Quantity - `change` event added (e.g. fixes the issue with plus/minus quantity buttons in "OceanWP" theme).
* Dev - Code refactoring (`alg-wc-pq-force-step-check.js` and `alg-wc-pq-force-min-max-check.js` files added).
* Tested up to: 5.2.

= 1.6.1 - 04/05/2019 =
* Fix - Returning default min/max quantity for products with "Sold individually" option enabled.
* Dev - "Price by Quantity" options added.
* Dev - Admin Options - "Admin columns" options added.
* Dev - `alg_wc_pq_get_product_qty_step`, `alg_wc_pq_get_product_qty_min`, `alg_wc_pq_get_product_qty_max` filters added.
* WC tested up to: 3.6.

= 1.6.0 - 12/04/2019 =
* Fix - Variable products - Reset step on variation change fixed.
* Dev - "Quantity Info" options added.
* Dev - "Quantity Dropdown" options added.
* Dev - General Options - "Force initial quantity on archives" option added.
* Dev - Code refactoring.
* Dev - "Exact Quantity" renamed to "Exact (i.e. Fixed) Quantity".
* Dev - Settings split into sections ("General", "Minimum Quantity", "Maximum Quantity", "Quantity Step", "Fixed Quantity").

= 1.5.0 - 31/01/2019 =
* Fix - Stop customer from reaching the checkout page - "WC_Cart::get_cart_url is deprecated..." message fixed.
* Dev - "Exact Quantity" section added.
* Dev - General Options - "On variation change (variable products)" option added.
* Dev - Code refactoring (`alg-wc-pq-variable.js` etc.).

= 1.4.1 - 17/01/2019 =
* Fix - Step check - Min quantity default value changed to `0` (was `1`).
* Fix - Admin settings - Per product meta boxes - Step option fixed; checking if max/min sections are enabled.
* Fix - Force minimum quantity - Description fixed.

= 1.4.0 - 14/01/2019 =
* Dev - "Force JS check" options enabled for decimal quantities.
* Dev - "Add to cart validation" option added.
* Dev - "Quantity step message" option added.
* Dev - "Force cart items minimum quantity" option added.
* Dev - Force JS check - Quantity step - Now value is changed to *nearest* correct value (instead of always *higher* correct value).
* Dev - Code refactoring.
* Dev - Admin settings restyled and descriptions updated.

= 1.3.0 - 28/12/2018 =
* Dev - "Decimal quantities" option added.
* Dev - "Force initial quantity on single product page" option added.
* Dev - "Quantity input style" option added.
* Dev - Minor admin settings restyling.
* Dev - Code refactoring.

= 1.2.1 - 23/10/2018 =
* Dev - Min/max "Per item quantity" (for all products) moved to free version.
* Dev - Admin settings descriptions updated.

= 1.2.0 - 18/10/2018 =
* Fix - Cart min/max quantities fixed.
* Dev - Advanced Options - "Force JS check" options added.
* Dev - Raw input is now allowed in all "Message" admin options.
* Dev - Code refactoring.
* Dev - Minor admin settings restyling.
* Dev - Plugin URI updated.

= 1.1.0 - 09/11/2017 =
* Fix - Core - Checking if max/min section is enabled, when calculating product's max/min quantity.
* Fix - Admin settings - Per product meta boxes - Checking if max/min section is enabled (not only "Per item quantity on per product basis" checkbox).
* Fix - Core - Maximum order quantity - Upper limit bug fixed (when `get_max_purchase_quantity()` equals `-1`).
* Dev - Core - Minimum order quantity - Upper limit (`get_max_purchase_quantity()`) added.
* Dev - "Quantity Step" section added.

= 1.0.0 - 08/11/2017 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
