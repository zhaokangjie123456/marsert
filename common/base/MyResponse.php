<?php
namespace common\base;


use yii\web\Response;

class MyResponse extends Response
{
    public $format = self::FORMAT_JSON;
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * 重新配置返回值code
     * @return int
     */
    public function getCode()
    {
        $statusCode = parent::getStatusCode();
        $data = $this->data;
        return isset($data['code']) && $data['code'] ? $data['code'] : ($statusCode === 200 ? 0 : $statusCode);
    }

    /**
     * @return string 重新设置返回信息
     */
    public function getMsg()
    {
        $data = $this->data;
        return $data["message"]??$this->statusText;
    }

    private function nullToString(&$data)
    {
        if (is_array($data) && !empty($data)) {
            $handleData = function ($data) use (&$handleData) {
                foreach ($data as $k => &$v) {
                    if (is_array($v) && !empty($v)) {
                        $handleData[$v];
                    } else {
                        $v = is_null($v) ? '' : $v;
                    }
                }

            };
        }
    }

    public function getData()
    {
        $data = $this->data;

        if (YII_DEBUG)
            return $data;
        else
            return parent::getStatusCode() != 200 ? '' : $data;
    }
}