<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CCertificate
 *
 * @author tkms
 */
interface ICertificate {
    public function doCertificate();
}

class CCertificateType {
    public $mId;
    public $mName;
    public function __construct($sId, $sName) {
        $this->mId = $sId;
        $this->mName = $sName;
    }
}
class CCertificateError extends Exception {
    public $mId;
    public $mCode;
    public $mComment;
    public $mMessage;
    public $mXMLData;
    private $mException;
    public function __construct($sId, $sCode, Exception $exception) {
        $this->mId = $sId;
        $this->mCode = $sCode;
        $this->mComment="";
        $this->mException = $exception;
        $this->mMessage = $this->mException->getMessage();
    }
    public function getErrorMessage(){
        $sMessage = "";
        if($this->mMessage){
            $sMessage .= $this->mMessage;
        }else{
            $sMessage .= $this->mComment;
        }
        return $sMessage;
    }
}

abstract class CTTDDCertification implements ICertificate {
    const TAG_CERT_REQUEST_ROOT = "ttdd_certification_request";
    const TAG_CERT_RESPONSE_ROOT = "ttdd_certification_response";
    const TAG_CERT_PRODUCT = "product_name";
    const TAG_CERT_PRODUCT_MODULE = "product_module";
    const TAG_CERT_FAST_LICENSE = "fast_license";
    const TAG_CERT_IPADDR = "ip_address";
    const TAG_CERT_MACADDR = "mac_address";
    const TAG_CERT_LICENSE = "license";
    const TAG_CERT_TYPE = "type";
    const TAG_CERT_RESULT = "result";
    const TAG_CERT_DURATION = "duration";
    const TAG_CERT_TSTAMP = "time_stamp";
    const TAG_CERT_EXPIRE_TIME = "expire_time";
    const TAG_CERT_EXPIRE_COUNT = "expire_count";
    const TAG_CERT_ERROR_MSG = "error_message";
    
    protected $mXMLString;
    protected $mXMLDom;
    protected $mDb;
    protected $mError;
    protected $mRespXMLDom;
    public function __construct() {
        $this->mXMLDom = new DOMDocument();
        $this->mRespXMLDom = new DOMDocument("1.0");
        $this->mDb = new CDatabase();
    }
    public function loadXML($xmlString){
        $this->mXMLString = $xmlString;
        if($this->mXMLString)
            if(!$this->mXMLDom->loadXML($this->mXMLString))
                $this->mXMLDom = null;
    }
    protected function isNodeExist($nodeName){
        return $this->mXMLDom->getElementsByTagName($nodeName)->length; // 
    }
    protected function isEmptyNode($nodeName){
        return $this->mXMLDom->getElementsByTagName($nodeName)->item(0)->nodeValue;
    }
    protected function checkXMLFormat(){
        if(!$this->mXMLString)
            throw new CCertificateError (0, "E101", new Exception());
        if(!$this->mXMLDom)
            throw new CCertificateError (0, "E102", new Exception());
    }    
    protected function checkXMLNodes($aryNodeName){
        foreach($aryNodeName as $nodeName){
            if(!($this->isNodeExist($nodeName))) 
                throw new CCertificateError (0, "E103", new Exception ("Tag \"$nodeName\" not exist."));
            if(!$this->isEmptyNode($nodeName))
                throw new CCertificateError (0, "E104", new Exception ("Tag \"$nodeName\" is empty."));
        }
    }

    abstract protected function saveXML();
    protected function saveError(CCertificateError $oCertificateError){
        $this->mError = $this->mDb->getCertificateError($oCertificateError);
        $this->mError->mXMLData = $this->mXMLString;
        $this->mDb->addFullCertification($this);
        $this->mDb->bindCertificationError($this,$this->mError);
    }


    public function getResultXML(){
        $this->saveXML();
        return $this->mRespXMLDom->saveXML();
    }
}

class CFullTTDDCertification extends CTTDDCertification {
    public $mId; //certificate_id
    public $mType;
    public $mLicense;
    public $mProduct;
    public $mModule;
    public $mMACAddress;
    public $mIPAddress;
    public $mResult;
    public $mDuration;
    public $mTimeStamp;
    public function __construct() {
        parent::__construct();
        $this->mType = $this->mDb->getCertificateTypeByName("full");
        $this->mTimeStamp = date("Y-m-d H:i:s");
        $this->mResult = false;
        $this->mDuration = 0;
    }
    protected function checkLicense(){
        $tagName = parent::TAG_CERT_LICENSE;
        $sLicenseContent = $this->mXMLDom->getElementsByTagName($tagName)->item(0)->nodeValue;
        $oLicense = $this->mDb->getLicenseByContent($sLicenseContent);
        if(!$oLicense) 
             throw new CCertificateError (0, "E201", new Exception ());
        /*
        if($oLicense->isOutOfDate()) 
             throw new CCertificateError (0, "E205", new Exception ());*/
//        throw new Exception ("Error: License not exist.");
        $this->mLicense = $oLicense;
    }
    protected function checkProduct(){
        $tagName = parent::TAG_CERT_PRODUCT;
        $sProductName = $this->mXMLDom->getElementsByTagName($tagName)->item(0)->nodeValue;
        $oProduct = $this->mDb->getProductByLicense($this->mLicense);
        if($oProduct->mName != $sProductName) 
            throw new CCertificateError (0, "E202", new Exception ());
//            throw new Exception ("Error: Not licensed product.");
        $this->mProduct = $oProduct;
    }
    protected function checkProductModule(){
        $tagName = parent::TAG_CERT_PRODUCT_MODULE;
        $sProductModuleName = $this->mXMLDom->getElementsByTagName($tagName)->item(0)->nodeValue;
        $oModule = $this->mProduct->getLicensedProductModule($sProductModuleName);
        if(!$oModule) 
            throw new CCertificateError (0, "E203", new Exception ());
//            throw new Exception ("Error: Not licensed module.");
        $this->mModule = $oModule;
        
    }
    protected function checkMacAddress(){
        $tagName = parent::TAG_CERT_MACADDR;
        $sMacAddress = $this->mXMLDom->getElementsByTagName($tagName)->item(0)->nodeValue;
        $oMacAddress = $this->mDb->isMacAddressExist(new CMacAddress(0,$sMacAddress));
        $aryMacAddress = $this->mDb->getMacAddressByLicense($this->mLicense);
        if(!is_int(array_search($oMacAddress, $aryMacAddress)))
                throw new CCertificateError (0, "E204", new Exception ());
//                throw new Exception ("Error: Not licensed mac address."); 
        $this->mMACAddress = $oMacAddress;
         
    }
    protected function checkIPAddress(){
        $tagName = parent::TAG_CERT_IPADDR;
        $sIPAddress = $this->mXMLDom->getElementsByTagName($tagName)->item(0)->nodeValue;
        $this->mIPAddress = $sIPAddress;
    }
            
    public function doCertificate() {
        $oTimer = new CTimer();
        $oTimer->start();
        $aryRequiredTagName = array();
        array_push($aryRequiredTagName, parent::TAG_CERT_TYPE);
        array_push($aryRequiredTagName, parent::TAG_CERT_LICENSE);
        array_push($aryRequiredTagName, parent::TAG_CERT_PRODUCT);
        array_push($aryRequiredTagName, parent::TAG_CERT_PRODUCT_MODULE);
        array_push($aryRequiredTagName, parent::TAG_CERT_MACADDR);
        try{
            //check xml tags and contents.
            $this->checkXMLFormat();
            $this->checkXMLNodes($aryRequiredTagName);
            $this->checkLicense();
            $this->checkProduct();
            $this->checkProductModule();
            $this->checkMacAddress();
            $this->checkIPAddress();
            $oTimer->stop();
            $this->mResult = true;
            $this->mDuration = $oTimer->getDuration();
            $this->mDb->addFullCertification($this);
        } catch (CCertificateError $oError) {
            $oTimer->stop();
            $this->mResult = false;
            $this->mDuration = $oTimer->getDuration();
            $this->saveError($oError);
        }
    }

    protected function saveXML() {
        $xmlDom = $this->mRespXMLDom;
        $tagName = parent::TAG_CERT_RESPONSE_ROOT;
        $ndRoot = $xmlDom->createElement($tagName);
        $tagName = parent::TAG_CERT_TYPE;
        $ndType = $xmlDom->createElement($tagName);
        $ndType->appendChild($xmlDom->createTextNode($this->mType->mName));
        $tagName = parent::TAG_CERT_RESULT;
        $ndResult = $xmlDom->createElement($tagName);
        $ndResult->appendChild($xmlDom->createTextNode(($this->mResult?"pass":"fail")));
        $tagName = parent::TAG_CERT_DURATION;
        $ndDuration = $xmlDom->createElement($tagName);
        $ndDuration->appendChild($xmlDom->createTextNode($this->mDuration));
        $tagName = parent::TAG_CERT_TSTAMP;
        $ndTimeStamp = $xmlDom->createElement($tagName);
        $ndTimeStamp->appendChild($xmlDom->createTextNode($this->mTimeStamp));
        
        if($this->mError){
            $tagName = parent::TAG_CERT_ERROR_MSG;
            $ndErrMessage = $xmlDom->createElement($tagName);
            $ndErrMessage->appendChild($xmlDom->createTextNode($this->mError->getErrorMessage()));
        }
        
        $ndRoot->appendChild($ndType);
        $ndRoot->appendChild($ndResult);
        $ndRoot->appendChild($ndDuration);
        $ndRoot->appendChild($ndTimeStamp);
        if($this->mError){
            $ndRoot->appendChild($ndErrMessage);
        }
        
        $xmlDom->appendChild($ndRoot);
    }


}

class CLazyTTDDCertification extends CFullTTDDCertification {
    protected $mHours = 1;
    public $mExpireTime;
    public function __construct() {
        parent::__construct();
        $this->mType = $this->mDb->getCertificateTypeByName("lazy");
        $this->mExpireTime = date("Y-m-d H:i:s", strtotime("+$this->mHours hour"));
    }
    protected function saveXML(){ //override
        parent::saveXML();
        if(!$this->mError){
            $xmlDom = $this->mRespXMLDom;
            $tagName = parent::TAG_CERT_EXPIRE_TIME;
            $ndExpireTime = $xmlDom->createElement($tagName);
            $ndExpireTime->appendChild($xmlDom->createTextNode($this->mExpireTime));        
            $xmlDom->documentElement->appendChild($ndExpireTime);
        }
    }
}


class CFastTTDDCertification extends CLazyTTDDCertification {
    private $mFastLicenseLength;
    public $mFastLicense;
    public $mExpireCount;
    public $mActive;
    public $mIsExpired;
    public function __construct() {
        parent::__construct();
        $this->mType = $this->mDb->getCertificateTypeByName("fast");
        $this->mFastLicense = "";
        $this->mExpireCount = 0;
        $this->mFastLicenseLength = 16;
    }
    
    protected function saveXML(){ //override
        parent::saveXML();
        if(!$this->mError){
            $xmlDom = $this->mRespXMLDom;
            $tagName = parent::TAG_CERT_EXPIRE_COUNT;
            $ndExpireCount = $xmlDom->createElement($tagName);
            $ndExpireCount->appendChild($xmlDom->createTextNode($this->mExpireCount));   
            $tagName = parent::TAG_CERT_FAST_LICENSE;
            $ndFastLicense = $xmlDom->createElement($tagName);
            $ndFastLicense->appendChild($xmlDom->createTextNode($this->mFastLicense));           
            $xmlDom->documentElement->appendChild($ndExpireCount);
            $xmlDom->documentElement->appendChild($ndFastLicense);
        }
    }    
    private function getFastLicense(){ 
        return CLicense::GenerateLicense($this->mFastLicenseLength);
    }
    
    public function doCertificate() { //override
        /*
         * The fast mode only checks lic_certificate_fast_rte table if fast_license is valid.
         */
        $nodeName = parent::TAG_CERT_FAST_LICENSE;
        $oTimer = new CTimer();
        $oTimer->start();
        try{
            /* fast check process */
            $this->checkXMLFormat();
            if($this->isNodeExist($nodeName))
                $this->mFastLicense = $this->mXMLDom->getElementsByTagName($nodeName)->item(0)->nodeValue;
            /* if fast license is available from XML then do fast certification */
            if($this->mFastLicense){
                $oFastTTDDCertification = $this->mDb->getFastRTECertification($this->mFastLicense);
                /* The certificate result should be decided by compound conditions. */
                /* Only the fast license expired will cause fast certification error 
                 * Once a fast license certification failed, it will turns to full mode automatically 
                 * and generates a new fast license to be used on next certification. 
                 */
                if($oFastTTDDCertification){
                    if($oFastTTDDCertification->mIsExpired=="1")
                        $oFastTTDDCertification->mResult = false;
                    if($oFastTTDDCertification->mIsExpired=="0")
                        $oFastTTDDCertification->mResult = true;
    //                echo $oFastTTDDCertification->mIsExpired;
    //                echo "EXP:".$oFastTTDDCertification->mIsExpired ;
    //                echo "RES:".$oFastTTDDCertification->mResult ;//                
    //                exit();
                    if($oFastTTDDCertification->mResult){
                        $this->mExpireTime = $oFastTTDDCertification->mExpireTime;
                        $this->mResult = $oFastTTDDCertification->mResult;
                        $this->mDb->addFastRTECertificationHistory($oFastTTDDCertification);
                        $oTimer->stop();
                        $oFastTTDDCertification->mDuration = $oTimer->getDuration();
                        $this->mDuration = $oFastTTDDCertification->mDuration;
                    }else{
                        $this->mDb->setFastRTECertificationInactive($oFastTTDDCertification); 
                        $oFastTTDDCertification=null;
                    }
                }
            }
        
            if(!$this->mFastLicense || !$oFastTTDDCertification){ //do full and generate a fast license to rte table for next use
                $this->mFastLicense = $this->getFastLicense();
                parent::doCertificate();
                $this->mDb->addFastRTECertification($this); // create new fast license
            }

        } catch (CCertificateError $oError) {
            $oTimer->stop();
            $this->mResult = false;
            $this->mDuration = $oTimer->getDuration();            
            $this->saveError($oError);
        }
    }    
}
