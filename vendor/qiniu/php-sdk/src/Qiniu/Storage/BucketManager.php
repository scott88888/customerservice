<?php
namespace Qiniu\Storage;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Zone;
use Qiniu\Http\Client;
use Qiniu\Http\Error;

/**
 * 主要涉及了空間資源管理及批量操作接口的實現，具体的接口规格可以参考
 *
 * @link https://developer.qiniu.com/kodo/api/1274/rs
 */
final class BucketManager
{
    private $auth;
    private $config;

    public function __construct(Auth $auth, Config $config = null)
    {
        $this->auth = $auth;
        if ($config == null) {
            $this->config = new Config();
        } else {
            $this->config = $config;
        }
    }

    /**
     * 取得指定账号下所有的空間名。
     *
     * @return string[] 包含所有空間名
     */
    public function buckets($shared = true)
    {
        $includeShared = "false";
        if ($shared === true) {
            $includeShared = "true";
        }
        return $this->rsGet('/buckets?shared=' . $includeShared);
    }

    /**
     * 取得指定空間绑定的所有的域名
     *
     * @return string[] 包含所有空間域名
     */
    public function domains($bucket)
    {
        return $this->apiGet('/v6/domain/list?tbl=' . $bucket);
    }

    /**
     * 取得空間绑定的域名列表
     * @return string[] 包含空間绑定的所有域名
     */

    /**
     * 列取空間的文件列表
     *
     * @param $bucket     空間名
     * @param $prefix     列举前缀
     * @param $marker     列举標識符
     * @param $limit      單次列举个數限制
     * @param $delimiter  指定目錄分隔符
     *
     * @return array    包含文件訊息的數组，類似：[
     *                                              {
     *                                                 "hash" => "<Hash string>",
     *                                                  "key" => "<Key string>",
     *                                                  "fsize" => "<file size>",
     *                                                  "putTime" => "<file modify time>"
     *                                              },
     *                                              ...
     *                                            ]
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/list.html
     */
    public function listFiles($bucket, $prefix = null, $marker = null, $limit = 1000, $delimiter = null)
    {
        $query = array('bucket' => $bucket);
        \Qiniu\setWithoutEmpty($query, 'prefix', $prefix);
        \Qiniu\setWithoutEmpty($query, 'marker', $marker);
        \Qiniu\setWithoutEmpty($query, 'limit', $limit);
        \Qiniu\setWithoutEmpty($query, 'delimiter', $delimiter);
        $url = $this->getRsfHost() . '/list?' . http_build_query($query);
        return $this->get($url);
    }

    /**
     * 取得資源的元訊息，但不返回文件内容
     *
     * @param $bucket     待取得訊息資源所在的空間
     * @param $key        待取得資源的文件名
     *
     * @return array    包含文件訊息的數组，類似：
     *                                              [
     *                                                  "hash" => "<Hash string>",
     *                                                  "key" => "<Key string>",
     *                                                  "fsize" => <file size>,
     *                                                  "putTime" => "<file modify time>"
     *                                                  "fileType" => <file type>
     *                                              ]
     *
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/stat.html
     */
    public function stat($bucket, $key)
    {
        $path = '/stat/' . \Qiniu\entry($bucket, $key);
        return $this->rsGet($path);
    }

    /**
     * 刪除指定資源
     *
     * @param $bucket     待刪除資源所在的空間
     * @param $key        待刪除資源的文件名
     *
     * @return mixed      成功返回NULL，失敗返回對象Qiniu\Http\Error
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/delete.html
     */
    public function delete($bucket, $key)
    {
        $path = '/delete/' . \Qiniu\entry($bucket, $key);
        list(, $error) = $this->rsPost($path);
        return $error;
    }


    /**
     * 给資源进行重命名，本质為move操作。
     *
     * @param $bucket     待操作資源所在空間
     * @param $oldname    待操作資源文件名
     * @param $newname    目标資源文件名
     *
     * @return mixed      成功返回NULL，失敗返回對象Qiniu\Http\Error
     */
    public function rename($bucket, $oldname, $newname)
    {
        return $this->move($bucket, $oldname, $bucket, $newname);
    }

    /**
     * 给資源进行重命名，本质為move操作。
     *
     * @param $from_bucket     待操作資源所在空間
     * @param $from_key        待操作資源文件名
     * @param $to_bucket       目标資源空間名
     * @param $to_key          目标資源文件名
     *
     * @return mixed      成功返回NULL，失敗返回對象Qiniu\Http\Error
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/copy.html
     */
    public function copy($from_bucket, $from_key, $to_bucket, $to_key, $force = false)
    {
        $from = \Qiniu\entry($from_bucket, $from_key);
        $to = \Qiniu\entry($to_bucket, $to_key);
        $path = '/copy/' . $from . '/' . $to;
        if ($force === true) {
            $path .= '/force/true';
        }
        list(, $error) = $this->rsPost($path);
        return $error;
    }

    /**
     * 将資源从一个空間到另一个空間
     *
     * @param $from_bucket     待操作資源所在空間
     * @param $from_key        待操作資源文件名
     * @param $to_bucket       目标資源空間名
     * @param $to_key          目标資源文件名
     *
     * @return mixed      成功返回NULL，失敗返回對象Qiniu\Http\Error
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/move.html
     */
    public function move($from_bucket, $from_key, $to_bucket, $to_key, $force = false)
    {
        $from = \Qiniu\entry($from_bucket, $from_key);
        $to = \Qiniu\entry($to_bucket, $to_key);
        $path = '/move/' . $from . '/' . $to;
        if ($force) {
            $path .= '/force/true';
        }
        list(, $error) = $this->rsPost($path);
        return $error;
    }

    /**
     * 主動修改指定資源的文件類型
     *
     * @param $bucket     待操作資源所在空間
     * @param $key        待操作資源文件名
     * @param $mime       待操作文件目标mimeType
     *
     * @return mixed      成功返回NULL，失敗返回對象Qiniu\Http\Error
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/chgm.html
     */
    public function changeMime($bucket, $key, $mime)
    {
        $resource = \Qiniu\entry($bucket, $key);
        $encode_mime = \Qiniu\base64_urlSafeEncode($mime);
        $path = '/chgm/' . $resource . '/mime/' . $encode_mime;
        list(, $error) = $this->rsPost($path);
        return $error;
    }


    /**
     * 修改指定資源的存储類型
     *
     * @param $bucket     待操作資源所在空間
     * @param $key        待操作資源文件名
     * @param $fileType       待操作文件目标文件類型
     *
     * @return mixed      成功返回NULL，失敗返回對象Qiniu\Http\Error
     * @link  https://developer.qiniu.com/kodo/api/3710/modify-the-file-type
     */
    public function changeType($bucket, $key, $fileType)
    {
        $resource = \Qiniu\entry($bucket, $key);
        $path = '/chtype/' . $resource . '/type/' . $fileType;
        list(, $error) = $this->rsPost($path);
        return $error;
    }

    /**
     * 修改文件的存储狀態，即禁用狀態和启用狀態间的的互相轉換
     *
     * @param $bucket     待操作資源所在空間
     * @param $key        待操作資源文件名
     * @param $status       待操作文件目标文件類型
     *
     * @return mixed      成功返回NULL，失敗返回對象Qiniu\Http\Error
     * @link  https://developer.qiniu.com/kodo/api/4173/modify-the-file-status
     */
    public function changeStatus($bucket, $key, $status)
    {
        $resource = \Qiniu\entry($bucket, $key);
        $path = '/chstatus/' . $resource . '/status/' . $status;
        list(, $error) = $this->rsPost($path);
        return $error;
    }

    /**
     * 从指定URL抓取資源，並将该資源存储到指定空間中
     *
     * @param $url        指定的URL
     * @param $bucket     目标資源空間
     * @param $key        目标資源文件名
     *
     * @return array    包含已拉取的文件訊息。
     *                         成功时：  [
     *                                          [
     *                                              "hash" => "<Hash string>",
     *                                              "key" => "<Key string>"
     *                                          ],
     *                                          null
     *                                  ]
     *
     *                         失敗时：  [
     *                                          null,
     *                                         Qiniu/Http/Error
     *                                  ]
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/fetch.html
     */
    public function fetch($url, $bucket, $key = null)
    {

        $resource = \Qiniu\base64_urlSafeEncode($url);
        $to = \Qiniu\entry($bucket, $key);
        $path = '/fetch/' . $resource . '/to/' . $to;

        $ak = $this->auth->getAccessKey();
        $ioHost = $this->config->getIovipHost($ak, $bucket);

        $url = $ioHost . $path;
        return $this->post($url, null);
    }

    /**
     * 从镜像源站抓取資源到空間中，如果空間中已经存在，則覆盖该資源
     *
     * @param $bucket     待取得資源所在的空間
     * @param $key        代取得資源文件名
     *
     * @return mixed      成功返回NULL，失敗返回對象Qiniu\Http\Error
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/prefetch.html
     */
    public function prefetch($bucket, $key)
    {
        $resource = \Qiniu\entry($bucket, $key);
        $path = '/prefetch/' . $resource;

        $ak = $this->auth->getAccessKey();
        $ioHost = $this->config->getIovipHost($ak, $bucket);

        $url = $ioHost . $path;
        list(, $error) = $this->post($url, null);
        return $error;
    }

    /**
     * 在單次請求中进行多个資源管理操作
     *
     * @param $operations     資源管理操作數组
     *
     * @return array 每个資源的處理情况，结果類似：
     *              [
     *                   { "code" => <HttpCode int>, "data" => <Data> },
     *                   { "code" => <HttpCode int> },
     *                   { "code" => <HttpCode int> },
     *                   { "code" => <HttpCode int> },
     *                   { "code" => <HttpCode int>, "data" => { "error": "<ErrorMessage string>" } },
     *                   ...
     *               ]
     * @link http://developer.qiniu.com/docs/v6/api/reference/rs/batch.html
     */
    public function batch($operations)
    {
        $params = 'op=' . implode('&op=', $operations);
        return $this->rsPost('/batch', $params);
    }

    /**
     * 設定文件的生命周期
     *
     * @param $bucket 設定文件生命周期文件所在的空間
     * @param $key    設定文件生命周期文件的文件名
     * @param $days   設定该文件多少天後刪除，当$days設定為0时表示取消该文件的生命周期
     *
     * @return Mixed
     * @link https://developer.qiniu.com/kodo/api/update-file-lifecycle
     */
    public function deleteAfterDays($bucket, $key, $days)
    {
        $entry = \Qiniu\entry($bucket, $key);
        $path = "/deleteAfterDays/$entry/$days";
        list(, $error) = $this->rsPost($path);
        return $error;
    }

    private function getRsfHost()
    {
        $scheme = "http://";
        if ($this->config->useHTTPS == true) {
            $scheme = "https://";
        }
        return $scheme . Config::RSF_HOST;
    }

    private function getRsHost()
    {
        $scheme = "http://";
        if ($this->config->useHTTPS == true) {
            $scheme = "https://";
        }
        return $scheme . Config::RS_HOST;
    }

    private function getApiHost()
    {
        $scheme = "http://";
        if ($this->config->useHTTPS == true) {
            $scheme = "https://";
        }
        return $scheme . Config::API_HOST;
    }

    private function rsPost($path, $body = null)
    {
        $url = $this->getRsHost() . $path;
        return $this->post($url, $body);
    }

    private function apiGet($path)
    {
        $url = $this->getApiHost() . $path;
        return $this->get($url);
    }

    private function rsGet($path)
    {
        $url = $this->getRsHost() . $path;
        return $this->get($url);
    }

    private function get($url)
    {
        $headers = $this->auth->authorization($url);
        $ret = Client::get($url, $headers);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        return array($ret->json(), null);
    }

    private function post($url, $body)
    {
        $headers = $this->auth->authorization($url, $body, 'application/x-www-form-urlencoded');
        $ret = Client::post($url, $body, $headers);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }

    public static function buildBatchCopy($source_bucket, $key_pairs, $target_bucket, $force)
    {
        return self::twoKeyBatch('/copy', $source_bucket, $key_pairs, $target_bucket, $force);
    }


    public static function buildBatchRename($bucket, $key_pairs, $force)
    {
        return self::buildBatchMove($bucket, $key_pairs, $bucket, $force);
    }


    public static function buildBatchMove($source_bucket, $key_pairs, $target_bucket, $force)
    {
        return self::twoKeyBatch('/move', $source_bucket, $key_pairs, $target_bucket, $force);
    }


    public static function buildBatchDelete($bucket, $keys)
    {
        return self::oneKeyBatch('/delete', $bucket, $keys);
    }


    public static function buildBatchStat($bucket, $keys)
    {
        return self::oneKeyBatch('/stat', $bucket, $keys);
    }

    public static function buildBatchDeleteAfterDays($bucket, $key_day_pairs)
    {
        $data = array();
        foreach ($key_day_pairs as $key => $day) {
            array_push($data, '/deleteAfterDays/' . \Qiniu\entry($bucket, $key) . '/' . $day);
        }
        return $data;
    }

    public static function buildBatchChangeMime($bucket, $key_mime_pairs)
    {
        $data = array();
        foreach ($key_mime_pairs as $key => $mime) {
            array_push($data, '/chgm/' . \Qiniu\entry($bucket, $key) . '/mime/' . base64_encode($mime));
        }
        return $data;
    }

    public static function buildBatchChangeType($bucket, $key_type_pairs)
    {
        $data = array();
        foreach ($key_type_pairs as $key => $type) {
            array_push($data, '/chtype/' . \Qiniu\entry($bucket, $key) . '/type/' . $type);
        }
        return $data;
    }

    private static function oneKeyBatch($operation, $bucket, $keys)
    {
        $data = array();
        foreach ($keys as $key) {
            array_push($data, $operation . '/' . \Qiniu\entry($bucket, $key));
        }
        return $data;
    }

    private static function twoKeyBatch($operation, $source_bucket, $key_pairs, $target_bucket, $force)
    {
        if ($target_bucket === null) {
            $target_bucket = $source_bucket;
        }
        $data = array();
        $forceOp = "false";
        if ($force) {
            $forceOp = "true";
        }
        foreach ($key_pairs as $from_key => $to_key) {
            $from = \Qiniu\entry($source_bucket, $from_key);
            $to = \Qiniu\entry($target_bucket, $to_key);
            array_push($data, $operation . '/' . $from . '/' . $to . "/force/" . $forceOp);
        }
        return $data;
    }
}
