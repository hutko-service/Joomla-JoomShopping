<?php
/**
 * @package     JoomShopping
 * @subpackage  Payment.pm_hutko
 *
 * @copyright   (C) 2026 Hutko Service
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

define('ADMIN_CFG_HUTKO_EMBEDDED_CHECKOUT', 'Use embedded checkout');
define('ADMIN_CFG_HUTKO_MERCHANT_ID', 'Merchant ID');
define('ADMIN_CFG_HUTKO_MERCHANT_ID_DESCRIPTION', 'Unique merchant identifier in the hutko account.');
define('ADMIN_CFG_HUTKO_SECRET_KEY', 'Secret key');
define('ADMIN_CFG_HUTKO_SECRET_KEY_DESCRIPTION', 'Secret key used to sign and validate hutko requests.');
define('ADMIN_CFG_HUTKO_CURRENCY', 'Currency');
define('ADMIN_CFG_HUTKO_CURRENCY_DESCRIPTION', 'Leave empty to use the order currency.');

define('HUTKO_UNKNOWN_ERROR', 'An error has occurred during payment. Please contact us to confirm your order status.');
define('HUTKO_MERCHANT_DATA_ERROR', 'The payment could not be validated because the merchant data is incorrect.');
define('HUTKO_ORDER_DECLINED', 'Thank you for your order. The payment transaction was declined.');
define('HUTKO_SIGNATURE_ERROR', 'The payment response signature is invalid.');
define('HUTKO_CHECKOUT_INIT_ERROR', 'The hutko checkout could not be initialized. Please try again later or choose another payment method.');
define('HUTKO_ORDER_APPROVED', 'hutko payment successful. Payment ID:');

define('_JSHOP_REDIRECT_TO_PAYMENT_PAGE', 'Redirecting to the payment page');
define('HUTKO_PAY', 'Pay');
define('_JSHOP_TRANSACTION_END', 'Transaction end');
define('_JSHOP_TRANSACTION_FAILED', 'Transaction failed');
