<?php

namespace common\base;
use yii\web\HttpException;

class ErrorException extends HttpException{

    public $statusCode;
    /**
     * Constructor.
     * @param string $message PDO error message
     * @param array $errorInfo PDO error info
     * @param int $code PDO error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message,$code = 1, $statusCode = 200 , \Exception $previous = null)
    {
        if(is_array($message) || is_object($message)){
            $message = is_object($message)?(array)$message:$message;
            $message = $this->getMsg($message);
        }
        parent::__construct($statusCode ,$message, $code, $previous);
    }

    /**
     * 取出数组第一条是文字
     * @param $data
     * @return string
     */
    private function getMsg($data){
        if(is_string($data)){
            return $data;
        }else{
            if(is_array($data)){
                return $this->getMsg(array_shift($data));
            }else{
                return $data;
            }
        }
    }
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'application Exception';
    }

}