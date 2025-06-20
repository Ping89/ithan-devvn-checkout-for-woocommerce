# ithan-devvn-checkout-for-woocommerce
Trình thay đổi địa chỉ của woocommerce cho phù hợp với Việt Nam

=== ithan devvn checkout for woocommerce ===

Contributors: laptrinhvienso0

Donate link: 

Tags: checkout, fields, woocommerce, custom, payment

Requires at least: 5.2

Tested up to: 6.8

Stable tag: 3.2

Requires PHP: 7.2

License: GPLv2 or later

License URI: https://www.gnu.org/licenses/gpl-2.0.html

Short Description: Allows you to customize address fields for Vietnamese addresses, including provinces, districts, and wards.

== Description ==

This is a custom checkout plugin for WooCommerce. It adds specialized billing address fields for Vietnamese addresses, making it easy to select provinces, districts, and wards.

== Frequently Asked Questions ==

= Where can I find the un-minified source code? =

All of our original, human-readable JavaScript and CSS is included under `assets/src/`. 
We use a build process to generate the files found in `assets/build/`.

= How do I rebuild these files myself? =

1. Make sure you have Node.js and npm installed.
2. From the plugin directory, run `npm install` to fetch all dependencies.
3. Run `npm run minify-js` and `npm run minify-css` to compile/minify the source code into `assets/build/`.
4. The final production-ready files will appear in the `assets/build/` directory.

We also maintain a GitHub repository (link below), where you can view the complete plugin source, including branches, commit history, and build scripts.

== Development & Source Code ==

You can view, fork, or contribute to our plugin’s source code on GitHub:
[https://github.com/Ping89/ithan-devvn-checkout-for-woocommerce](https://github.com/Ping89/ithan-devvn-checkout-for-woocommerce)

== Installation ==

Upload the plugin files to the `/wp-content/plugins/ithandech-devvn-checkout-customizer` directory, or install the plugin through the WordPress plugins screen directly.

= How to Get Started =

Install “woo-checkout-field-editor” (or a similar checkout field editor plugin) and adjust any billing fields as desired.  
To use this plugin effectively, you must include the following three fields:
- `billing_province_code` (select) – Province/City
- `billing_district_code` (select) – District
- `billing_ward_code` (select) – Ward
  
  <img src="./assets/billing_address_editor_changed.png" alt="Ảnh cài đặt thêm địa chỉ thanh toán" width="600">
  
- `shipping_province_code` (select) – Province/City
- `shipping_district_code` (select) – District
- `shipping_ward_code` (select) – Ward
  
  <img src="./assets/shipping_address_editor_changed.png" alt="Ảnh cài đặt thêm địa chỉ thanh toán" width="600">
    
== Screenshots ==

1. Start screen: `/assets/overview.png`
<img src="./assets/overview.png" alt="Ảnh tổng quan" width="600">
2. Label Tooltips: `/assets/labelview.png`
<img src="./assets/labelview.png" alt="Ảnh hiển thị nhãn" width="600">
3. Select VN Address: `/assets/select_vn_address_view.png`
<img src="./assets/select_vn_address_view.png" alt="Ảnh hiển chọn địa chỉ" width="600">
4. Error Message view: `/assets/error_messages.png`
<img src="./assets/error_messages.png" alt="Ảnh hiển thông báo lỗi" width="600">
5. Order Edit Form (Admin): `/assets/order_edit_in_admin.png`
<img src="./assets/order_edit_in_admin.png" alt="Ảnh hiển cập nhật đơn hàng" width="600">

== Changelog ==

= 3.2 =
* Fixed some function/class names for better convention.

= 3.0 =
* Fixed some function/class names for better convention.

= 2.0 =
* Added scripts to build minified JS/CSS via Node.js.
* Changed some function/class names for better convention.

= 1.2 =
* Introduced a new skin.
* Added custom CSS/JS for error display.
* Displayed label tooltips.

= 1.0 =
* Initial plugin creation.

== Upgrade Notice ==

= 1.2 =
No special steps required.

== A brief Markdown ==

Markdown is what the parser uses to process much of the readme file.

[markdown syntax]: https://daringfireball.net/projects/markdown/syntax

Ordered list:

1. Edit billing and shipping addresses to match Vietnamese addresses.
2. Allow selecting provinces, districts, and wards via combo boxes.
3. Display more flexible error messages.

