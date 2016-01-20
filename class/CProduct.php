<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CProduct
 *
 * @author tkms
 */
class CProduct {
    //put your code here
    public $mId;
    public $mType;
    public $mSubtype;
    public $mName;
    public $mModules;
    public $mActive;
    
    public function __construct($sId,$sType,$sName) {
        $this->mId = $sId;
        $this->mType = $sType;
        $this->mName = $sName;
        $this->mModules = array();
        $this->mActive=1;
    }
    public function addProductModule(CProductModule $oModule){
        array_push($this->mModules, $oModule);
    }
    public function setProductSubtype(CProductSubtype $oProductSubtype){
        $this->mSubtype = $oProductSubtype;
    }
    public function setActive($active){
        if(strcmp("ACTIVE", $active)==0){
            $this->mActive = 1;
        }else{
            $this->mActive = 0;
        }
    }
}
class CLicensedProduct extends CProduct {
    public $mSelectedModules;
    public function __construct(CProduct $oProduct) {
        parent::__construct($oProduct->mId, $oProduct->mType, $oProduct->mName);
        $this->mModules = $oProduct->mModules;
        $this->mSelectedModules = array();
    }
    public function addLicensedProductModule(CProductModule $oModule){
        array_push($this->mSelectedModules, $oModule);
    }
    public function isLicensedProductModule(CProductModule $oModule){
        foreach($this->mSelectedModules as $oSelectedModule){
            if($oSelectedModule->mId == $oModule->mId){
                return true;
            }
        }
        return false;
    }
    public function getLicensedProductModule($sModuleName){
        foreach($this->mSelectedModules as $oSelectedModule){
            if($oSelectedModule->mName == $sModuleName){
                return $oSelectedModule;
            }
        }
    }
}


class CProductModule {
    public $mId;
    public $mName;
    public $mProduct;
    public $mActive;
    public function __construct($sId,$sName) {
        $this->mId = $sId;
        $this->mName = $sName;
        $this->mActive= 1;
    }
    public function setProduct(CProduct $oProduct){
        $this->mProduct = $oProduct;
    }
    public function setActive($active){
        if(strcmp("ACTIVE", $active)==0){
            $this->mActive = 1;
        }else{
            $this->mActive = 0;
        }
    }
}
class CProductSubtype {
    public $mId;
    public $mName;
    public $mComment;
    public $mProducts;
    public $mType;
    
    public function __construct($sId,$sName,$sComment) {
        $this->mId = $sId;
        $this->mName = $sName;
        $this->mComment = $sComment;
        $this->mProducts = array();
    }
    public function addProduct(CProduct $oProduct){
        array_push($this->mProducts, $oProduct);
    }  
    public function getId(){
        return $this->mId;
    }
    public function setType(CProductType $oProductType){
        $this->mType = $oProductType;
    }
}
class CProductType {
    public $mId;
    public $mName;
    public $mComment;
    public function __construct($sId,$sName,$sComment) {
        $this->mId = $sId;
        $this->mName = $sName;
        $this->mComment = $sComment;
    }    
    public function getId(){
        return $this->mId;
    }
}
class CProductOutline {
    public $mId;
    public $mContent;
    public $mProduct;
    public function __construct($sId, CProduct $oProduct, $sContent) {
        $this->mId = $sId;
        $this->mProduct = $oProduct;
        $this->mContent = $sContent;
    }    
}
class CProductAttachment {
    const APV_ACCEPT = 1;
    const APV_REJECT = 0;
    public $mId;
    public $mProduct;
    public $mFileName;
    public $mFileType;
    public $mFileSize;
    public $mFileContent;
    public $mCreateTime;
    public function __construct($sId, CProduct $oProduct, $sFileName, $sFileType, $sFileSize, $sFileContent,$sCreateTime) {
        $this->mId = $sId;
        $this->mProduct = $oProduct;
        $this->mFileName = $sFileName;
        $this->mFileType = $sFileType;
        $this->mFileSize = $sFileSize;
        $this->mFileContent = $sFileContent;
        if(!$sCreateTime){
            $this->mCreateTime = date("Y-m-d H:i:s") ;
        }else{
            $this->mCreateTime = $sCreateTime;
        }
    }
}