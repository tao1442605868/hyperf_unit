<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 *
 * 微信支付//支付宝支付
 */

namespace Hyperf\assembly\Annotation;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class Payment
{
    protected $wechatConfig;
    protected $alipayConfig;
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * 构造函数
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->alipayConfig = (object)($container->get(ConfigInterface::class)->get('cwtpay.alipay.default'));
        $this->wechatConfig = (object)($container->get(ConfigInterface::class)->get('cwtpay.wechat.default'));
    }
    /**
     * 支付宝wap支付
     */
    public function getAlipay($data){
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($this->alipayConfig);
        new \Yurun\PaySDK\Alipay\SDK($this->alipayConfig);
        // 支付接口
        $request = new \Yurun\PaySDK\AlipayApp\Wap\Params\Pay\Request;
        $request->notify_url = 'http://meet.gzyxxy.com/githook/wp-git.php'; // 支付后通知地址（作为支付成功回调，这个可靠）
        $request->return_url = 'http://meet.gzyxxy.com/githook/wp-git.php'; // 支付后跳转返回地址
        $request->businessParams->out_trade_no = 'test' . mt_rand(10000000,99999999); // 商户订单号
        $request->businessParams->total_amount = 0.01; // 价格
        $request->businessParams->subject = '小米手机9黑色陶瓷尊享版'; // 商品标题

        // 跳转到支付页面
        // $pay->redirectExecute($request);

        // 获取跳转url
        $pay->prepareExecute($request, $url);
        var_dump($url);
    }
    /**
     * 支付宝app支付
     */
    public function getAppAlipay($data){
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($this->alipayConfig);
        // 支付接口
        $request = new \Yurun\PaySDK\AlipayApp\App\Params\Pay\Request;
        $request->notify_url = 'http://meet.gzyxxy.com/githook/wp-git.php'; // 支付后通知地址（作为支付成功回调，这个可靠）
        $request->businessParams->out_trade_no = 'test' . mt_rand(10000000,99999999); // 商户订单号
        $request->businessParams->total_amount = 0.01; // 价格
        $request->businessParams->subject = '小米手机9黑色陶瓷尊享版'; // 商品标题
        // 处理
        $pay->prepareExecute($request, $url, $data);
        // echo $url; // 输出的是可以让app直接请求的url
        echo http_build_query($data); // 输出的是可以让app直接使用的参数
    }
    /**
     * 支付宝提现
     */
    public function alipayWithdraw($data){
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($this->alipayConfig);
        // 支付接口
        $request = new \Yurun\PaySDK\AlipayApp\Fund\Transfer\Request;
        $request->businessParams->out_biz_no = 'test' . mt_rand(10000000,99999999);
        $request->businessParams->payee_type = 'ALIPAY_LOGONID';
        $request->businessParams->payee_account = 'jgfreb0683@sandbox.com';
        $request->businessParams->amount = '100';
        // 调用接口
        $result = $pay->execute($request);
        var_dump('result:', $result);
        var_dump('success:', $pay->checkResult());
        var_dump('error:', $pay->getError(), 'error_code:', $pay->getErrorCode());
    }
    /**
     * 支付宝回调
     */
    public function alipayNotify(){
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($this->alipayConfig);
        if($pay->verifyCallback($this->request->all()))
        {
            // 通知验证成功，可以通过POST参数来获取支付宝回传的参数
            return 'success';
        }
        else
        {
            // 通知验证失败
            return false;
        }
    }

    /*----------------------------------------分隔----------------上面是支付宝---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
    /*----------------------------------------分隔----------------下面是微信-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

    /**
     * 微信h5支付
     */
    public function getWxpay($data){
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\Weixin\SDK($this->wechatConfig);
        // 支付接口
        $request = new \Yurun\PaySDK\Weixin\H5\Params\Pay\Request;
        $request->body = 'test'; // 商品描述
        $request->out_trade_no = 'test' . mt_rand(10000000,99999999); // 订单号
        $request->total_fee = 1; // 订单总金额，单位为：分
        $request->spbill_create_ip = '127.0.0.1'; // 客户端ip
        $request->notify_url = 'http://meet.gzyxxy.com/githook/wp-git.php'; // 异步通知地址
        // 调用接口
        $result = $pay->execute($request);
        if($pay->checkResult())
        {
            var_dump($result);
            // 跳转支付界面
            //header('Location: ' . $result['mweb_url']);
        }else {
            var_dump($pay->getErrorCode() . ':' . $pay->getError());
        }
    }
    /**
     * 微信app支付
     */
    public function getAppWxpay($getdata){
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\Weixin\SDK($this->wechatConfig);
        // 支付接口
        $request = new \Yurun\PaySDK\Weixin\APP\Params\Pay\Request;
        $request->body = 'test'; // 商品描述
        $request->out_trade_no = 'test' . mt_rand(10000000,99999999); // 订单号
        $request->total_fee = 1; // 订单总金额，单位为：分
        $request->spbill_create_ip = '127.0.0.1'; // 客户端ip，必须传正确的用户ip，否则会报错
        $request->notify_url = $GLOBALS['PAY_CONFIG']['pay_notify_url']; // 异步通知地址
        $request->scene_info->store_id = '门店唯一标识，选填';
        $request->scene_info->store_name = '门店名称，选填';
        // 调用接口
        $result = $pay->execute($request);
        if($pay->checkResult())
        {
            $clientRequest = new \Yurun\PaySDK\Weixin\APP\Params\Client\Request;
            $clientRequest->prepayid = $result['prepay_id'];
            $pay->prepareExecute($clientRequest, $url, $data);
            var_dump($data); // 需要将这个数据返回给app端
        }
        else
        {
            var_dump($pay->getErrorCode() . ':' . $pay->getError());
        }
    }
    /**
     * 微信公众号和小程序支付
     */
    public function getJspWxpay($data){
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\Weixin\SDK($this->wechatConfig);
        // 支付接口
        $request = new \Yurun\PaySDK\Weixin\JSAPI\Params\Pay\Request;
        $request->body = 'test'; // 商品描述
        $request->out_trade_no = 'test' . mt_rand(10000000,99999999); // 订单号
        $request->total_fee = 1; // 订单总金额，单位为：分
        $request->spbill_create_ip = '127.0.0.1'; // 客户端ip
        $request->notify_url = $GLOBALS['PAY_CONFIG']['pay_notify_url']; // 异步通知地址
        $request->openid = ''; // 必须设置openid
        // 调用接口
        $result = $pay->execute($request);
        var_dump('result:', $result);
        var_dump('success:', $pay->checkResult());
        var_dump('error:', $pay->getError(), 'error_code:', $pay->getErrorCode());
        if($pay->checkResult())
        {
            $request = new \Yurun\PaySDK\Weixin\JSAPI\Params\JSParams\Request;
            $request->prepay_id = $result['prepay_id'];
            $jsapiParams = $pay->execute($request);
            // 最后需要将数据传给js，使用WeixinJSBridge进行支付
            echo json_encode($jsapiParams);
        }
    }
    /**
     * 微信提现
     */
    public function wxpayWithdraw($data){
        // SDK实例化，传入公共配置
        $sdk = new \Yurun\PaySDK\Weixin\SDK($this->wechatConfig);
        $request = new \Yurun\PaySDK\Weixin\CompanyPay\Weixin\Pay\Request;
        $request->partner_trade_no = 'test' . mt_rand(10000000,99999999); // 订单号
        $request->openid = 'opWUlwsi_2Yy9ScbM9EdSJCxY-QA';
        $request->check_name = 'NO_CHECK';
        $request->amount = 1;
        $request->desc = '测试';
        $request->spbill_create_ip = '127.0.0.1';
        $result = $sdk->execute($request);
        var_dump('result:', $result);
        var_dump('success:', $sdk->checkResult());
        var_dump('error:', $sdk->getError(), 'error_code:', $sdk->getErrorCode());
    }
    /**
     * 微信回调
     */
    public function wxpayNotify(){
        $sdk = new \Yurun\PaySDK\Weixin\SDK($this->wechatConfig);
        $payNotify = new class extends \Yurun\PaySDK\Weixin\Notify\Pay
        {
            /**
             * 后续执行操作
             * @return void
             */
            protected function __exec(){
                //订单操作

                //结果
                $this->reply('SUCCESS', 'OK');
            }
        };
        // 目前主流 Swoole 基本都支持 PSR-7 标准的对象
        // 所以可以直接传入，如何获取请查阅对应框架的文档
        $payNotify->swooleRequest = $this->request;
        $payNotify->swooleResponse = $this->response;
        $sdk->notify($payNotify);
        // 处理完成后需要将 $response 从控制器返回或者赋值给上下文
        // 不同框架的操作不同，请自行查阅对应框架的文档
        return $payNotify->swooleResponse;
    }
}

