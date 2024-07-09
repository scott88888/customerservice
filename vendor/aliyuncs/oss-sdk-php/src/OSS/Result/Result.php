<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Http\ResponseCore;


/**
 * Class Result, 操作结果類的基類，不同的請求在處理返回資料的时候有不同的逻辑，
 * 具体的解析逻辑推迟到子類實現
 *
 * @package OSS\Model
 */
abstract class Result
{
    /**
     * Result constructor.
     * @param $response ResponseCore
     * @throws OssException
     */
    public function __construct($response)
    {
        if ($response === null) {
            throw new OssException("raw response is null");
        }
        $this->rawResponse = $response;
        $this->parseResponse();
    }

    /**
     * 取得requestId
     *
     * @return string
     */
    public function getRequestId()
    {
        if (isset($this->rawResponse) &&
            isset($this->rawResponse->header) &&
            isset($this->rawResponse->header['x-oss-request-id'])
        ) {
            return $this->rawResponse->header['x-oss-request-id'];
        } else {
            return '';
        }
    }

    /**
     * 得到返回資料，不同的請求返回資料格式不同
     *
     * $return mixed
     */
    public function getData()
    {
        return $this->parsedData;
    }

    /**
     * 由子類實現，不同的請求返回資料有不同的解析逻辑，由子類實現
     *
     * @return mixed
     */
    abstract protected function parseDataFromResponse();

    /**
     * 操作是否成功
     *
     * @return mixed
     */
    public function isOK()
    {
        return $this->isOk;
    }

    /**
     * @throws OssException
     */
    public function parseResponse()
    {
        $this->isOk = $this->isResponseOk();
        if ($this->isOk) {
            $this->parsedData = $this->parseDataFromResponse();
        } else {
            $httpStatus = strval($this->rawResponse->status);
            $requestId = strval($this->getRequestId());
            $code = $this->retrieveErrorCode($this->rawResponse->body);
            $message = $this->retrieveErrorMessage($this->rawResponse->body);
            $body = $this->rawResponse->body;

            $details = array(
                'status' => $httpStatus,
                'request-id' => $requestId,
                'code' => $code,
                'message' => $message,
                'body' => $body
            );
            throw new OssException($details);
        }
    }

    /**
     * 尝试从body中取得錯誤Message
     *
     * @param $body
     * @return string
     */
    private function retrieveErrorMessage($body)
    {
        if (empty($body) || false === strpos($body, '<?xml')) {
            return '';
        }
        $xml = simplexml_load_string($body);
        if (isset($xml->Message)) {
            return strval($xml->Message);
        }
        return '';
    }

    /**
     * 尝试从body中取得錯誤Code
     *
     * @param $body
     * @return string
     */
    private function retrieveErrorCode($body)
    {
        if (empty($body) || false === strpos($body, '<?xml')) {
            return '';
        }
        $xml = simplexml_load_string($body);
        if (isset($xml->Code)) {
            return strval($xml->Code);
        }
        return '';
    }

    /**
     * 根據返回http狀態碼判断，[200-299]即认為是OK
     *
     * @return bool
     */
    protected function isResponseOk()
    {
        $status = $this->rawResponse->status;
        if ((int)(intval($status) / 100) == 2) {
            return true;
        }
        return false;
    }

    /**
     * 返回原始的返回資料
     *
     * @return ResponseCore
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * 标示請求是否成功
     */
    protected $isOk = false;
    /**
     * 由子類解析过的資料
     */
    protected $parsedData = null;
    /**
     * 存放auth函數返回的原始Response
     *
     * @var ResponseCore
     */
    protected $rawResponse;
}