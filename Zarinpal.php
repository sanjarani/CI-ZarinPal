<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Zarinpal {

    private $authority;
    private $error;
    private $refId;
    private $url;

    private $wsdlUrl = 'https://www.zarinpal.com/pg/services/WebGate/wsdl';
    private $payUrl  = 'https://www.zarinpal.com/pg/StartPay/';

    public function request($merchantId, $amount, $desc, $callback, $mobile = '', $email = '')
    {
        $params = array(
            'MerchantID'  => $merchantId,
            'Amount'      => $amount,
            'Description' => $desc,
            'CallbackURL' => $callback
        );

        if ($mobile) {
            $params['Mobile'] = $mobile;
        }

        if ($email) {
            $params['Email'] = $email;
        }

        $client = new SoapClient($this->wsdlUrl, array(
            'encoding' => 'UTF-8'
        ));

        $result = $client->PaymentRequest($params);

        if ($result->Status != 100) {
            $this->error = $result->Status;
            return false;
        }

        $this->authority = $result->Authority;
        $this->url       = $this->payUrl . $this->authority;
        return true;
    }

    public function redirect()
    {
        if (!function_exists('redirect')) {
            $CI =& get_instance();
            $CI->load->helper('url');
        }

        redirect($this->url);
    }

    public function verify($merchantId, $amount, $authority)
    {
        $params = array(
            'MerchantID' => $merchantId,
            'Amount'     => $amount,
            'Authority'  => $authority
        );

        $client = new SoapClient($this->wsdlUrl, array(
            'encoding' => 'UTF-8'
        ));

        $result = $client->PaymentVerification($params);

        if ($result->Status != 100) {
            $this->error = $result->Status;
            return false;
        }

        $this->refId = $result->RefID;
        return true;
    }

    public function sandbox()
    {
        $this->wsdlUrl = 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl';
        $this->payUrl  = 'https://sandbox.zarinpal.com/pg/StartPay/';
    }

    public function getAuthority()
    {
        return $this->authority;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getRefId()
    {
        return $this->refId;
    }
    
    public function errorText($errorNumber)
    {
        switch ($errorNumber)
        {
            case -1:
                return 'اطلاعات ارسال شده ناقص است';

            case -2:
                return 'ip و يا مرچنت كد پذيرنده صحيح نيست';
                
            case -3:
                return 'با توجه به محدوديت هاي شاپرك امكان پرداخت با رقم درخواست شده ميسر نمي باشد';
                
            case -4:
                return 'سطح تاييد پذيرنده پايين تر از سطح نقره اي است';
                
            case -11:
                return 'درخواست مورد نظر يافت نشد';
                
            case -12:
                return 'امكان ويرايش درخواست ميسر نمي باشد';
                
            case -21:
                return 'هيچ نوع عمليات مالي براي اين تراكنش يافت نشد';
                
            case -22:
                return 'تراكنش نا موفق ميباشد';
                
            case -33:
                return 'رقم تراكنش با رقم پرداخت شده مطابقت ندارد';
                
            case -34:
                return 'سقف تقسيم تراكنش از لحاظ تعداد يا رقم عبور نموده است';
                
            case -40:
                return 'اجازه دسترسي به متد مربوطه وجود ندارد';
                
            case -41:
                return 'اطلاعات ارسال شده مربوط به AdditionalData غيرمعتبر ميباشد';
                
            case -42:
                return 'مدت زمان معتبر طول عمر شناسه پرداخت بايد بين 30 دقيه تا 45 روز مي باشد';
                
            case -54:
                return 'درخواست مورد نظر آرشيو شده است';
                
            case 100:
                return 'عمليات با موفقيت انجام گرديده است';
                
            case 101:
                return 'عمليات پرداخت موفق بوده و قبلا PaymentVerification تراكنش انجام شده است';
                
            default :
                return 'خطای غیرمنتظره ای روی داد';
        }
        
    }
}
