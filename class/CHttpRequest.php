<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CHttpRequest
 *
 * @author tkms
 */
abstract class CHttpRequest {
    //put your code here
    public $mMethod;
    abstract function dumpVar();
    
}

class CHttpRequestPost extends CHttpRequest {    
    public function __construct() {
        $this->mMethod = "POST";
        if(!$_POST){
            exit("$this->mMethod connection rejected.");
        }
    }
    public function dumpVar() {
        echo print_r($_POST);
    }
}
