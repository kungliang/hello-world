<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CSession
 *
 * @author tkms
 */
class CSession {
    //put your code here
    public function __construct() {
        session_start();
        date_default_timezone_set("Asia/Taipei");
        if(!$_SESSION){
            $location = "/TSMS/login.php";
            header("location:$location");
        }
    }
}
new CSession();
