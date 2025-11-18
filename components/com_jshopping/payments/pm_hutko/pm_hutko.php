<?php
/*
 * @version      2.0.0
 * @author       DM
 * @package      Jshopping
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

class pm_hutko extends PaymentRoot{
    const VERSION = '2.0';
    const ORDER_APPROVED = 'approved';
    const ORDER_DECLINED = 'declined';
    const SIGNATURE_SEPARATOR = '|';
    const ORDER_SEPARATOR = ":";
	const URL = 'https://pay.hutko.org/api/checkout/redirect/';
	
    /**
     * Connecting the required language file for the module
     */
    function loadLanguageFile(){
      $lang = \Joomla\CMS\Factory::getApplication()->getLanguage();
    
        // determine the current language
        $lang_tag = $lang->getTag();
        // folder with module language files
        $lang_dir = JPATH_ROOT . '/components/com_jshopping/payments/pm_hutko/lang/';
        // variable with the full name of the language file (with path)
        $lang_file = $lang_dir . $lang_tag . '.php';
        // we try to connect the language file, if there is none - it is connected by default (en-GB.php)
        if(file_exists($lang_file))
            require_once $lang_file;
        else
            require_once $lang_dir . 'en-GB.php';
    }

    function showPaymentForm($params, $pmconfigs){
        include(dirname(__FILE__) . "/paymentform.php");
    }

    /**
     * This method is responsible for the plugin settings in the admin section.
     * @param $params Plugin settings parameters
     */
    function showAdminFormParams($params){
        $module_params_array = array(
			'hutko_redirect',
            'hutko_merchant_id',
            'hutko_secret_key',
			'hutko_cur',
            'transaction_end_status',
            'transaction_failed_status'
        );
        foreach($module_params_array as $module_param){
            if(!isset($params[$module_param]))
                $params[$module_param] = '';
        }
        
      
        $orders = \Joomla\CMS\MVC\Model\BaseDatabaseModel::getInstance('orders', 'JshoppingModel');

        
        $this->loadLanguageFile();
        include dirname(__FILE__) . '/adminparamsform.php';
    }

    /* !!! Ch Log !!! S  */
    function getReservationDataProducts($orderItemsProducts)
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

    function getReservationData($order)
    {
      $_country = \JSFactory::getTable('country');
      $_country->load($order->d_country);
      $country = $_country->country_code_2;

        $reservationData = [
            'customer_zip' => $order->d_zip,
            'customer_name' => $order->d_f_name . ' ' . $order->d_l_name,
            'customer_address' => $order->d_street . ' ' . $order->d_city,
            'customer_state' => $order->d_state,
            'customer_country' => $country,
            'phonemobile' => $order->d_phone,
            'account' => $order->email,
            'cms_name' => 'Joomla',
            'cms_version' => JVERSION,
            'cms_plugin_version' => self::VERSION,
            'shop_domain' => \JURI::root(),
            'path' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
            'products' => $this->getReservationDataProducts($order->getAllItems())
        ];

     return base64_encode(json_encode($reservationData));
    }
    /* !!! Ch Log !!! E  */


	function showEndForm($pmconfigs, $order){
        // We upload a language file to describe possible errors
        $this->loadLanguageFile();
        
        /* Next we get the necessary fields to initialize the payment */

        $lang = \Joomla\CMS\Factory::getApplication()->getLanguage()->getTag();
        

        switch($lang){
            case 'en_EN':
                $lang = 'en';
                break;
            case 'ru_RU':
                $lang = 'ru';
                break;
            default:
                $lang = 'en';
                break;
        }
        $order_id = $order->order_id;
        $description = 'Order :' . $order_id;

        $base_url = JURI::root() . 'index.php?option=com_jshopping&controller=checkout&task=step7&js_paymentclass=' . __CLASS__ . '&order_id=' . $order_id;
        $success_url = $base_url . '&act=finish';
        $fail_url = $base_url . '&act=cancel';
		if($pmconfigs['hutko_cur']!= '')
		{$cur = $pmconfigs['hutko_cur'];}else{$cur = $order->currency_code_iso;}
        $result_url = $base_url . '&act=notify&nolang=1';
		
        ?>

    <?php /* !!! Ch Log !!! S  */ ?>
    <?php
      $reservation_data = $this->getReservationData($order);
   //-- die();
    ?>
    <?php /* !!! Ch Log !!! E  */ ?>

    <?php /* !!! Ch Log !!! here ... */ ?>    
		<?php if ($pmconfigs['hutko_redirect'] == 1) {
			 $hutko_args = array('order_id' => $order_id . self::ORDER_SEPARATOR . time(),
		    'merchant_id' =>  $pmconfigs['hutko_merchant_id'],
            'order_desc' => $description,
            'amount' =>  round($this->fixOrderTotal($order)*100),
            'currency' => $cur,
            'server_callback_url' => $result_url,
            'response_url' => $success_url,
            'lang' => $lang,
            'sender_email' => $order->email, 
            'reservation_data' => $reservation_data);

        $hutko_args['signature'] = $this->getSignature($hutko_args, $pmconfigs['hutko_secret_key']);
		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://pay.hutko.org/api/checkout/url/');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('request'=>$hutko_args)));
			$result = json_decode(curl_exec($ch));
			if ($result->response->response_status == 'failure'){
				echo $result->response->error_message;
				exit;
			}
						?>
        <html>
        <head>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
            <script src="https://pay.hutko.org/static_common/v1/checkout/ipsp.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.js"></script>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css" type="text/css" rel="stylesheet" media="screen">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        </head>
        <body>
        <style>
            #checkout_wrapper a{
                font-size: 20px;
                top: 30px;
                padding: 20px;
                position: relative;
            }
            #checkout_wrapper {
                text-align: left;
                position: relative;
                background: #FFF;
                /* padding: 30px; */
                padding-left: 15px;
                padding-right: 15px;
                padding-bottom: 30px;
                width: auto;
                max-width: 2000px;
                margin: 9px auto;
				
            }
			#checkout{
			overflow:hidden;	
			}

        </style>
        <div id="checkout">
            <div id="checkout_wrapper"></div>
        </div>
		<?php if ($pmconfigs['hutko_popup'] == 1) { ?>
			<script>
				$(document).ready(function() {
					$.magnificPopup.open({
						showCloseBtn:false,
						items: {
							src: $("#checkout_wrapper"),
							type: "inline"
						},
						callbacks: {
							close: function() { location.href = '<?php echo $fail_url ?>'}
						}
					});
				})
			</script>
		<?php } ?>
        <script>
		var checkoutStyles = {
		<?php echo  $pmconfigs['hutko_style'] ?>
		}
		function checkoutInit(url, val) {
                $ipsp("checkout").scope(function() {
                    this.setCheckoutWrapper("#checkout_wrapper");
                    this.addCallback(__DEFAULTCALLBACK__);
					this.setCssStyle(checkoutStyles);
                    this.width('100%');
                    this.action("show", function(data) {
                        $("#checkout_loader").remove();
                        $("#checkout").show();
                    });
                    this.action("hide", function(data) {
                        $("#checkout").hide();
                    });
                    if(val){
                        this.width(val);
                        this.action("resize", function(data) {
                            $("#checkout_wrapper").width(val).height(data.height);
                        });
                    }else{
                        this.action("resize", function(data) {
                            $("#checkout_wrapper").width(<?php echo ($pmconfigs['hutko_popup'] == 1) ? "480" : '"100%"'; ?>).height(data.height);
                        });
                    }
                    this.loadUrl(url);
                });
            }
            checkoutInit("<?php echo $result->response->checkout_url ?>");
        </script>
        </body>
        </html>
    <?php /* !!! Ch Log !!! here 2 ... */ ?>   
		<?php }else { 
			 $hutko_args = array('order_id' => $order_id . self::ORDER_SEPARATOR . time(),
		    'merchant_id' =>  $pmconfigs['hutko_merchant_id'],
            'order_desc' => $description,
            'amount' =>  round($this->fixOrderTotal($order)*100),
            'currency' => $cur,
            'server_callback_url' => $result_url,
            'response_url' => $success_url,
            'lang' => $lang,
            'sender_email' => $order->email, 
            'reservation_data' => $reservation_data);
        $hutko_args['signature'] = $this->getSignature($hutko_args, $pmconfigs['hutko_secret_key']);
		?>
		  <html>
        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />            
        </head>
        <body>
        <form id="paymentform" action="<?php print pm_hutko::URL; ?>" name = "paymentform" method = "post">
        <?php
            foreach ($hutko_args as $key => $value) :
        ?>
                <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
        <?php
            endforeach;
			//die();
        ?>
        </form>        
        <?php print _JSHOP_REDIRECT_TO_PAYMENT_PAGE ?>
        <br>
        <script type="text/javascript">document.getElementById('paymentform').submit();</script>
        </body>
        </html>
        <?php
        die(); } ?>
        <?php

	}
    function checkTransaction($pmconfig, $order, $rescode)
    {
        // We upload a language file to describe possible errors
        $this->loadLanguageFile();

        // get an object containing input data (GET and POST), use instead of deprecated JRequest::getInt('var')

    $callback = \Joomla\CMS\Factory::getApplication()->input->post->getArray();

    /* !!! Ch Log !!! + */
    $emty_callb = "not empty";

    If (empty($callback)){
      
      $emty_callb = "empty!";

            $fap = json_decode(file_get_contents("php://input"));
            foreach($fap as $key=>$val)
            {
                $callback[$key] =  $val ;
            }
		}

    $paymentInfo = [];
        $paymentInfo = $this->isPaymentValid($callback, $pmconfig, $order);

                /*****************!!! Ch Log !!!**************************/

        return $paymentInfo;
    }

    function getSignature($data, $password, $encoded = true)
    {
        $data = array_filter($data, function($var) {
            return $var !== '' && $var !== null;
        });
        ksort($data);

        $str = $password;
        foreach ($data as $k => $v) {
            $str .= self::SIGNATURE_SEPARATOR . $v;
        }

        if ($encoded) {
            return sha1($str);
        } else {
            return $str;
        }
    }

    function isPaymentValid($response, $pmconfig, $order)
    {
        list($orderId,) = explode(self::ORDER_SEPARATOR, $response['order_id']);
        if ($orderId != $order->order_id) {
            return array(0, HUTKO_UNKNOWN_ERROR);
        }
		
        if ($pmconfig['hutko_merchant_id'] != $response['merchant_id']) {

            return array(0, HUTKO_MERCHANT_DATA_ERROR);
        }

        $responseSignature = $response['signature'];
		if (isset($response['response_signature_string'])){
			unset($response['response_signature_string']);
		}
		if (isset($response['signature'])){
			unset($response['signature']);
		}
		
		
        if ($this->getSignature($response, $pmconfig['hutko_secret_key']) != $responseSignature) {
            return array(0, HUTKO_SIGNATURE_ERROR);
        }

        if ($response['order_status'] != self::ORDER_APPROVED) {
            return array(0, HUTKO_ORDER_DECLINED);
        }

        if ($response['order_status'] == self::ORDER_APPROVED) {
            \Joomla\CMS\Factory::getApplication()->enqueueMessage( HUTKO_ORDER_APPROVED . $_REQUEST['payment_id']);
           
            return array(1, HUTKO_ORDER_APPROVED . $_REQUEST['payment_id']);

        }

    }

    function getUrlParams($hutko_config){
        $params = array();

        $input = \Joomla\CMS\Factory::getApplication()->input;
        

        $params['order_id'] = $input->getInt('order_id', null);
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = 1;
        return $params;
    }
    
	function fixOrderTotal($order){
        $total = $order->order_total;
        if ($order->currency_code_iso=='HUF'){
            $total = round($total);
        }else{
            $total = number_format($total, 2, '.', '');
        }
    return $total;
    }
}
?>