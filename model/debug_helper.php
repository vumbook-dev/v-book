<?php
class Helper {
    private $switch;
    public $result;
    public $errorType;
    public $mode;
    public $errorMSG;
    private $getError;
    public function __construct($option){
        $this->switch = $option;
    }

    public function failSafe($data, $line, $phpfilename){
        //Check If Message Pop Up is enable
        if($this->switch){
            //Set debug mode to enable
            $this->mode = "debug_enable";
            //Perform Checking Error
            if(strlen($data) < 240){
                $this->result = true;
                $this->errorMSG = "Fail Safe Debug Mode is Enabled: <br> Error found in /model/{$phpfilename} on line {$line}!";
                $this->errorType = "danger";
            }else{
                $this->result = false;
                $this->errorMSG = "No Error Found.";
                $this->errorType = "success";
            }            
        }else{
            //Set debug mode to disable
            $this->mode = "debug_disable";
            //Perform Checking Error
            if(strlen($data) < 240){
                $this->result = true;
                $this->errorMSG = "Something went wrong, please reload your browser!";
                $this->errorType = "secondary";
            }else{
                $this->result = false;
                $this->errorMSG = "No Error Found.";
                $this->errorType = "success";
            }
        }
    }
}