# CI-ZarinPal
Codeigniter 3.x library for ZarinPal payment gateway

Based on https://github.com/MahdiMajidzadeh/CodeIgniter-Zarinpal-gateway
Without nusoap library

##how to install
Copy `ZarinPal.php` to `application/libraries` of your project.

##how to use
First, load library:
```
$this->load->library('zarinpal');
```

For sending user to gateway:
```
$this->zarinpal->request($merchantId , $amount, $desc, $callback, $mobile, $email);
```
Full code is:
```
if ($this->zarinpal->request($merchantId , $amount, $desc, $callback, $mobile, $email)) {
    $authority = $this->zarinpal->getAuthority();
    // do database 
    $this->zarinpal->redirect();
} else {
    $error = $this->zarinpal->getError();
}
```
For verify user payment:
```
$this->zarinpal->verify($merchantId , $amount, $authority);
```
Full code is:
```
if ($_GET['Status'] == 'OK') {
    if ($this->zarinpal->verify($merchantId , $amount, $authority)) {
        $refId = $this->zarinpal->getRefId();
        // do database 
    } else {
        $error = $this->zarinpal->getError();
    }
} else {
    // transaction canceled by user
}
```

##Sandbox
To turn on sandbox mode:
```
$this->zarinpal->sandbox();
```
