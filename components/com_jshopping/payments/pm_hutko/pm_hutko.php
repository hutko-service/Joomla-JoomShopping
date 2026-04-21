<?php
/**
 * @package     JoomShopping
 * @subpackage  Payment.pm_hutko
 *
 * @copyright   (C) 2026 Hutko Service
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;

class pm_hutko extends PaymentRoot
{
    private const VERSION = '1.0.1';
    private const ORDER_APPROVED = 'approved';
    private const SIGNATURE_SEPARATOR = '|';
    private const ORDER_SEPARATOR = ':';
    private const CHECKOUT_URL = 'https://pay.hutko.org/api/checkout/redirect/';
    private const EMBEDDED_URL_API = 'https://pay.hutko.org/api/checkout/url/';

    public function loadLanguageFile()
    {
        $lang = Factory::getApplication()->getLanguage();
        $langTag = $lang->getTag();
        $langDir = JPATH_ROOT . '/components/com_jshopping/payments/pm_hutko/lang/';
        $langFile = $langDir . $langTag . '.php';

        if (is_file($langFile)) {
            require_once $langFile;

            return;
        }

        require_once $langDir . 'en-GB.php';
    }

    public function showPaymentForm($params, $pmconfigs)
    {
        include __DIR__ . '/paymentform.php';
    }

    public function showAdminFormParams($params)
    {
        foreach (['hutko_redirect', 'hutko_merchant_id', 'hutko_secret_key', 'hutko_cur', 'transaction_end_status', 'transaction_failed_status'] as $key) {
            if (!isset($params[$key])) {
                $params[$key] = '';
            }
        }

        $orders = BaseDatabaseModel::getInstance('orders', 'JshoppingModel');

        $this->loadLanguageFile();
        include __DIR__ . '/adminparamsform.php';
    }

    public function showEndForm($pmconfigs, $order)
    {
        $this->loadLanguageFile();

        $orderId = (int) $order->order_id;
        $currency = !empty($pmconfigs['hutko_cur']) ? $pmconfigs['hutko_cur'] : $order->currency_code_iso;
        $baseUrl = Uri::root() . 'index.php?option=com_jshopping&controller=checkout&task=step7&js_paymentclass=' . __CLASS__ . '&order_id=' . $orderId;
        $successUrl = $baseUrl . '&act=finish';
        $resultUrl = $baseUrl . '&act=notify&nolang=1';

        $checkoutData = [
            'order_id' => $orderId . self::ORDER_SEPARATOR . time(),
            'merchant_id' => trim((string) ($pmconfigs['hutko_merchant_id'] ?? '')),
            'order_desc' => 'Order: ' . $orderId,
            'amount' => (int) round($this->fixOrderTotal($order) * 100),
            'currency' => $currency,
            'server_callback_url' => $resultUrl,
            'response_url' => $successUrl,
            'lang' => $this->mapLanguageTag(Factory::getApplication()->getLanguage()->getTag()),
            'sender_email' => (string) $order->email,
            'reservation_data' => $this->getReservationData($order),
        ];

        $checkoutData['signature'] = $this->getSignature($checkoutData, (string) ($pmconfigs['hutko_secret_key'] ?? ''));

        if ((int) ($pmconfigs['hutko_redirect'] ?? 0) === 1) {
            $checkoutUrl = $this->requestEmbeddedCheckoutUrl($checkoutData);

            if ($checkoutUrl === null) {
                echo htmlspecialchars(HUTKO_CHECKOUT_INIT_ERROR, ENT_QUOTES, 'UTF-8');
                die();
            }

            ?>
            <html>
            <head>
                <meta http-equiv="content-type" content="text/html; charset=utf-8">
                <script src="https://pay.hutko.org/static_common/v1/checkout/ipsp.js"></script>
            </head>
            <body>
            <div id="checkout" style="display:none;overflow:hidden;">
                <div id="checkout_wrapper" style="background:#fff;margin:9px auto;max-width:980px;padding:15px 15px 30px;"></div>
            </div>
            <script>
            (function () {
                $ipsp("checkout").scope(function () {
                    this.setCheckoutWrapper("#checkout_wrapper");
                    this.width("100%");
                    this.action("show", function () {
                        document.getElementById("checkout").style.display = "block";
                    });
                    this.action("hide", function () {
                        document.getElementById("checkout").style.display = "none";
                    });
                    this.action("resize", function (data) {
                        var wrapper = document.getElementById("checkout_wrapper");
                        wrapper.style.height = data.height + "px";
                    });
                    this.loadUrl("<?php echo htmlspecialchars($checkoutUrl, ENT_QUOTES, 'UTF-8'); ?>");
                });
            }());
            </script>
            </body>
            </html>
            <?php
            die();
        }

        ?>
        <html>
        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8">
        </head>
        <body>
        <form id="paymentform" action="<?php echo htmlspecialchars(self::CHECKOUT_URL, ENT_QUOTES, 'UTF-8'); ?>" method="post" name="paymentform">
            <?php foreach ($checkoutData as $key => $value) : ?>
                <input type="hidden" name="<?php echo htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?>">
            <?php endforeach; ?>
        </form>
        <?php echo htmlspecialchars(_JSHOP_REDIRECT_TO_PAYMENT_PAGE, ENT_QUOTES, 'UTF-8'); ?>
        <br>
        <script>document.getElementById('paymentform').submit();</script>
        </body>
        </html>
        <?php
        die();
    }

    public function checkTransaction($pmconfig, $order, $rescode)
    {
        $this->loadLanguageFile();

        $callback = Factory::getApplication()->input->post->getArray();

        if (empty($callback)) {
            $rawBody = file_get_contents('php://input');
            $decoded = json_decode($rawBody, true);

            if (is_array($decoded)) {
                $callback = $decoded;
            }
        }

        return $this->isPaymentValid($callback, $pmconfig, $order);
    }

    public function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, static function ($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);

        $signatureString = $password;

        foreach ($data as $value) {
            $signatureString .= self::SIGNATURE_SEPARATOR . $value;
        }

        return $encoded ? sha1($signatureString) : $signatureString;
    }

    public function isPaymentValid($response, $pmconfig, $order)
    {
        if (empty($response['order_id']) || empty($response['merchant_id']) || empty($response['signature']) || empty($response['order_status'])) {
            return [0, HUTKO_UNKNOWN_ERROR];
        }

        [$orderId] = explode(self::ORDER_SEPARATOR, (string) $response['order_id']);

        if ((int) $orderId !== (int) $order->order_id) {
            return [0, HUTKO_UNKNOWN_ERROR];
        }

        if ((string) ($pmconfig['hutko_merchant_id'] ?? '') !== (string) $response['merchant_id']) {
            return [0, HUTKO_MERCHANT_DATA_ERROR];
        }

        $responseSignature = (string) $response['signature'];
        unset($response['response_signature_string'], $response['signature']);

        if ($this->getSignature($response, (string) ($pmconfig['hutko_secret_key'] ?? '')) !== $responseSignature) {
            return [0, HUTKO_SIGNATURE_ERROR];
        }

        if ((string) $response['order_status'] !== self::ORDER_APPROVED) {
            return [0, HUTKO_ORDER_DECLINED];
        }

        $paymentId = isset($response['payment_id']) ? ' ' . (string) $response['payment_id'] : '';

        return [1, HUTKO_ORDER_APPROVED . $paymentId];
    }

    public function getUrlParams($hutkoConfig)
    {
        $input = Factory::getApplication()->input;

        return [
            'order_id' => $input->getInt('order_id', null),
            'hash' => '',
            'checkHash' => 0,
            'checkReturnParams' => 1,
        ];
    }

    public function fixOrderTotal($order)
    {
        $total = (float) $order->order_total;

        if ($order->currency_code_iso === 'HUF') {
            return (float) round($total);
        }

        return (float) number_format($total, 2, '.', '');
    }

    private function mapLanguageTag(string $languageTag): string
    {
        return match ($languageTag) {
            'uk-UA' => 'uk',
            'ru-RU' => 'ru',
            default => 'en',
        };
    }

    private function requestEmbeddedCheckoutUrl(array $checkoutData): ?string
    {
        $payload = json_encode(['request' => $checkoutData]);

        if ($payload === false) {
            return null;
        }

        try {
            $http = HttpFactory::getHttp();
            $response = $http->post(
                self::EMBEDDED_URL_API,
                $payload,
                ['Content-Type' => 'application/json'],
                30
            );

            if ((int) $response->code >= 400) {
                return null;
            }

            $decoded = json_decode((string) $response->body, true);
        } catch (\Throwable $exception) {
            if (!function_exists('curl_init')) {
                return null;
            }

            $ch = curl_init(self::EMBEDDED_URL_API);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $body = curl_exec($ch);
            $curlError = curl_errno($ch);
            curl_close($ch);

            if ($curlError || $body === false) {
                return null;
            }

            $decoded = json_decode($body, true);
        }

        $status = $decoded['response']['response_status'] ?? null;
        $checkoutUrl = $decoded['response']['checkout_url'] ?? null;

        if ($status !== 'success' || empty($checkoutUrl)) {
            return null;
        }

        return (string) $checkoutUrl;
    }

    private function getReservationDataProducts($orderItemsProducts): array
    {
        $reservationDataProducts = [];

        foreach ($orderItemsProducts as $orderProduct) {
            $reservationDataProducts[] = [
                'id' => $orderProduct->product_id,
                'name' => $orderProduct->product_name,
                'price' => $orderProduct->product_item_price,
                'total_amount' => $orderProduct->product_item_price * $orderProduct->product_quantity,
                'quantity' => $orderProduct->product_quantity,
            ];
        }

        return $reservationDataProducts;
    }

    private function getReservationData($order): string
    {
        $_country = \JSFactory::getTable('country');
        $_country->load($order->d_country);
        $country = $_country->country_code_2;

        $reservationData = [
            'customer_zip' => $order->d_zip,
            'customer_name' => trim($order->d_f_name . ' ' . $order->d_l_name),
            'customer_address' => trim($order->d_street . ' ' . $order->d_city),
            'customer_state' => $order->d_state,
            'customer_country' => $country,
            'phonemobile' => $order->d_phone,
            'account' => $order->email,
            'cms_name' => 'Joomla',
            'cms_version' => JVERSION,
            'cms_plugin_version' => self::VERSION,
            'shop_domain' => Uri::root(),
            'path' => $_SERVER['HTTP_REFERER'] ?? '',
            'products' => $this->getReservationDataProducts($order->getAllItems()),
        ];

        return base64_encode((string) json_encode($reservationData));
    }
}
