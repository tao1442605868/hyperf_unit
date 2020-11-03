<?php
declare(strict_types=1);

return [
    /*
     * 微信支付
     */
    'wechat' => [
        'default' => [
            'appID'             => env('WECHAT_PAYMENT_APPID', ''),//appid
            'mch_id'            => env('WECHAT_PAYMENT_MCH_ID', ''),//商户号
            'key'               => env('WECHAT_PAYMENT_KEY', ''),//商户密钥
            'certPath'          => env('WECHAT_PAYMENT_CERT_PATH', ''),    // XXX: 绝对路径！！！！
            'keyPath'           => env('WECHAT_PAYMENT_KEY_PATH', ''),     // XXX: 绝对路径！！！！
            'sign_type'         => 'MD5',
            'apiDomain'         => 'https://api.mch.weixin.qq.com/',
            'reportLevel'       => '2'
        ],
    ],
    /*
     * 支付宝支付
     */
    'alipay' => [
        'default' => [
            'appID'                => env('ALIPAY_APPID', ''),//appid
            'appPublicKey'         => env('ALIPAY_APPPUBLICKEY', ''),//公钥
            'appPrivateKey'        => env('ALIPAY_APPPRIVATEKEY', ''),//私钥
            'isUseAES'             => false,//是否使用AES加密解密数据
            'sign_type'            => 'RSA2',//加密方式
            'aesKey'               => '',//AES密钥
            //TODO::真实网关https://openapi.alipay.com/gateway.do  下面是沙箱网关
            'apiDomain'            => 'https://openapi.alipaydev.com/gateway.do',//网关
            //公钥证书模式
            'usePublicKeyCert'     => env('ALIPAY_USERPUBLICKEYCERT',false),//是否使用公钥证书模式
            'alipayCertPath'       => env('ALIPAY_ALIPAYCERTPATH', ''),//支付宝公钥证书文件路径
            'merchantCertPath'     => env('ALIPAY_MERCHANTCERTPATH', ''),//支付宝应用公钥证书文件路径
            'alipayRootCertPath'   => env('ALIPAY_ALIPAYROOTCERTPATH', ''),//支付宝根证书文件路径
        ],
    ],
];