<?php

/**
 * Logger class
 *
 * logs all errors and warnings with their  details and exact time in files on the server
 * supports only one
 *
 * @version 1.0
 */
class Logger
{
    /**
     * Summary of $logger
     * @var Logger
     * @access private
     */
    private static $logger=null;
    private $writer=null;
    static function getLogger(){
        if(Logger::$logger == null){
            Logger::$logger = new Logger;
        }
        return Logger::$logger;
    }

    private function __construct(){
        $this->writer = fopen(LOGDIR.date('y-m-d').'.log','a');
        set_exception_handler(function($exception) {
            $this->log("Exception:".$exception,true);
        });
        set_error_handler(function($errno , $errstr , $errfile , $errline) {
            $this->log("PHP error no. ".$errno." in:".$errfile." line:".$errline." Error message:".$errstr);
        });
    }

    function __destruct(){
        fclose($this->writer);
    }

    private function log($message, $fatal=false){
        fwrite($this->writer,date('H:i:s '.$message.'\n'));
        if($fatal){
            echo json_encode(array("fatal error"=>true));
            die();
        }
    }

}