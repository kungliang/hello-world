<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CLicense
 *
 * @author tkms
 */
class CLicense {
    //put your code here
    const LIC_ACTIVE=1;
    const LIC_INACTIVE=0;
    public $mId;
    public $mType;
    public $mOwner;
    public $mQuantity;
    public $mLicenseContent;
    public $mStartDate;
    public $mEndDate;
    public $mActive;
    public $mCreateTime;
    private $mLicenseLength;
    public static function GenerateLicense($sLength){
        $licenseString="";
        $randomNumber=0;
        while(strlen($licenseString)<=$sLength){
            $randomNumber=rand(48,122);
            if($randomNumber>=48 && $randomNumber<=57) { //0-9
                $licenseString.=chr($randomNumber);
            }
            if($randomNumber>=65 && $randomNumber<=90) { //A-Z
                $licenseString.=chr($randomNumber);
            }
        }       
        return $licenseString;
    }
    public function __construct($sId,  CLicenseType $oLicenseType, CUser $oOwner, $sQuantity, $sLicenseContent, $sStartDate, $sEndDate, $sActive ) {
        $this->mId = $sId;
        $this->mType = $oLicenseType;
        $this->mOwner = $oOwner;
        $this->mQuantity = $sQuantity;
        $this->mLicenseContent = $sLicenseContent;
        $this->mStartDate = $sStartDate;
        $this->mEndDate = $sEndDate;
        $this->mActive = $sActive;
        $this->mLicenseLength = 64;
        if(!strlen($this->mLicenseContent)){
            $this->mLicenseContent = $this->getLicenseContent();
        }
    }
    
    public function getLicenseContent(){
        return CLicense::GenerateLicense($this->mLicenseLength);
//        $licenseContent="";
//        $randomNumber=0;
//        while(strlen($licenseContent)<=$this->mLicenseLength){
//            $randomNumber=rand(48,122);
//            if($randomNumber>=48 && $randomNumber<=57) { //0-9
//                $licenseContent.=chr($randomNumber);
//            }
//            if($randomNumber>=65 && $randomNumber<=90) { //A-Z
//                $licenseContent.=chr($randomNumber);
//            }
//        }
//        return $licenseContent;
//        return "HMRUEERF8XVD73TJC8ZIFAW6NEMYQ2CRIVASDBI02CSE2OQPHONN8KHX5HMFB7F09";
    }
    public function isOutOfDate(){
        return strtotime(date("Y-m-d"))>strtotime($this->mEndDate);
    }
    public function getDueDays(){
        return (strtotime($this->mEndDate) - strtotime(date("Y-m-d")))/86400;
    }
}

class CLicenseType {
    public $mId;
    public $mName;
    public $mParameter;
    public $mComment;
    
    public function __construct($sId, $sName, $sParameter, $sComment) {
        $this->mId = $sId;
        $this->mName = $sName;
        $this->mParameter = $sParameter;
        $this->mComment = $sComment;
    }
}

class CMacAddress {
    //put your code here
    public $mId;
    public $mAddress;
    
    public function __construct($sId,$sAddress) {
        $this->mId = $sId;
        $this->mAddress = $sAddress;
    }
}

