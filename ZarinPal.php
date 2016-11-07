<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ZarinPal {

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
}
