<?php
/*
 * @version      1.2.0
 * @author       DM
 * @package      Jshopping
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
//direct access protection
defined('_JEXEC') or die();
define ('ADMIN_CFG_HUTKO_hutko_redirect', 'Enable no redirect mode');
define ('ADMIN_CFG_HUTKO_hutko_popup', 'Enable pop up mode');
define ('ADMIN_CFG_HUTKO_hutko_styles', 'Customize you payment form');
define ('ADMIN_CFG_HUTKO_MERCHANT_ID', 'Merchant ID');
define ('ADMIN_CFG_HUTKO_MERCHANT_ID_DESCRIPTION', "Unique id of the store in hutko system. You can find it in your hutko.org.");
define ('ADMIN_CFG_HUTKO_SECRET_KEY', 'Secret key');
define ('ADMIN_CFG_HUTKO_SECRET_KEY_DESCRIPTION', 'Custom character set is used to sign messages are forwarded.');
define ('ADMIN_CFG_HUTKO_PAYMODE', 'Payment method');
define ('ADMIN_CFG_HUTKO_CURRENCY_DESCRIPTION', 'Merchant currency');
define ('ADMIN_CFG_HUTKO_CURRENCY', 'Currency');

define('HUTKO_UNKNOWN_ERROR', 'An error has occurred during payment. Please contact us to ensure your order has submitted.');
define('HUTKO_MERCHANT_DATA_ERROR', 'An error has occurred during payment. Merchant data is incorrect.');
define('HUTKO_ORDER_DECLINED', 'Thank you for shopping with us. However, the transaction has been declined.');
define('HUTKO_SIGNATURE_ERROR', 'An error has occurred during payment. Signature is not valid.');
define('HUTKO_REDIRECT_PENDING_STATUS_ERROR', 'An error during payment.');

define('HUTKO_ORDER_APPROVED', 'hutko payment successful. hutko ID:');

define('_JSHOP_REDIRECT_TO_PAYMENT_PAGE', 'Redirection to the payment page');

define ('HUTKO_PAY', 'Pay');

define ('_JSHOP_TRANSACTION_END', 'Transaction end');
define ('_JSHOP_TRANSACTION_FAILED', 'Transaction failed');