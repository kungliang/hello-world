<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CUser
 *
 * @author tkms
 */
class CUser {
    //put your code here
    public $mId;
    public $mEmployeeId;
    public $mName;
    private $mPassword;
    public $mSite;
    public $mDivision;
    public $mPhone;
    public $mMail;
    public function __construct($nId, $sEmployeeId, $sName, $sPassword, $sSite, $sDivision, $sPhone, $sMail) {
        $this->mId=$nId;       
        $this->mEmployeeId=$sEmployeeId;
        $this->mName=$sName;
        $this->mPassword=$sPassword;
        $this->mSite=$sSite;
        $this->mDivision=$sDivision;
        $this->mPhone=$sPhone;
        $this->mMail=$sMail;
    }
}
