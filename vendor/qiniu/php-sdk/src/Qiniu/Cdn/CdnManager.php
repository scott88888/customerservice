<?php

namespace Qiniu\Cdn;

use Qiniu\Auth;
use Qiniu\Http\Error;
use Qiniu\Http\Client;

final class CdnManager
{

    private $auth;
    private $server;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->server = 'http://fusion.qiniuapi.com';
    }

    /**
     * @param array $urls 待刷新的文件連結數组
     * @return array
     */
    public function refreshUrls(array $urls)
    {
        return $this->refreshUrlsAndDirs($urls, array());
    }

    /**
     * @param array $dirs 待刷新的文件連結數组
     * @return array
     * 目前客户默認没有目錄刷新权限，刷新会有400038报错，参考：https://developer.qiniu.com/fusion/api/1229/cache-refresh
     * 需要刷新目錄請工單联系技術支援 https://support.qiniu.com/tickets/category
     */
    public function refreshDirs(array $dirs)
    {
        return $this->refreshUrlsAndDirs(array(), $dirs);
    }

    /**
     * @param array $urls 待刷新的文件連結數组
     * @param array $dirs 待刷新的目錄連結數组
     *
     * @return array 刷新的請求回覆和錯誤，参考 examples/cdn_manager.php 程式碼
     * @link http://developer.qiniu.com/article/fusion/api/refresh.html
     *
     * 目前客户默認没有目錄刷新权限，刷新会有400038报错，参考：https://developer.qiniu.com/fusion/api/1229/cache-refresh
     * 需要刷新目錄請工單联系技術支援 https://support.qiniu.com/tickets/category
     */
    public function refreshUrlsAndDirs(array $urls, array  $dirs)
    {
        $req = array();
        if (!empty($urls)) {
            $req['urls'] = $urls;
        }
        if (!empty($dirs)) {
            $req['dirs'] = $dirs;
        }

        $url = $this->server . '/v2/tune/refresh';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    /**
     * @param array $urls 待预取的文件連結數组
     *
     * @return array 预取的請求回覆和錯誤，参考 examples/cdn_manager.php 程式碼
     *
     * @link http://developer.qiniu.com/article/fusion/api/refresh.html
     */
    public function prefetchUrls(array $urls)
    {
        $req = array(
            'urls' => $urls,
        );

        $url = $this->server . '/v2/tune/prefetch';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    /**
     * @param array $domains 待取得带宽資料的域名數组
     * @param string $startDate 開始的日期，格式類似 2017-01-01
     * @param string $endDate 结束的日期，格式類似 2017-01-01
     * @param string $granularity 取得資料的時間间隔，可以是 5min, hour 或者 day
     *
     * @return array 带宽資料和錯誤訊息，参考 examples/cdn_manager.php 程式碼
     *
     * @link http://developer.qiniu.com/article/fusion/api/traffic-bandwidth.html
     */
    public function getBandwidthData(array $domains, $startDate, $endDate, $granularity)
    {
        $req = array();
        $req['domains'] = implode(';', $domains);
        $req['startDate'] = $startDate;
        $req['endDate'] = $endDate;
        $req['granularity'] = $granularity;

        $url = $this->server . '/v2/tune/bandwidth';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    /**
     * @param array $domains 待取得流量資料的域名數组
     * @param string $startDate 開始的日期，格式類似 2017-01-01
     * @param string $endDate 结束的日期，格式類似 2017-01-01
     * @param string $granularity 取得資料的時間间隔，可以是 5min, hour 或者 day
     *
     * @return array 流量資料和錯誤訊息，参考 examples/cdn_manager.php 程式碼
     *
     * @link http://developer.qiniu.com/article/fusion/api/traffic-bandwidth.html
     */
    public function getFluxData(array $domains, $startDate, $endDate, $granularity)
    {
        $req = array();
        $req['domains'] = implode(';', $domains);
        $req['startDate'] = $startDate;
        $req['endDate'] = $endDate;
        $req['granularity'] = $granularity;

        $url = $this->server . '/v2/tune/flux';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    /**
     * @param array $domains 待取得日誌下载連結的域名數组
     * @param string $logDate 取得指定日期的日誌下载連結，格式類似 2017-01-01
     *
     * @return array 日誌下载連結資料和錯誤訊息，参考 examples/cdn_manager.php 程式碼
     *
     * @link http://developer.qiniu.com/article/fusion/api/log.html
     */
    public function getCdnLogList(array $domains, $logDate)
    {
        $req = array();
        $req['domains'] = implode(';', $domains);
        $req['day'] = $logDate;

        $url = $this->server . '/v2/tune/log/list';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    private function post($url, $body)
    {
        $headers = $this->auth->authorization($url, $body, 'application/json');
        $headers['Content-Type'] = 'application/json';
        $ret = Client::post($url, $body, $headers);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }

    /**
     * 构建時間戳防盗链鉴权的訪問外链
     *
     * @param string $rawUrl 需要签名的資源url
     * @param string $encryptKey 時間戳防盗链密钥
     * @param string $durationInSeconds 連結的有效期（以秒為單位）
     *
     * @return string 带鉴权訊息的資源外链，参考 examples/cdn_timestamp_antileech.php 程式碼
     */
    public static function createTimestampAntiLeechUrl($rawUrl, $encryptKey, $durationInSeconds)
    {

        $parsedUrl = parse_url($rawUrl);

        $deadline = time() + $durationInSeconds;
        $expireHex = dechex($deadline);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $path = implode('/', array_map('rawurlencode', explode('/', $path)));

        $strToSign = $encryptKey . $path . $expireHex;
        $signStr = md5($strToSign);

        if (isset($parsedUrl['query'])) {
            $signedUrl = $rawUrl . '&sign=' . $signStr . '&t=' . $expireHex;
        } else {
            $signedUrl = $rawUrl . '?sign=' . $signStr . '&t=' . $expireHex;
        }

        return $signedUrl;
    }
}
