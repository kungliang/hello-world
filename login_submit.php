<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//    print_r($_SERVER); 
    //error_reporting(E_ALL);
    session_start();
    include "class/CUser.php";
    include "class/CDatabase.php";
    if($_POST){
        $oDb = new CDatabase();
        $sUser = $_POST["userName"];
        $sPassword = $_POST["userPassword"];
        $oUser = $oDb->isLegalUser($sUser, $sPassword);
        if($oUser){
            $_SESSION["USER_ID"] = $oUser->mId;
            $_SESSION["USER_NAME"] = $oUser->mName;                    
            $_SESSION["USER_SITE"] = $oUser->mSite; 
            $_SESSION["USER_MAIL_ADDRESS"] = $oUser->mMail;
            $_SESSION["USER_PHONE_NUMBER"] = $oUser->mPhone;
            $_SESSION["USER_DIVISION"] = $oUser->mDivision;
            $_SESSION["USER_EMPLOYEE_ID"] = $oUser->mEmployeeId;
            $_SESSION["USER_ERR"] = NULL;
            header("Location:home.php");
        }else{
            $errMessage="User not found!";
            $_SESSION["USER_ERR"] = $errMessage;
            header("Location:login.php");
        }
    }
?>

