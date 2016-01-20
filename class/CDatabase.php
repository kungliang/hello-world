<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CDatabase
 *
 * @author tkms
 */
class CDatabase {

    //put your code here
    private $mConnection;
    private $mResultSet;

    /*
     *  private functions
     */

    private function xGetDbError($sql) {
        $sErrHTML = "<table border=1 cellspacing=0 cellpadding=0>";
        $sErrHTML .= " <tr><th>mysql error number</th><td> ";
        $sErrHTML .= mysqli_errno($this->mConnection) . "</tr>";
        $sErrHTML .= " <tr><th>mysql error message</th><td>";
        $sErrHTML .= mysqli_error($this->mConnection) . "</tr>";
        $sErrHTML .= "<tr><th>SQL String:</th><td> " . $sql . "</td></tr></table>";
        exit($sErrHTML);
    }

    private function xQuery($sql) {
        /* run query string and return number of rows */
        
        $this->mResultSet = mysqli_query($this->mConnection, $sql) or die($this->xGetDbError($sql));
        return mysqli_num_rows($this->mResultSet);
    }
    private function xQueryEx($sql) {
        /* run query string and return number of rows */
        try{ 
            $this->mResultSet = mysqli_query($this->mConnection, $sql) or die($this->xGetDbError($sql));
            return mysqli_num_rows($this->mResultSet);
         }catch(Exception $e){
           echo  'Caught exception: ',  $e->getMessage(), "\n";
           throw new Exception($e->getMessage());
       } 
    }
    private function xInsert($sql) {
        /* run query string and return number of rows */
        mysqli_query($this->mConnection, $sql) or die($this->xGetDbError($sql));
        return mysqli_insert_id($this->mConnection);
    }
    private function xInsertEx($sql) {
         try{ 
        mysqli_query($this->mConnection, $sql);
        return mysqli_insert_id($this->mConnection);
         }catch(Exception $e){
           echo  'Caught exception: ',  $e->getMessage(), "\n";
           throw new Exception($e->getMessage());
       } 
    }
    private function xReplaceEx($sql) {
        /* run query string and return number of rows */
       try{ 
         mysqli_query($this->mConnection, $sql);
         return mysqli_insert_id($this->mConnection);
       }catch(Exception $e){
           echo  'Caught exception: ',  $e->getMessage(), "\n";
           throw new Exception($e->getMessage());
       } 
    }
    private function xExecute($sql) {
        /* run sql string and no return */
        mysqli_query($this->mConnection, $sql) or die($this->xGetDbError($sql));
//        return mysqli_affected_rows($this->mResultSet);
    }
    private function xExecuteEx($sql) {
        /* run sql string and no return */
         try{ 
        mysqli_query($this->mConnection, $sql) or die($this->xGetDbError($sql));
        }catch(Exception $e){
           echo  'Caught exception: ',  $e->getMessage(), "\n";
           throw new Exception($e->getMessage());
       } 
//        return mysqli_affected_rows($this->mResultSet);
    }
    /* constructor and destructor */

    public function __construct() {
        $sHost = "localhost";
        $sUser = "";
        $sPassword = "";
        $sDbname = "";
        $this->mConnection = mysqli_connect($sHost, $sUser, $sPassword, $sDbname) or die($this->xGetDbError(""));
        /* change character set to utf8 */
        /*if (!mysqli_set_charset($this->mConnection, "utf8")) {
            printf("Error loading character set utf8: %s\n", mysqli_error($this->mConnection));
        } else {
            printf("Current character set: %s\n", mysqli_character_set_name($this->mConnection));
        }*/
    }

    public function __destruct() {
        if ($this->mConnection) {
            mysqli_close($this->mConnection);
        }
    }

    /* public users-related functions */

    public function isLegalUser($sUser, $sPassword) {
        /* validate user and return a user object */
        $sql = " select id,employee_id,name,password,site,division,phone,mail ";
        $sql .= " from ppo_users_list ";
        $sql .= " where name='$sUser' and password='$sPassword' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $nId = $row[0];
            $sEmployeeId = $row[1];
            $sName = $row[2];
            $sPassword = $row[3];
            $sSite = $row[4];
            $sDivision = $row[5];
            $sPhone = $row[6];
            $sMail = $row[7];
            return new CUser($nId, $sEmployeeId, $sName, $sPassword, $sSite, $sDivision, $sPhone, $sMail);
        } else {
            return null;
        }
    }

    public function getAllUser() {
        $aryUser = array();
        $sql = "select id from ppo_users_list order by id";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $oUser = $this->getUserById($sId);
                array_push($aryUser, $oUser);
            }
        }
        if (sizeof($aryUser)) {
            return $aryUser;
        } else {
            return null;
        }
    }

    public function getUserById($sId) {
        $sql = " select id,employee_id,name,'' password,site,division,phone,mail ";
        $sql .= " from ppo_users_list ";
        $sql .= " where id=$sId ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $nId = $row[0];
            $sEmployeeId = $row[1];
            $sName = $row[2];
            $sPassword = $row[3];
            $sSite = $row[4];
            $sDivision = $row[5];
            $sPhone = $row[6];
            $sMail = $row[7];
            return new CUser($nId, $sEmployeeId, $sName, $sPassword, $sSite, $sDivision, $sPhone, $sMail);
        } else {
            return null;
        }
    }

    /* public product-related functions */

    public function getAllProductType() {
        $sql = "select id from pdt_product_type";
        if ($this->xQuery($sql)) {
            $aryProductType = array();
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $oProductType = $this->getProductType($sId);
                array_push($aryProductType, $oProductType);
            }
            if (sizeof($aryProductType)) {
                return $aryProductType;
            } else
                return null;
        }
    }
    public function getProductSubtypes($sType) {
        $sql = "select id from pdt_product_subtype where active=1 and type_id in";
        $sql .= "(select id from pdt_product_type where name='$sType')";
        if ($this->xQuery($sql)) {
            $aryProductSubtype = array();
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $oProductSubtype = $this->getProductSubType($sId);
                array_push($aryProductSubtype, $oProductSubtype);
            }
            if (sizeof($aryProductSubtype)) {
                return $aryProductSubtype;
            } else
                return null;
        }
    }
    //********************************************************
    public function getProductBySubtype($sSubtype) {
        $sql = "select id from pdt_product_subtype where active=1 and name='$sSubtype' ";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $oProductSubtype = $this->getProductSubType($sId);
                $this->getAllProductBySubtype($oProductSubtype);
                return $oProductSubtype;
            }
          
        }else
                return null;
    }
    public function getProductSubType($sId) {
        $sql = "select name,comment,type_id from pdt_product_subtype where id=$sId ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sName = $row[0];
            $sComment = $row[1];
            $nTypeId = $row[2];
            $oProductSubtype = new CProductSubtype($sId, $sName, $sComment);
            $oProductType = $this->getProductType($nTypeId);
            $oProductSubtype->setType($oProductType);
            return $oProductSubtype;
        }
    }
    public function getProductType($sId) {
        $sql = "select name,comment from pdt_product_type where id=$sId ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sName = $row[0];
            $sComment = $row[1];
            return new CProductType($sId, $sName, $sComment);
        }
    }
    
    public function getProductByName($sName) {
        $sql = "select id from pdt_product_list where name='$sName' and active=1 ";
        //echo $sql;
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId  = $row[0];
            $oProduct = $this->getProductById($sId);
            return $oProduct;
        } else {
            return null;
        }
    }
    public function getProductModuleByName($sName) {
        $sql = "select id from pdt_product_module where name='$sName' and active=1";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId  = $row[0];
            $oProductModule = $this->getProductModuleById($sId);
            return $oProductModule;
        } else {
            return null;
        }
    }
    public function getProductModuleById($sId) {
        $sql = "select id, name from pdt_product_module where id='$sId' ";
        echo $sql;
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sName = $row[1];
            $oProductModule = new CProductModule($sId,$sName);
            return $oProductModule;
        } else {
            return null;
        }
    }
    public function delProductByName(CProduct $oProduct) {
        $sql = " delete from pdt_product_list where name='$oProduct->mName'; ";
        $this->xExecute($sql);
    }
    public function addProduct(CProduct $oProduct) {
        //$oProduct =  $oProduct->mType;
        $oProductSubtype = $oProduct->mSubtype;
        $sql = " insert into pdt_product_list (subtype_id,name,active,create_user,create_time)";
        $sql .= " values('$oProductSubtype->mId','$oProduct->mName','$oProduct->mActive','".$_SESSION["USER_ID"]."',now());";
        $nProductId = $this->xInsert($sql);
        if ($nProductId) {
            $oProduct->mId = $nProductId;
        }
    }
    public function setProduct(CProduct $oProduct) {
        //$oProduct =  $oProduct->mType;
        $oProductSubtype = $oProduct->mSubtype;
        //(subtype_id,name,active,create_user,create_time)
        $sql = " update pdt_product_list set subtype_id='$oProductSubtype->mId',";
        $sql .= " name='$oProduct->mName',active='$oProduct->mActive',modify_user='".$_SESSION["USER_ID"]."',modify_time=now()";
        $sql .= " where id='$oProduct->mId'; ";
        $this->xExecute($sql);
    }
    public function deleteProductAccessory(CProduct $oMasterProduct) {
        $sql = " delete from  pdt_product_accessory_map  where master_product_id='$oMasterProduct->mId'; ";
        $this->xExecute($sql);
    }
    public function bindProductAccessory(CProduct $oMasterProduct,CProduct $oSlaveProduct) {
        //$this->deleteProductAccessory($oMasterProduct);
        $sql = " insert into pdt_product_accessory_map (master_product_id,slave_product_id,modify_user,modify_time)";
        $sql .= " values('$oMasterProduct->mId','$oSlaveProduct->mId','".$_SESSION["USER_ID"]."',now());";
        $nProductAccessoryId = $this->xInsert($sql);
    }
    public function delProductModuleByName(CProductModule $oProductModule) {
        $sql = " delete from  pdt_product_module  where name ='$oProductModule->mName'; ";
        $this->xExecute($sql);
    }
    public function addProductModule(CProductModule $oProductModule) {
        $oProduct =  $oProductModule->mProduct;
        $sql = " insert into pdt_product_module (product_id,name,active,create_user,create_time)";
        $sql .= " values('$oProduct->mId','$oProductModule->mName','$oProductModule->mActive','".$_SESSION["USER_ID"]."',now());";
        $nModuleId = $this->xInsert($sql);
        if ($nModuleId) {
            $oProductModule->mId = $nModuleId;
        }
    }
    public function addProductAttachment(CProductAttachment $oProductAttachment) {
        $sFileContent = mysqli_real_escape_string($this->mConnection, $oProductAttachment->mFileContent);
        //$nProductId = $oProductAttachment->mProduct->mId;
        $sql = "replace into pdt_product_attachment (file_name, mime_type,file_size,file_content,create_time) ";
        //attachment id ? one table split to twol
        $sql .= "values ('$oProductAttachment->mFileName','$oProductAttachment->mFileType','$oProductAttachment->mFileSize','$sFileContent','$oProductAttachment->mCreateTime');";
        $oProductAttachment->mId = $this->xInsertEx($sql);
        $this->bindProductAttachment($oProductAttachment);
        
    }
   public function getProductAttachment($sId) {
       $sql = "select id,file_name, mime_type,file_size,file_content,create_time from pdt_product_attachment where id= '$sId' ";
       if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
                $sFileName = $row[1];
                $sMimeType = $row[2];
                $sFileSize = $row[3];
                $sFileContent = $row[4];
                $sCreateTime = $row[5];
                $sProductId = $row[6];
                $sId = $row[0];
                //$oProduct = $this->getProductById($sProductId);
                $oProductAttachment = new CProductAttachment($sId, new CProduct(0,null,null), $sFileName, $sMimeType, $sFileSize, $sFileContent,$sCreateTime);
            return $oProductAttachment;
        } else {
            return null;
        }
   }
    public function getProductAttachments(CProduct $oProduct) {
        $aryProductAttachment = array();
        $nProductId = $oProduct->mId;
        $sql = "select id,file_name, mime_type,file_size,file_content,create_time from pdt_product_attachment where id in (select attachment_id from pdt_product_attachment_map where  ";
        //attachment id ? one table split to twol
        $sql .= "product_id= '$oProduct->mId')  ";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sFileName = $row[1];
                $sMimeType = $row[2];
                $sFileSize = $row[3];
                $sFileContent = $row[4];
                $sCreateTime = $row[5];
                $sId = $row[0];
                $oProductAttachment = new CProductAttachment($sId, $oProduct, $sFileName, $sMimeType, $sFileSize, $sFileContent,$sCreateTime);
                array_push($aryProductAttachment, $oProductAttachment);
            }
            if (sizeof($aryProductAttachment))
                return $aryProductAttachment;
            else
                return null;
        }else {
            return null;
        }
        
        
    }
     public function bindProductAttachment(CProductAttachment $oProductAttachment) {
         $oProduct = $oProductAttachment->mProduct;
         $sql = "insert into pdt_product_attachment_map (product_id,attachment_id) ";
         $sql .= "values ('$oProduct->mId','$oProductAttachment->mId');";
         //echo $sql;
         $this->xInsert($sql);
     }
     public function unbindProductAttachment(CProductAttachment $oProductAttachment) {
         $oProduct = $oProductAttachment->mProduct;
         $sql = "delete from pdt_product_attachment_map where ";
         $sql .= "product_id='$oProduct->mId' and attachment_id='$oProductAttachment->mId'; ";
         $this->xExecute($sql);
     }
     public function addProductOutline(CProductOutline $oProductOutline) {
        
        $sql = "insert into pdt_product_outline (content) ";
        //attachment id ? one table split to twol
        $sql .= "values ('$oProductOutline->mContent');";
        $oProductOutline->mId = $this->xInsert($sql);
        $this->bindProductOutline($oProductOutline);
        
    }
    public function getProductOutline($sId) {
        $sql = "select content from pdt_product_outline where id='$sId'; ";
         if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sContent = $row[0];
            $oProductOutline =new CProductOutline($sId,new CProduct(0, null, null),$sContent);
           return $oProductOutline;
        } else {
            return null;
        }
    }
    public function getProductOutlineByMap(CProduct $oProduct) {
        //$aryProductOutline = array();
        $nProductId = $oProduct->mId;
        $sql = "select id,content from pdt_product_outline where id in (select max(outline_id) from pdt_product_outline_map where  ";
        $sql .= "product_id= '$oProduct->mId') ; ";
         if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet); 
            $sContent = $row[1];
            $sId = $row[0];
            $oProductOutline = new CProductOutline($sId,$oProduct, $sContent);
           return $oProductOutline;
        } else {
            return null;
        }
        
    }
    public function getProductOutlines(CProduct $oProduct) {
        //$aryProductOutline = array();
        $nProductId = $oProduct->mId;
        $sql = "select id,content from pdt_product_outline where id in (select max(outline_id) from pdt_product_outline_map where  ";
        //attachment id ? one table split to twol
        $sql .= "product_id= '$oProduct->mId') ; ";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sContent = $row[1];
                $sId = $row[0];
                $oProductOutline = new CProductOutline($sId,$oProduct, $sContent);
                array_push($aryProductOutline, $oProductOutline);
            }
            if (sizeof($aryProductOutline))
                return $aryProductOutline;
            else
                return null;
        }else {
            return null;
        }
        
        
    }
     public function bindProductOutline(CProductOutline $oProductOutline) {
         $oProduct = $oProductOutline->mProduct;
         $sql = "insert into pdt_product_outline_map (product_id,outline_id) ";
         $sql .= "values ('$oProduct->mId','$oProductOutline->mId');";
         $this->xInsert($sql);
     }
     public function unbindProductOutline(CProductOutline $oProductOutline) {
         $oProduct = $oProductOutline->mProduct;
         $sql = "delete from pdt_product_outline_map where ";
         $sql .= "product_id='$oProduct->mId' and outline_id='$oProductOutline->mId'; ";
         $this->xExecute($sql);
     }
     public function setProductModule(CProductModule $oProductModule) {
        $oProduct =  $oProductModule->mProduct;
        $sql = " update pdt_product_module ";
        $sql .= " set active='$oProductModule->mActive',modify_time=now(),modify_user=".$_SESSION["USER_ID"].", name='$oProductModule->mName', product_id='$oProduct->mId' ";
        $sql .= " where id='$oProductModule->mId'; ";
        $this->xExecute($sql);
    }
    public function getProductAccessory(CProduct $oProduct){
         $aryProduct = array();
         $sql = "select slave_product_id from pdt_product_accessory_map where master_product_id='$oProduct->mId' ";
          if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $o = $this->getNewProductById($sId);
                array_push($aryProduct, $o);
            }
            if (sizeof($aryProduct))
                return $aryProduct;
            else
                return null;
        }else {
            return null;
        }
   
    }
    public function getNewProductById($sId) {
        $sql = "select type_id, name, subtype_id,if(active=1,'ACTIVE','INACTIVE')active from pdt_product_list where id='$sId' ";
        //$sql = "select type_id, name from pdt_product_list where id='$sId' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sType = $row[0];
            $sName = $row[1];
            $sSubtype_id = $row[2];
            $sActive = $row[3];
            $oProduct = new CProduct($sId, $sType, $sName);
            $oProduct->setActive($sActive);
            $oProduct->setProductSubtype($this->getProductSubType($sSubtype_id));
            $this->getAllProductModule($oProduct);
            return $oProduct;
        } else {
            return null;
        }
    }
    //should not be easy to modify
    public function getProductById($sId) {
        //$sql = "select type_id, name, subtype_id,if(active=1,'ACTIVE','INACTIVE')active from pdt_product_list where id='$sId' ";
        $sql = "select type_id, name from pdt_product_list where id='$sId' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sType = $row[0];
            $sName = $row[1];
            //$sSubtype_id = $row[2];
            //$sActive = $row[3];
            $oProduct = new CProduct($sId, $sType, $sName);
            //$oProduct->setActive($sActive);
            //$oProduct->setProductSubtype($this->getProductSubType($sSubtype_id));
            $this->getAllProductModule($oProduct);
            return $oProduct;
        } else {
            return null;
        }
    }
    public function bindProductMail(CProduct $oProduct, CMail $oMail) {
        $sql = " insert into pdt_product_mail_map (product_id, mail_id) values ('$oProduct->mId','$oMail->mId');";
        return $this->xInsert($sql); //
    }
    public function getProductByLicense(CLicense $oLicense) {
        $arySelectedModules = array();
        $sql = "SELECT product_id FROM tsms.lic_license_module_map where license_id=$oLicense->mId group by product_id";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sProductId = $row[0];
            $oProduct = $this->getProductById($sProductId);
            $oLicenseProduct = new CLicensedProduct($oProduct);
            $sql = "SELECT module_id FROM tsms.lic_license_module_map where license_id=$oLicense->mId and product_id=$oProduct->mId";
            if ($this->xQuery($sql)) {
                while ($row = mysqli_fetch_row($this->mResultSet)) {
                    $sModuleId = $row[0];
                    foreach ($oProduct->mModules as $oModule) {
                        if ($oModule->mId == $sModuleId) {
                            $oLicenseProduct->addLicensedProductModule($oModule);
                            break;
                        }
                    }
                }
            }
            return $oLicenseProduct;
        }
    }
    public function getAllSubtype(CProductType $oProductType) {
        
        $aryProductSubType = array();
        $sql = "select id from pdt_product_subtype where active=1 ";
        if($oProductType->mId){
            $sql .= "and type_id=" . $oProductType->mId;
        }
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $oProductSubType = $this->getProductSubType($sId);
                array_push($aryProductSubType, $oProductSubType);
            }
            if (sizeof($aryProductSubType))
                return $aryProductSubType;
            else
                return null;
        }else {
            return null;
        }
    }
    public function getAllProduct(CProductType $oProductType) {
        /* return a product array() */
        $aryProduct = array();
        $sql = "select id from pdt_product_list where active=1 ";
        if($oProductType->mId){
            $sql .= "and type_id=" . $oProductType->mId;
        }
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $oProduct = $this->getProductById($sId);
                array_push($aryProduct, $oProduct);
            }
            if (sizeof($aryProduct))
                return $aryProduct;
            else
                return null;
        }else {
            return null;
        }
    }
   public function getAllProductBySubtype(CProductSubtype $oProductSubtype) {
        /* return a product array() */
        $aryProduct = array();
        $sql = "select id from pdt_product_list where active=1 and subtype_id=" . $oProductSubtype->mId;
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $oProduct = $this->getNewProductById($sId);
                array_push($oProductSubtype->mProducts, $oProduct);
            }
          
        }else {
            return null;
        }
    }
    
    public function getAllProductModule(CProduct $oProduct) {
        /* return module array */
        $sql = "select id,name from pdt_product_module where active=1 and product_id=$oProduct->mId";
        if ($this->xQuery($sql)) {
            while ($row = mysqli_fetch_row($this->mResultSet)) {
                $sId = $row[0];
                $sName = $row[1];
                $oModule = new CProductModule($sId, $sName);
                array_push($oProduct->mModules, $oModule);
            }
        }
    }

    /* license related functions */

    public function getAllLicenseType() {
        $sql = "select id from lic_license_type";
        if ($this->xQuery($sql)) {
            $aryLicenseType = array();
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $oLicenseType = $this->getLicenseTypeById($sId);
                array_push($aryLicenseType, $oLicenseType);
            }
        }
        return $aryLicenseType;
    }

    public function getLicenseTypeById($sId) {
        $sql = "select name,parameter,comment from lic_license_type where id=$sId";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sName = $row[0];
            $sParameter = $row[1];
            $sComment = $row[2];
            return new CLicenseType($sId, $sName, $sParameter, $sComment);
        }
    }

    public function addLicense(CLicense $oLicense) {
        $nTypeId = $oLicense->mType->mId;
        $nOwnerId = $oLicense->mOwner->mId;
        $nQuantity = $oLicense->mQuantity;
        $sContent = $oLicense->mLicenseContent;
        $sql = " insert into lic_license_list (lic_type_id,owner_id,quantity,content)";
        $sql .= " values('$nTypeId','$nOwnerId','$nQuantity','$sContent');";
        $nlicenseId = $this->xInsert($sql);
        if ($nlicenseId) {
            $oLicense->mId = $nlicenseId;
        }
    }

    public function isLicenseExist($sLicenseContent) {
        $sql = "select id from lic_license_list where content = '$sLicenseContent'; ";
        $nId = 0;
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $nId = $row[0];
        } 
        return $nId;
    }
    public function setLicenseExtend(CLicense $oLicense) {
        $sLicenseExtDays = $oLicense->mType->mParameter;
        $sql = " update lic_license_list ";
        $sql .= " set start_date=current_date, end_date=date_add(current_date, interval $sLicenseExtDays day), active=1 "; //active license automatically
        $sql .= " where id='$oLicense->mId'; ";
        $this->xExecute($sql);
    }

    public function getLicenseByMacAddress($sMacAddress) {
        $aryLicense = array();
        $sql = " select content from tsms.lic_license_list";
        $sql .= " where id in ( ";
        $sql .= "  select license_id FROM tsms.lic_license_mac_address_map ";
        $sql .= "  where mac_address_id = ( ";
        $sql .= "     select id from tsms.lic_license_mac_address where mac_address='$sMacAddress') ";
        $sql .= "  ) and active=1";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sLicenseContent = $row[0];
                $oLicense = $this->getLicenseByContent($sLicenseContent);
                array_push($aryLicense, $oLicense);
            }
        }
        if (sizeof($aryLicense)) {
            return $aryLicense;
        } else {
            return null;
        }
    }

    public function getLicenseByContent($sLicenseContent) {
        $sql = " SELECT id,lic_type_id,owner_id,quantity,start_date,end_date,create_time,Active ";
        $sql .= " FROM tsms.lic_license_list where content='$sLicenseContent'";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sLicenseTypeId = $row[1];
            $oLicenseType = $this->getLicenseTypeById($sLicenseTypeId);
            $sOwnerId = $row[2];
            $oOwner = $this->getUserById($sOwnerId);
            $sQuantity = $row[3];
            $sStartDate = $row[4];
            $sEndDate = $row[5];
            $sActive = $row[7];
            return new CLicense($sId, $oLicenseType, $oOwner, $sQuantity, $sLicenseContent, $sStartDate, $sEndDate, $sActive);
        } else
            return null;
    }
    
    public function delLicense($sLicenseContent) {
        $sql = " delete from lic_license_list where content = '$sLicenseContent'; ";
        $this->xExecute($sql);

    }
  
    public function getLicenseBySignOff(CSignOff $oSignOff) {
        $sql = " select (select content from lic_license_list where id= a.license_id) licenseContent ";
        $sql .= " from lic_license_signoff_map a where signoff_id = '$oSignOff->mId'; ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sLicenseContent = $row[0];
            return $this->getLicenseByContent($sLicenseContent);
        } else
            return null;
    }

    public function isMacAddressExist(CMacAddress $oMacAddress) {
        $sql = "select id,mac_address from lic_license_mac_address where mac_address = '$oMacAddress->mAddress'";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $nId = $row[0];
//            $sAddress = $row[1];
            $oMacAddress->mId = $nId;
            return $oMacAddress;
        } else
            return null;
    }

    public function addMacAddress(CMacAddress $oMacAddress) {
        $sql = " insert into lic_license_mac_address (mac_address) values ('$oMacAddress->mAddress');";
        $nMacAddressId = $this->xInsert($sql);
        if ($nMacAddressId) {
            $oMacAddress->mId = $nMacAddressId;
        }
    }

    public function getMacAddressByLicense(CLicense $oLicense) {
        $aryMacAddress = array();
        $sql = " select id,mac_address from lic_license_mac_address ";
        $sql .= " where id in ( ";
        $sql .= "     SELECT mac_address_id FROM tsms.lic_license_mac_address_map where license_id=$oLicense->mId ";
        $sql .= " );";
        if ($this->xQuery($sql)) {
            while ($row = mysqli_fetch_row($this->mResultSet)) {
                $sId = $row[0];
                $sAddress = $row[1];
                $oMacAddress = new CMacAddress($sId, $sAddress);
                array_push($aryMacAddress, $oMacAddress);
            }
        }
        if (sizeof($aryMacAddress)) {
            return $aryMacAddress;
        } else {
            return null;
        }
    }

    public function bindLicenseMacAddress(CLicense $oLicense, CMacAddress $oMacAddress) {
        $sql = " select * from lic_license_mac_address_map where license_id='$oLicense->mId' and mac_address_id='$oMacAddress->mId' ";
        if (!$this->xQuery($sql)) {
            $sql = " insert into lic_license_mac_address_map(license_id, mac_address_id) values ('$oLicense->mId','$oMacAddress->mId');";
            $this->xInsert($sql);
        }
    }

    public function unbindLicenseMacAddress(CLicense $oLicense) {
        $sql = " delete from lic_license_mac_address_map where license_id='$oLicense->mId'";
        $this->xExecute($sql);
    }

    public function bindLicenseProduct(CLicense $oLicense, CProduct $oProduct) {
        foreach ($oProduct->mModules as $oModule) {
            $sql = " insert into lic_license_module_map (license_id,product_id,module_id) values ('$oLicense->mId','$oProduct->mId','$oModule->mId');";
            $this->xInsert($sql);
        }
    }

    public function bindLicenseSignOff(CLicense $oLicense, CSignOff $oSignOff) {
        $sql = " select ";
        $sql = " insert into lic_license_signoff_map (license_id, signoff_id) values ('$oLicense->mId','$oSignOff->mId');";
        $this->xInsert($sql);
    }

    /* signoff related functions */

    public function getAllSignOffState() {
        $arySignOffState = array();
        $sql = "select name from ppo_signoff_state ";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sStateName = $row[0];
                $oSignOffState = $this->getSignOffState($sStateName);
                array_push($arySignOffState, $oSignOffState);
            }
        }
        if (sizeof($arySignOffState)) {
            return $arySignOffState;
        } else {
            return null;
        }
    }

    public function getSignOffState($sStateName) {
        $sql = "select id,name,comment from ppo_signoff_state where name='$sStateName' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sName = $row[1];
            $sComment = $row[2];
            return new CSignOffState($sId, $sName, $sComment);
        } else {
            return null;
        }
    }

    public function getSignOffGroup($sGroupName) {
        $sql = "select id,name,comment from ppo_signoff_group where name='$sGroupName' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sName = $row[1];
            $sComment = $row[2];
            return new CSignOffGroup($sId, $sName, $sComment);
        } else {
            return null;
        }
    }

    public function getAllSignOffType(CSignOffGroup $oGroup) {
        $arySignOffType = array();
        $sql = "select code from ppo_signoff_type where group_id='$oGroup->mId' ";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sCode = $row[0];
                $oSignOffType = $this->getSignOffType($oGroup, $sCode);
                array_push($arySignOffType, $oSignOffType);
            }
        }
        if (sizeof($arySignOffType)) {
            return $arySignOffType;
        } else {
            return null;
        }
    }

    public function getSignOffType(CSignOffGroup $oGroup, $sCode) {
        $sql = "select id,name,comment from ppo_signoff_type where group_id='$oGroup->mId' and code='$sCode' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sName = $row[1];
            $sComment = $row[2];
            return new CSignOffType($sId, $oGroup, $sCode, $sName, $sComment);
        } else {
            return null;
        }
    }

    public function getLicenseSignOff(CSignOffType $oSignOffType, CSignOffState $oSignOffState) {
        $arySignOff = array();
        $sql = " SELECT a.id,user_id,code,reason,create_time ";
        $sql .= " FROM tsms.ppo_signoff_list a , tsms.lic_license_signoff_map b ";
        $sql .= " where a.id = b.signoff_id and type_id=$oSignOffType->mId and state_id=$oSignOffState->mId ";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $sUserId = $row[1];
                $sCode = $row[2];
                $sReason = $row[3];
                $sCreateTime = $row[4];
                $oOwner = $this->getUserById($sUserId);
                $oSignOff = new CSignOff($sId, $oSignOffType, $oOwner, $oSignOffState, $sCode, $sReason, $sCreateTime);
                array_push($arySignOff, $oSignOff);
            }
        }
        if (sizeof($arySignOff)) {
            return $arySignOff;
        } else
            return null;
    }

    public function addSignOff(CSignOff $oSignOff) {
        $nTypeId = $oSignOff->mType->mId;
        $nOwnerId = $oSignOff->mOwner->mId;
        $nStateId = $oSignOff->mState->mId;
        $sql = " insert into ppo_signoff_list (type_id,user_id,state_id,code,reason,create_time) ";
        $sql .= " values('$nTypeId','$nOwnerId','$nStateId','$oSignOff->mCode','$oSignOff->mReasion','$oSignOff->mCreateTime') ";
        $oSignOff->mId = $this->xInsert($sql);
    }

    public function setSignOffClosed(CSignOff $oSignOff) {
        $sStateId = $oSignOff->mState->mId;
        $sql = "update ppo_signoff_list set state_id='$sStateId' where id='$oSignOff->mId'";
        $this->xExecute($sql);
    }

    public function getSignOffByCode($sCode) {
        $sql = "select id, ";
        $sql .= " (select name from ppo_signoff_group where id = b.group_id ) group_name, ";
        $sql .= " type_code,user_id,state_name,reason,create_time ";
        $sql .= " from ( ";
        $sql .= "  select id, ";
        $sql .= "  (select group_id from ppo_signoff_type where id=a.type_id) group_id, ";
        $sql .= "  (select code from ppo_signoff_type where id=a.type_id) type_code, ";
        $sql .= "  type_id,user_id, ";
        $sql .= "  (select name from ppo_signoff_state where id= a.state_id) state_name, ";
        $sql .= "  code, reason, create_time  ";
        $sql .= " from ppo_signoff_list a where code='$sCode' ";
        $sql .= " ) b ";

        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sGroupName = $row[1];
            $sTypeCode = $row[2];
            $sUserId = $row[3];
            $sStateName = $row[4];
            $sReason = $row[5];
            $sCreateTime = $row[6];
            $oState = $this->getSignOffState($sStateName);
            $oOwner = $this->getUserById($sUserId);
            $oGroup = $this->getSignOffGroup($sGroupName);
            $oType = $this->getSignOffType($oGroup, $sTypeCode);
            return new CSignOff($sId, $oType, $oOwner, $oState, $sCode, $sReason, $sCreateTime);
        } else {
            return null;
        }
    }

    public function addSignOffAttachment(CSignOffAttachment $oSignOffAttachment) {
        $sFileContent = mysqli_real_escape_string($this->mConnection, $oSignOffAttachment->mFileContent);
        $nSignOffId = $oSignOffAttachment->mSignOff->mId;
        $sql = "insert into ppo_signoff_attachment (signoff_id, file_name, mime_type,file_size,file_content,create_time) ";
        $sql .= "values ('$nSignOffId','$oSignOffAttachment->mFileName','$oSignOffAttachment->mFileType','$oSignOffAttachment->mFileSize','$sFileContent','$oSignOffAttachment->mCreateTime');";
        $oSignOffAttachment->mId = $this->xInsert($sql);
    }

    public function addSignOffApprove(CSignOffApprove $oSignOffApprove) {
        $oSignOff = $oSignOffApprove->mSignOff;
        $oOwner = $oSignOffApprove->mOwner;
        $sql = " insert into ppo_signoff_approve(signoff_id, user_id, result, comment) ";
        $sql .= " values ('$oSignOff->mId','$oOwner->mId','$oSignOffApprove->mResult','$oSignOffApprove->mComment') ;";
        $oSignOffApprove->mId = $this->xInsert($sql);
    }

    public function getSignOffApprove(CSignOff $oSignOff) {
        $arySignOffApprove = array();
        $sql = " SELECT id,user_id,result,comment,create_time ";
        $sql.= " FROM ppo_signoff_approve where signoff_id='$oSignOff->mId' order by create_time desc";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $sUserId = $row[1];
                $sResult = $row[2];
                $sComment = $row[3];
                $sCreateTime = $row[4];
                $oOwner = $this->getUserById($sUserId);
                $oSingOffApprove = new CSignOffApprove($sId, $oSignOff, $oOwner, $sResult, $sComment, $sCreateTime);
                array_push($arySignOffApprove, $oSingOffApprove);
            }
        }
        if (sizeof($arySignOffApprove)) {
            return $arySignOffApprove;
        } else {
            return null;
        }
    }

    public function getSignOffAttachment(CSignOff $oSignOff) {
        $arySignOffAttachment = array();
        $sql = " select id,file_name,mime_type,file_size,file_content,create_time ";
        $sql .= " from ppo_signoff_attachment where signoff_id=$oSignOff->mId ";
        if ($this->xQuery($sql)) {
            while ($row = mysqli_fetch_row($this->mResultSet)) {
                $sId = $row[0];
                $sFileName = $row[1];
                $sFileType = $row[2];
                $sFileSize = $row[3];
                $sFileContent = $row[4];
                $sCreateTime = $row[5];
                $oSignOffAttachment = new CSignOffAttachment($sId, $oSignOff, $sFileName, $sFileType, $sFileSize, $sFileContent, $sCreateTime);
                array_push($arySignOffAttachment, $oSignOffAttachment);
            }
        }
        if (sizeof($arySignOffAttachment)) {
            return $arySignOffAttachment;
        } else {
            return null;
        }
    }

    public function bindSignOffMail(CSignOff $oSignOff, CMail $oMail) {
        $sql = " insert into ppo_signoff_mail_map (signoff_id, mail_id) values ('$oSignOff->mId','$oMail->mId');";
        return $this->xInsert($sql); //
    }

    /* Mail related functions */

    public function getMailTemplateById($sId) {
        $sql = "select id, name, template from alm_mail_template where id='$sId'";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sName = $row[1];
            $sTemplate = $row[2];
            return new CMailTemplate($sId, $sName, $sTemplate);
        } else
            return null;
    }

    public function getMailTypeByName($sName) {
        $sql = "select id, template_id, comment from alm_mail_type where name= '$sName' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sTemplateId = $row[1];
            $sComment = $row[2];
            return new CMailType($sId, $this->getMailTemplateById($sTemplateId), $sName, $sComment);
        }
    }

    public function addMailList(CMail $oMail) { //Mail list, will be clean requalarly.
        $sTypeId = $oMail->mMailType->mId;
//        $sContent = $oMail->mContent;
        $sContent = mysqli_escape_string($this->mConnection, $oMail->mContent);
        $sSenderId = $oMail->mSender->mId;
        $sRecipientId = $oMail->mRecipient->mId;
        $sql = " insert into alm_mail_list (type_id,sender,recipient,subject,content) ";
        $sql .= " values ('$sTypeId','$sSenderId','$sRecipientId','$oMail->mSubject','$sContent');";
        $oMail->mId = $this->xInsert($sql);
        $this->addMailHistory($oMail);
    }

    private function addMailHistory(CMail $oMail) { //Mail log, will be saved forever.
        $sMailId = $oMail->mId;
        if ($sMailId) {
            $sql = "insert into alm_mail_list_history select * from alm_mail_list where id=$sMailId";
            $this->xInsert($sql);
        }
    }

    /* report related functions */
    public function getReport(CReport $oReport) {
        $sql = $oReport->getSQLString();
        if ($this->xQuery($sql)) {
            $oReport->mDataSet = $this->mResultSet;
        }
    }
     
    public function setForm(CControlForm $oForm) {
        $sql = $oForm->getSQLString();
        if ($this->xQuery($sql)) {
            $oForm->mDataSet = $this->mResultSet;
        }
    }
    /* keypro related functions */

    public function getAllKeyproPhysicState() {
        $aryKeyproPhysicState = array();
        $sql = " select name from kpo_physic_state order by id";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sStateName = $row[0];
                $oKeyproPhysicState = $this->getKeyproPhysicState($sStateName);
                array_push($aryKeyproPhysicState, $oKeyproPhysicState);
            }
        }
        if (sizeof($aryKeyproPhysicState)) {
            return $aryKeyproPhysicState;
        } else {
            return null;
        }
    }

    public function getKeyproPhysicState($sStateName) {
        $sql = "SELECT id,comment FROM kpo_physic_state where name='$sStateName'";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sComment = $row[1];
            $sName = $sStateName;
            return new CKeyproPhysic($sId, $sName, $sComment);
        } else {
            return null;
        }
    }

    public function getAllKeyproUsageState() {
        $aryKeyproUsageState = array();
        $sql = " select name from kpo_usage_state order by id";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sStateName = $row[0];
                $oKeyproUsageState = $this->getKeyproUsageState($sStateName);
                array_push($aryKeyproUsageState, $oKeyproUsageState);
            }
        }
        if (sizeof($aryKeyproUsageState)) {
            return $aryKeyproUsageState;
        } else {
            return null;
        }
    }

    public function getKeyproUsageState($sStateName) {
        $sql = "SELECT id,comment FROM tsms.kpo_usage_state where name='$sStateName'";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sComment = $row[1];
            $sName = $sStateName;
            return new CKeyproUsage($sId, $sName, $sComment);
        } else {
            return null;
        }
    }

    public function addKeypro(CKeypro $oKeypro) {
        $sPhysicId = $oKeypro->mPhysic->mId;
        $sUsageId = $oKeypro->mUsage->mId;
        $sOwnerId = $oKeypro->mOwner->mId;
        $sql = " insert into kpo_keypro_list (id,serial_number,physic_id,usage_id,owner_id,online_date,del_flag)";
        $sql .= " values ('$oKeypro->mId','$oKeypro->mSerialNumber','$sPhysicId','$sUsageId','$sOwnerId','$oKeypro->mOnlineDate',0)";
        if ($this->xInsert($sql)) {
            return $oKeypro;
        } else {
            return null;
        }
    }

    public function getKeyproBySignOff(CSignOff $oSignOff) {
        $aryKeypro = array();
        $sql = " SELECT serial_number FROM rpt_report_keypro_list ";
        $sql.= " where id in ( ";
        $sql.= "   select keypro_id from kpo_keypro_signoff_map where signoff_id='$oSignOff->mId'";
        $sql.= "  )";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sSerialNumber = $row[0];
                $oKeypro = $this->getKeyproBySerialNumber($sSerialNumber);
                array_push($aryKeypro, $oKeypro);
            }
        }
        if (sizeof($aryKeypro)) {
            return $aryKeypro;
        } else {
            return null;
        }
    }

    public function getKeyproBySerialNumber($sSerialNumber) {
        $sql = " SELECT id,serial_number,physic_state,usage_state,owner_id,online_date,del_flag ";
        $sql .= "FROM rpt_report_keypro_list where serial_number='$sSerialNumber'";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sSerialNumber = $row[1];
            $sPhysicStateName = $row[2];
            $oPhysic = $this->getKeyproPhysicState($sPhysicStateName);
            $sUsageStateName = $row[3];
            $oUsage = $this->getKeyproUsageState($sUsageStateName);
            $sOwnerId = $row[4];
            $oOwner = $this->getUserById($sOwnerId);
            $sOnlineDate = $row[5];
            $sDeleteFlag = $row[6];
            return new CKeypro($sId, $sSerialNumber, $oPhysic, $oUsage, $oOwner, $sOnlineDate, $sDeleteFlag);
        } else {
            return null;
        }
    }

    public function getKeyproByState(CKeyproPhysic $oKeyproPhysic, CKeyproUsage $oKeyproUsage) {
        $aryKeypro = array();
        $sql = " select id,serial_number,owner_id,online_date,del_flag from kpo_keypro_list ";
        $sql .= " where del_flag=0 and physic_id='$oKeyproPhysic->mId' and usage_id='$oKeyproUsage->mId' order by id";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $sSerialNumber = $row[1];
                $sUserId = $row[2];
                $sOnlineDate = $row[3];
                $sDeleteFlag = $row[4];
                $oOwner = $this->getUserById($sUserId);
                $oPhysic = $oKeyproPhysic;
                $oUsage = $oKeyproUsage;
                $oKeypro = new CKeypro($sId, $sSerialNumber, $oPhysic, $oUsage, $oOwner, $sOnlineDate, $sDeleteFlag);
                array_push($aryKeypro, $oKeypro);
            }
        }
        if (sizeof($aryKeypro)) {
            return $aryKeypro;
        } else {
            return null;
        }
    }

    public function getKeyproMaxId() {
        $sql = "select max(id) from kpo_keypro_list";
        $sMaxId = 1;
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sMaxId = $row[0];
        }
        return $sMaxId;
    }

    public function setKeyproRemoved(CKeypro $oKeypro) {
        $sql = " update kpo_keypro_list set del_flag=1 where serial_number='$oKeypro->mSerialNumber'; ";
        $this->xExecute($sql);
    }

    public function setKeyproUpdated(CKeypro $oKeypro) {
        $sPhysicId = $oKeypro->mPhysic->mId;
        $sUsageId = $oKeypro->mUsage->mId;
        $sOwnerId = $oKeypro->mOwner->mId;
        $sql = " update kpo_keypro_list set physic_id='$sPhysicId', usage_id='$sUsageId', owner_id='$sOwnerId' where serial_number='$oKeypro->mSerialNumber';";
        $this->xExecute($sql);
    }

    public function bindKeyproSignOff(CKeypro $oKeypro, CSignOff $oSignOff) {
        $sql = " select * from kpo_keypro_signoff_map where keypro_id='$oKeypro->mId' and signoff_id='$oSignOff->mId' ";
        if (!$this->xQuery($sql)) {
            $sql = " insert into kpo_keypro_signoff_map(keypro_id, signoff_id) values ('$oKeypro->mId','$oSignOff->mId');";
            $this->xInsert($sql);
        }
    }

    /* Certification related functions */

    public function bindCertificationError(CFullTTDDCertification $oTTDDCertification, CCertificateError $oCertificationError) {
        $sCertificateId = $oTTDDCertification->mId;
        $sCertTypeId = $oTTDDCertification->mType->mId;
        $sErrorTypeId = $oCertificationError->mId;
        $sMessage = $oCertificationError->getErrorMessage();
        $sXMLData = $oCertificationError->mXMLData;
        $sql = " insert into lic_certificate_error_map ";
        $sql .= "(cert_type_id, error_type_id, certificate_id, message, xml_data) ";
        $sql .= " values ('$sCertTypeId','$sErrorTypeId','$sCertificateId','$sMessage','$sXMLData'); ";
        $this->xInsert($sql);
    }

    public function getCertificateError(CCertificateError $oCertificateError) {
        $sql = " select id,comment from lic_certificate_error_type where code='$oCertificateError->mCode' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            $sComment = $row[1];
            $oCertificateError->mId = $sId;
            $oCertificateError->mComment = $sComment;
        }
        return $oCertificateError;
    }

    public function getCertificateTypeByName($sName) {
        $sql = " select id from lic_certificate_type where name='$sName' ";
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
            return new CCertificateType($sId, $sName);
        } else {
            return null;
        }
    }

    public function addFullCertification(CFullTTDDCertification $oFullTTDDCertification) {
        $oType = $oFullTTDDCertification->mType;
        $oLicense = $oFullTTDDCertification->mLicense;
        $oProduct = $oFullTTDDCertification->mProduct;
        $oModule = $oFullTTDDCertification->mModule;
        $oMacAddress = $oFullTTDDCertification->mMACAddress;
        $sIpAddress = $oFullTTDDCertification->mIPAddress;
        $sResult = $oFullTTDDCertification->mResult;
        $sTimeCost = $oFullTTDDCertification->mDuration;
        $sCreateTime = $oFullTTDDCertification->mTimeStamp;

        $sTypeId = (isset($oType) ? $oType->mId : "");
        $sLicenseId = (isset($oLicense) ? $oLicense->mId : "");
        $sProductId = (isset($oProduct) ? $oProduct->mId : "");
        $sModuleId = (isset($oModule) ? $oModule->mId : "");
        $sMacAddressId = (isset($oMacAddress) ? $oMacAddress->mId : "");

        $sql = " insert into lic_certificate_full";
        $sql .= " (type_id,license_id,product_id,module_id,mac_address_id,ip_address,result,time_cost,create_time) ";
        $sql .= " values('$sTypeId','$sLicenseId','$sProductId','$sModuleId','$sMacAddressId','$sIpAddress','$sResult','$sTimeCost','$sCreateTime');";
        $sql = str_replace("''", "null", $sql);
        $sId = $this->xInsert($sql);
        if ($sId) {
            $oFullTTDDCertification->mId = $sId;
            $this->addFullCertificationHistory($oFullTTDDCertification);
        }
    }

    private function addFullCertificationHistory(CFullTTDDCertification $oFullTTDDCertification) {
        $sql = " insert into lic_certificate_full_history select * from lic_certificate_full where id=$oFullTTDDCertification->mId";
        $this->xInsert($sql);
    }

    public function addFastRTECertification(CFastTTDDCertification $oFastTTDDCertification) {
        $sql = " insert into lic_certificate_fast_rte ";
        $sql .= " (certificate_id,fast_license,expire_count,expire_time,create_time,active) ";
        $sql .= " values ('$oFastTTDDCertification->mId','$oFastTTDDCertification->mFastLicense','$oFastTTDDCertification->mExpireCount','$oFastTTDDCertification->mExpireTime','$oFastTTDDCertification->mTimeStamp','1');";
//        echo $sql;
        $this->xInsert($sql);
    }

    public function setFastRTECertificationInactive(CFastTTDDCertification $oFastTTDDCertification) {
        $sql = " update lic_certificate_fast_rte set active=0 where fast_license='$oFastTTDDCertification->mFastLicense';";
        $this->xExecute($sql);
    }

    public function addFastRTECertificationHistory(CFastTTDDCertification $oFastTTDDCertification) {
        $sql = "  insert into lic_certificate_fast_history ";
        $sql .= " (certificate_id, fast_license, result, time_cost, create_time) ";
        $sql .= " values ('$oFastTTDDCertification->mId','$oFastTTDDCertification->mFastLicense','$oFastTTDDCertification->mResult','$oFastTTDDCertification->mDuration','$oFastTTDDCertification->mTimeStamp');";
        $this->xInsert($sql);
    }

    public function getFastRTECertification($sFastLicense) {
        $sql = " SELECT certificate_id,(current_timestamp>=expire_time) isExpired,active, expire_time ";
//        $sql .= " FROM tsms.lic_certificate_fast_rte where fast_license = '$sFastLicense' and current_timestamp<=expire_time;";
        $sql .= " FROM tsms.lic_certificate_fast_rte where fast_license = '$sFastLicense';";
//        echo ($sql);
        if ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sCertificateId = $row[0];
            $sIsExpired = $row[1];
            $sActive = $row[2];
            $sExpireTime = $row[3];
            $oFastTTDDCertification = new CFastTTDDCertification();
            $oFastTTDDCertification->mId = $sCertificateId;
            $oFastTTDDCertification->mIsExpired = $sIsExpired;
            $oFastTTDDCertification->mActive = $sActive;
            $oFastTTDDCertification->mFastLicense = $sFastLicense;
            $oFastTTDDCertification->mExpireTime = $sExpireTime;
            return $oFastTTDDCertification;
        } else {
            return null;
        }
    }
    /*--------------------------------For Report Module----------------------------------------*/
     public function getViewName($sCategory,$nFlag) {
        $aryView = array();
        $sql = "SELECT distinct id,name from rpt_configure_view_list where 1=1 ";
        if($nFlag){
           $sql .= "and id in (select view_id from rpt_view_user_map where user_id =".$_SESSION["USER_ID"].")";
        }
        $sql .= "and category_id  in (select id from rpt_configure_view_category where name='" . $sCategory . "')";
         //echo $sql;
         if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                  $oView= new CPair($row[0], $row[1]);
                  //array_push($aryView, $row[1]);
                  array_push($aryView, $oView);
            }
                return $aryView;
         } else {
            return null;
        }
    }
    public function getAllViewCategory() {
        $aryCategory = array();
        $sql = "SELECT distinct id,name from rpt_configure_view_category";
         if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                  $oCategory= new CPair($row[0], $row[1]);
                  array_push($aryCategory, $oCategory);
            }
                return $aryCategory;
         } else {
            return null;
        }
    }
    /*
     @ from views the user unset to collect category
     */
    public function getCategories() {
        if (isset($_SESSION["USER_ID"])) {
            $sql = "SELECT distinct id,name from rpt_configure_view_list where id in (SELECT distinct category_id from rpt_configure_view_list where id not in (select view_id from rpt_view_user_map where user_id=" . $_SESSION["USER_ID"] . "))";
            if ($this->xQuery($sql)) {
                $row = mysqli_fetch_row($this->mResultSet);
                $sCategoryId = $row[0];
                $sCategoryName = $row[1];
                $oCategory = new CCategory($sCategoryId, $sCategoryName);
                $this->getViews($oCategory);
                return $oCategory;
            } else {
                return null;
            }
        }
    }
    /*
     @ fetch views the user unset 
     */
    public function getViews(CCategory $oCategory) {
        if (isset($_SESSION["USER_ID"])) {
            $sql = "SELECT distinct id,name from rpt_configure_view_list where category_id ='" . $oCategory->getId() . "' and id not in (select view_id from rpt_view_user_map where user_id=" . $_SESSION["USER_ID"] . ")";
            $aryView = array();
            if ($this->xQuery($sql)) {
                $row = mysqli_fetch_row($this->mResultSet);
                $sViewId = $row[0];
                $sViewName = $row[1];
                $oView = new CView($sViewId, $sViewName);
                $this->getColumns($oView);
                array_push($aryView, $oView);
            }
            $oCategory.addViews($aryView);
        }
    }
   
    /*
     @  get all columns by specific view
     */
    public function getColumns(CView $oView) {
        if (isset($_SESSION["USER_ID"])) {
            $sql = "SELECT distinct id,name from rpt_configure_view_column where view_id =" . $oView->getId() . ")";
            $aryColumn = array();
            if ($this->xQuery($sql)) {
                $row = mysqli_fetch_row($this->mResultSet);
                $sColumnId = $row[0];
                $sColumnName = $row[1];
                $oColumn = new CColumn($sColumnId, $sColumnName);
                array_push($aryColumn, $oColumn);
            }
            $oView.addColumns($aryColumn);
        }
    }
    public function getAllUI() {
        $aryUI = array();
        $sql = "select id,name from rpt_ui_type  ";
        if ($this->xQuery($sql)) {
            $sUIId = $row[0];
            $sUIName = $row[1];
            array_push($aryUI, $sUIName);
        }

        return $aryUI;
    }
     public function getAllFilter() {
        $aryFilter = array();
        $sql = "select id,operator from rpt_filter_type  ";
        if ($this->xQuery($sql)) {
            $sId = $row[0];
            $sOperator = $row[1];
            array_push($aryFilter, $sOperator);
        }

        return $aryFilter;
    }
    public function getColumnUI(CColumn $oColumn) {
        if (isset($_SESSION["USER_ID"])) {
            $sql = "select c.id,c.column_id,c.ui_type_id,c.filter_type_id,u.name ui_type_name,f.operator ";
            $sql .= " from rpt_column_ui_filter_map c left join rpt_ui_type u on c.ui_type_id=u.id left join rpt_filter_type f on c.filter_type_id = f.id ";
            $sql .= " where c.user_id=" . $_SESSION["USER_ID"] . " and c.column_id = " . $oColumn->getId();
            if ($this->xQuery($sql)) {
                $oColumn->mFilterOperator = $row["operator"]; //wether alter this param to an object attribute
                $oColumn->mUIType = $row["ui_type_name"]; //wether alter this param to pass an object attribute
            }
        }
        $this->getColumnFilterParam($oColumn);
    }
    
     public function getColumnFilterParam(CColumn $oColumn) {
          $sql = "select f.id,f.column_id,f.key,f.value from rpt_filter_param f where f.user_id=". $_SESSION["USER_ID"] . " and f.column_id = " . $oColumn->getId();
            $aryFilterParam = array();
            if ($this->xQuery($sql)) {
                $result = $this->mResultSet;
                while ($row = mysqli_fetch_row($result)) {
                    $sKey = $row["key"];
                    $sValue = $row["value"];
                    $oFilterParam = new CFilterParam($sValue, True);
                    array_push($aryFilterParam, $oFilterParam);
                }
            }
     }

    /*
     @  add new view the user unset then call addColumns()
     */
    public function addView(CView $oView) {
        $sql = "  insert into rpt_view_user_map ";
        $sql .= " (view_id, user_id, create_time) ";
        $sql .= " values ('$oView->getId().','" . $_SESSION["USER_ID"] . "',sysdate)";
        $this->xInsert($sql);
        $this->addColumns($oView);
    }
     /*
     @  add new columns and relenvant info after add specific view
     */
    public function addColumnUI(CColumn $oColumn) {
        $sql = "  insert into rpt_column_ui_filter_map ";
        $sql .= " (column_id, ui_type_id,user_id, filter_type_id) ";
        $sql .= " values ('" . $oColumn->getId() . "','" . $oColumn->$mUIType . ",'" . $_SESSION["USER_ID"] . "','" . $oColumn->$mFilterOperator . "')";
        $this->xInsert($sql);
        $this->addColumnFilterParams($oColumn);
    }

    public function addColumnFilterParams(CColumn $oColumn) {
        foreach ($oColumn->getParam() as $oFilterParam) {
            $sql = "  insert into rpt_filter_param ";
            $sql .= " (column_id, user_id, key, value) ";
            $sql .= " values ('$oColumn->getId().','" . $_SESSION["USER_ID"] . "','" . $oFilterParam->mName . "')";
            $this->xInsert($sql);
        }
    }

    public function addColumns(CView $oView) {
        if (isset($_SESSION["USER_ID"])) {
            foreach ($oView->getColumns as $oColumn) {
                $sql = "  insert into rpt_column_user_map ";
                $sql .= " (column_id, user_id, visible,create_time) ";
                $sql .= " values ('$oColumn->getId().','" . $_SESSION["USER_ID"] . "','" . !$oColumn->mDisabled . "',sysdate)";
                $this->xInsert($sql);
            }
        }
        $this->addColumnUI($oColumn);
    }

    /*
     @  choose the view the user have already set
     */
    public function getView($sName) {
        
            $sql = "SELECT distinct id from rpt_configure_view_list where name ='$sName' ";
            if ($this->xQuery($sql)) {
                $row = mysqli_fetch_row($this->mResultSet);
                $sViewId = $row[0];
                $oView = new CPair($sViewId, $sName);
                return $oView;
            }else{
                return null;
            }
           
    }
    public function getViewById($sId) {
        
            $sql = "SELECT distinct name from rpt_configure_view_list where id ='$sId' ";
            if ($this->xQuery($sql)) {
                $row = mysqli_fetch_row($this->mResultSet);
                $sName = $row[0];
                $oView = new CPair($sId, $sName);
                return $oView;
            }else{
                return null;
            }
           
    }
    public function addColumnById($nId,$fDisabled,$nOrder,$sShowname) {
        $this->delColumnById($nId);
        $sql = "  insert into rpt_column_user_map";
        $sql .= " ( column_id,user_id, visible,create_time) ";
        $sql .= " values ($nId," . $_SESSION["USER_ID"] . ",$fDisabled,current_time);";
        $this->xInsert($sql);
        $sql = "  insert into rpt_column_user_information";
        $sql .= " ( column_id,user_id, `order`,`type`,showname) ";
        $sql .= " values ($nId," . $_SESSION["USER_ID"] . ",$nOrder,2,'$sShowname');";
        $this->xInsert($sql);
    }
    public function addColumnUIByValues($nColumnId,$nUIndex,$nOperator) {
        $this->delColumnUIByValues($nColumnId);
        $sql = "  insert into rpt_column_ui_filter_map ";
        $sql .= " (column_id, ui_type_id,user_id, filter_type_id) ";
        $sql .= " values ('" . $nColumnId . "','" . $nUIndex . "','" . $_SESSION["USER_ID"] . "','" . $nOperator . "');";
        $this->xInsert($sql);
    }
    public function addColumnFilterParam($nColumnId,$sParam,$nOrder,$sShowname) {
        $this->delColumnFilterParam($nColumnId);
        $sql = "  insert into rpt_filter_param ";
        $sql .= " (column_id, user_id, param) ";
        $sql .= " values ('" . $nColumnId . "','" . $_SESSION["USER_ID"] . "','" .  $sParam . "');";
        $this->xInsert($sql);
        $sql = "  insert into rpt_column_user_information ";
        $sql .= " ( column_id,user_id, `order`,`type`,showname) ";
        $sql .= " values ('" . $nColumnId . "','" . $_SESSION["USER_ID"] . "',$nOrder,1,'$sShowname');";
        $this->xInsert($sql);
    }
     public function addViewByName($sView) {
        $this->delViewByName($sView); 
        $sql = "  insert into rpt_view_user_map ";
        $sql .= " (view_id, user_id, create_time) ";
        $sql .= " values ((select id from rpt_configure_view_list where name='$sView'),'" . $_SESSION["USER_ID"] . "',current_time)";
        $this->xInsert($sql);
    }
     public function delViewByName($sView) {
        $sql = "  delete from rpt_view_user_map where  view_id in (select id from rpt_configure_view_list where name='$sView') and user_id=".$_SESSION["USER_ID"];
        $this->xExecute($sql);
    }
    public function delColumnById($nColumnId) {
        $sql = " delete from  rpt_column_user_map where column_id=$nColumnId and user_id=".$_SESSION["USER_ID"];
        $this->xExecute($sql);
        $sql = " delete from  rpt_column_user_information where type=2 and column_id=$nColumnId and user_id=".$_SESSION["USER_ID"];
        $this->xExecute($sql);
    }
    public function delColumnFilterParam($nColumnId) {
        $sql = "  delete from rpt_filter_param where column_id=$nColumnId and user_id=".$_SESSION["USER_ID"] ;
        $this->xExecute($sql);
         $sql = " delete from  rpt_column_user_information where type=1 and column_id=$nColumnId and user_id=".$_SESSION["USER_ID"];
        $this->xExecute($sql);
        
    }
    public function delColumnUIByValues($nColumnId) {
        $sql = "  delete from rpt_column_ui_filter_map where column_id='$nColumnId' and user_id=". $_SESSION["USER_ID"];
        $this->xExecute($sql);
    }
    public function getColumnHeadByReport($sView) {
        $aryHeadCell = array();
        if (isset($_SESSION["USER_ID"])) {
            $sql = " select name callname,ifnull((select column_name from rpt_view_column_comment where column_id=vc.id),vc.name) showname ";
            $sql .= " from rpt_configure_view_column vc where vc.view_id in (select id from rpt_configure_view_list where name='$sView') ";
            $sql .= " and vc.id in(select column_id from rpt_filter_param where param is not null and user_id=". $_SESSION["USER_ID"];
            if ($this->xQuery($sql)) {
                $row = mysqli_fetch_row($this->mResultSet);
                $sCallName = $row[0];
                $sShowName = $row[1];
                $oHeadCell = new CPair($sCallName, $sShowName);
                array_push($aryHeadCell, $oHeadCell);
            }
        }
       return $aryHeadCell;
    }
    public function getVisibleColumnHeadByReport($sView){
        $aryHeadCell = array();
        if(isset($_SESSION["USER_ID"])){
            $_ID = $_SESSION["USER_ID"];
        }else{
             $_ID = $_REQUEST["USER_ID"];
        }
        if ($_ID) {          
            /*$sql = " select T1.* from";
            $sql .= " (select c.id,c.name callname,ifnull((select column_name from rpt_view_column_comment where column_id=c.id),c.name) showname,(select visible from rpt_column_user_map  where column_id = c.id and user_id=". $_SESSION["USER_ID"]." )visible from rpt_configure_view_column c where c.view_id in ";
            $sql .= " (select view_id from rpt_view_user_map where view_id in (select id from rpt_configure_view_list where name='$sView') and user_id=". $_SESSION["USER_ID"] .") ";
            $sql .= " ) T1 where T1.visible=1 order by id ";*/
            $sql = "select rr.column_id,cv.name callname,ifnull(rr.showname,cv.name) showname ";
            $sql .= "from rpt_column_user_information rr right join rpt_column_user_map cu on rr.column_id = cu.column_id and rr.user_id = cu.user_id  right join rpt_configure_view_column cv on rr.column_id=cv.id ";
            $sql .= "where rr.type = 2 and cu.visible = 1 and cv.view_id in (select id from rpt_configure_view_list where name='$sView')  and rr.user_id=". $_ID; 
            $sql .= " order by rr.order asc ";
            if ($this->xQuery($sql)) {
                $result = $this->mResultSet;
                while ($row = mysqli_fetch_row($result)) {
                $sCallName = $row[1];
                $sShowName = $row[2];
                $oHeadCell = new CHeadCell($sCallName, $sShowName);
                array_push($aryHeadCell, $oHeadCell);
            }
        }
        return $aryHeadCell;
       }
   }
   public function getReportCategory() {
        $aryCategory = array();
        $sql = "SELECT distinct id,name from rpt_configure_view_category where id in (";
        $sql .= " select distinct category_id from rpt_configure_view_list where id in ( ";
        $sql .= " select view_id from rpt_view_user_map where user_id = ". $_SESSION["USER_ID"]. ") ) ";
         if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                  $oCategory= new CPair($row[0], $row[1]);
                  array_push($aryCategory, $oCategory);
            }
                return $aryCategory;
         } else {
            return null;
        }
    }
    public function getUITypeAndOperator($sView,$sColumn){
        //$aryHeadCell = array();
        if (isset($_SESSION["USER_ID"])) {          
            $sql = "select ";
            $sql .= " (select name from rpt_ui_type where m.ui_type_id=id) ui_format,";
            $sql .= " (select operator from rpt_filter_type where m.filter_type_id=id) operator ";
            $sql .= "  from rpt_column_ui_filter_map m where m.column_id in ";
            $sql .= "  ( ";
            $sql .= "     select id from rpt_configure_view_column where view_id in (";
            $sql .=       " select id from rpt_configure_view_list where name = '$sView')";
            $sql .= "      and name='$sColumn' ";
            $sql .= "  ) and user_id = ".$_SESSION["USER_ID"];
            /*
            ($this->xQuery($sql)) {
            $row = mysqli_fetch_row($this->mResultSet);
            $sId = $row[0];
             */        
            if ($this->xQuery($sql)) {
                $row = mysqli_fetch_row($this->mResultSet);
                $sUIFormat = $row[0];
                $sOperator = $row[1];
                $oCOperatorpair = new COperatorpair($sUIFormat, $sOperator);
                return $oCOperatorpair;
            }else{
                return null;
            }
        }else{
            return null;
        }        
   }
   public function getOperators($sView,$sColumn,$nType){    
            $aryOperator = array();
            $sql ="";
            if(strlen($sView))
            $sql .= "select id,operator from rpt_filter_type where id = (select filter_type_id from rpt_column_ui_filter_map where column_id in (select id from rpt_configure_view_column where view_id in (select id from rpt_configure_view_list where name='$sView') and name='$sColumn') and user_id=".$_SESSION["USER_ID"]." ) union ";
            $sql .="select id,operator from rpt_filter_type where id in ( ";
            $sql .= " select filter_type_id from rpt_column_ui_filter_constraint_map where ui_type_id=$nType";
            $sql .= ") ";
            if ($this->xQuery($sql)) {
                $result = $this->mResultSet;
                while ($row = mysqli_fetch_row($result)) {
                       $nId = $row[0];
                       $sOperator = $row[1];
                       $oOperator = new COperatorpair($nId, $sOperator);
                       array_push($aryOperator, $oOperator);
                }
                return $aryOperator;
             } else {
               return null;
             }
   }
   public function setReportRemoved($sView){
        //$aryHeadCell = array();
        if (isset($_SESSION["USER_ID"])) {   
             $sql = " delete  from rpt_column_user_information where column_id in ";
             $sql .= " ( select id from rpt_configure_view_column where view_id in ";
             $sql .= " ( select id from rpt_configure_view_list where name='$sView') ";
             $sql .= " ) and user_id = ".$_SESSION["USER_ID"];
             $this->xExecute($sql);
             $sql = " delete  from rpt_column_ui_filter_map where column_id in ";
             $sql .= " ( select id from rpt_configure_view_column where view_id in ";
             $sql .= " ( select id from rpt_configure_view_list where name='$sView') ";
             $sql .= " ) and user_id = ".$_SESSION["USER_ID"];
             //echo $sql;
             $this->xExecute($sql);
             $sql = " delete  from rpt_filter_param where column_id in ";
             $sql .= " ( select id from rpt_configure_view_column where view_id in ";
             $sql .= " ( select id from rpt_configure_view_list where name='$sView') ";
             $sql .= " ) and user_id = ".$_SESSION["USER_ID"];
             //echo $sql;
             $this->xExecute($sql);
             $sql = " delete  from rpt_column_user_map where column_id in ";
             $sql .= " ( select id from rpt_configure_view_column where view_id in ";
             $sql .= " ( select id from rpt_configure_view_list where name='$sView') ";
             $sql .= " ) and user_id = ".$_SESSION["USER_ID"];
             //echo $sql;
             $this->xExecute($sql);
             $sql = " delete  from rpt_view_user_map where view_id in ";
             $sql .= " ( select id from rpt_configure_view_list where name='$sView') ";
             $sql .= "  and user_id = ".$_SESSION["USER_ID"];
             //echo $sql;
             $this->xExecute($sql);
             
        }
   }
    public function getAllReportType($sView,$sUser){
         $aryReportType = array();
         if (isset($sUser)) {
            $sql = " select T1.id,T1.name,T1.description,ifnull(T2.id,0) is_set";
            $sql .= " from  rpt_report_type T1 left join ";
            $sql .= " (select id,report_type_id from rpt_report_reservation_map ";
            $sql .= " where view_id in (select id from rpt_configure_view_list where name='$sView') ";
            $sql .= " and user_id = '$sUser' ";
            $sql .= " ) T2 on T1.id = T2.report_type_id ";
            if ($this->xQuery($sql)) {
                $result = $this->mResultSet;
                while ($row = mysqli_fetch_row($result)) {
                    $Id = $row[0];
                    $name = $row[1];
                    $desc = $row[2];
                    $is_set = $row[3];
                    $oReportType = new CReportType($Id, $name);
                    $oReportType->setIsSet($is_set);
                    $oReportType->setDescription($desc);
                    array_push($aryReportType, $oReportType);
                }
            }
        }
        
        if (sizeof($aryReportType)) {
            return $aryReportType;
        } else {
            return null;
        }
    }
    
    public function getAllChartType(){
         $aryChartType= array();
         $sql = " select id,name from rpt_chart_type where enable = 1 order by id";
         if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            
            while ($row = mysqli_fetch_row($result)) {
                $Id = $row[0];
                $name = $row[1];
                $oChartType = new CChartType($Id,$name);
                array_push($aryChartType, $oChartType);
            }
        }
        if (sizeof($aryChartType)) {
            return $aryChartType;
        } else {
            return null;
        }
    }
    public function getNumericColumnByReport($sView){
        $aryHeadCell = array();
        //if (isset($_SESSION["USER_ID"])) { 
            $sql = "select T2.id id,T2.name,T2.DATA_TYPE data_type ";
            $sql .= "from rpt_configure_view_list T1 left join rpt_configure_view_column T2 ";
            $sql .= "on T1.id = T2.view_id where  T1.name = '$sView' and T2.is_numeric = '1' ";
            //echo $sql;
            if ($this->xQuery($sql)) {
                $result = $this->mResultSet;
                while ($row = mysqli_fetch_row($result)) {
                $id = $row[1];
                $name = $row[2];
                $oHeadCell = new CHeadCell($id, $name);
                array_push($aryHeadCell, $oHeadCell);
            }
        //}
        return $aryHeadCell;
       }
        
    }
    public function getColumnByReport($sView){
        $aryReportColumn = array();
            $sql = "select id,name,data_type,is_numeric from rpt_configure_view_column where  ";
            $sql .= "view_id in (select id from rpt_configure_view_list where name='$sView') order by id ";
            if ($this->xQuery($sql)) {
                $result = $this->mResultSet;
                while ($row = mysqli_fetch_row($result)) {
                $id = $row[0];
                $name = $row[1];
                $data_type = $row[2];
                $is_numeric = $row[3];
                $oReportColumn = new CReportColumn($id, $name);
                $oReportColumn->setDataType($data_type);
                $oReportColumn->setIsNumeric($is_numeric);
                array_push($aryReportColumn, $oReportColumn);
            }
        }
        if (sizeof($aryReportColumn)) {
            return $aryReportColumn;
        } else {
            return null;
        }
       
        
    }
   
    function getChartByReport($sView,$sUser){
         $aryChartSetting = array();
        
         if ($sView && $sUser) {
                $sql = " select id,view_id,chart_type_id,chart_title,show_column_id from rpt_view_chart_setting ";
                $sql .= " where view_id in (select id from rpt_configure_view_list where name='".$sView."') ";
                $sql .= " and user_id = '".$sUser."' ";
            if ($this->xQuery($sql)) {
                $result = $this->mResultSet;
                while ($row = mysqli_fetch_row($result)) {
                    $Id = $row[0];$sViewId = $row[1];$sChartTypeId = $row[2];$sChartTitle = $row[3];$sShowColumnId = $row[4];
                    $oChartSetting = new CChartSetting($Id,$sViewId, $sChartTypeId ,$sChartTitle , $sShowColumnId);
                    $aryDataColumn = $this->getDataColumnByReport($Id);
                    $oChartSetting->setDataColumnId($aryDataColumn);
                    array_push($aryChartSetting, $oChartSetting);
                }
            }
        }else throw new Exception("Empty arguments");
        
        if (sizeof($aryChartSetting)) {
            return $aryChartSetting;
        } else return null;
       
    }
    function getDataColumnByReport($sId){
        $aryDataColumn = array();
        $sql = " select id,data_column_id from rpt_view_chart_series  where chart_setting_id=$sId";
        if ($this->xQuery($sql)) {
           $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
               $sDataColumnId = $row[1];
               array_push($aryDataColumn, $sDataColumnId);
           }
       }
        if (sizeof($aryDataColumn)) {
            return $aryDataColumn;
        } else {
            return null;
        }
         
        
    }
    function addChartByReport($oChart,$sUser){
        //$this->delChartByReport($oChart,$sUser); 
        $sql = "  insert into rpt_view_chart_setting ";
        $sql .= " (`view_id`, `user_id`, `chart_type_id`, `chart_title`, `show_column_id`) ";
        $sql .= " values ('$oChart->mViewId','".$sUser."','$oChart->mChartTypeId','$oChart->mChartTitle','$oChart->mShowColumnId')";
        //echo $sql;
        $nChartSettingId = $this->xInsert($sql);
        if ($nChartSettingId) {
            $oChart->mId = $nChartSettingId;
        }
        $this->addDataColumnByReport($oChart);
    }
    function addDataColumnByReport($oChart){
        $aryDataColumnId = $oChart->mDataColumnId;
        foreach($aryDataColumnId as $sDataColumnId){
            $sql = "  insert into rpt_view_chart_series ";
            $sql .= " (`chart_setting_id`, `data_column_id`) ";
            $sql .= " values ('$oChart->mId','$sDataColumnId')";
            //echo $sql;
            $nDataId = $this->xInsert($sql);
        }
        
    }
    function delChartByReport($sView,$sUser){
        $sql = "  delete from rpt_view_chart_setting where  view_id ='$sView' and user_id='$sUser'";
        $this->xExecute($sql);
    }
    //separate from different chart types
    //bar(single) and line(multiple)
    // first echo then set
    function echoChartFormat($sql){
       if ($this->xQuery($sql)) {
           echo "var myData = new Array(";
           $result = $this->mResultSet;
           $r = 1;
           $e = mysqli_num_rows($result);
            //$n = $result->num_rows;
           $n = mysqli_num_fields($result);
            while ($row = mysqli_fetch_row($result)) {
              echo "[";
              $t = "";
              $i = 0;
              for(; $i < $n; $i++){
                  if($i==0){
                    $t = ",'".$row[$i]."'";  
                  }else{
                     $t = $t.",". $row[$i];   
                  }
              }
              echo substr($t, 1);
              echo "]";
              if($r<$e)echo",";
              $t = "";
              $r++;
           }
           echo ");";
       }
    }
    function echoMultiSeriesChartFormat($sql){
       $aryData = array(); 
       if ($this->xQuery($sql)) {
           $result = $this->mResultSet;
             $n = mysqli_num_fields($result);
             $t = "";
             for($i=1; $i < $n; $i++){
                 array_push($aryData, "");
             }
             while ($row = mysqli_fetch_row($result)) {
                for($i=1; $i < $n; $i++){
                  if($aryData){  
                     $aryData[$i-1] = $aryData[$i-1] . ",['".$row[0]."',".$row[$i]."]";   
                  }else{
                     $aryData[$i-1] = ",['".$row[0]."',".$row[$i]."]";    
                  }
                }
                 /*if($aryData){  
                     $aryData[0] = $aryData[0] . ",['".$row[0]."',".$row[1]."]";   
                  }else{
                     $aryData[0] = ",['".$row[0]."',".$row[1]."]";    
                  }*/
                 //$aryData[0] =  ",['".$row[0]."',".$row[1]."]"; 
             }
             /* run for loop to gen code*/
             foreach($aryData as $cData){
                echo "var myData = new Array(";            
                echo substr($cData, 1);
                echo ");setChartData(myChart,myData);";   
             }
            echo "drawChartReport(myChart);";
       }
    }
    function getReportReserved($reportType) {
        $aryReportSetting = array();
        $sql = "select id,view_id,report_type_id,user_id from rpt_report_reservation_map where report_type_id in (select id from rpt_report_type where name='$reportType')";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $sViewId = $row[1];
                $sReportTypeId = $row[2];
                $sUserId = $row[3];
                $oReportSetting = new CReportSetting($sId, $sViewId, $sReportTypeId);
                $oReportSetting->mUserId = $sUserId;
                array_push($aryReportSetting, $oReportSetting);
            }
        }
         if (sizeof($aryReportSetting)) {
            return $aryReportSetting;
        } else {
            return null;
        }
    }
     public function delReportReserved($sViewId,$sUser) {
        /*$sViewId = "";
        foreach($aryReportSetting as $oReportSetting){
            $sViewId = $oReportSetting->mViewId;
        }*/
        if($sUser && $sViewId){
        $sql = " delete from  rpt_report_reservation_map  where user_id ='$sUser' and view_id = '$sViewId'; ";
        $this->xExecute($sql);
        }
    }
    function addReportReserved(CReportSetting $oReportSetting,$sUser) {
        //$this->delReportReserved($aryReportSetting,$sUser);
        if(isset($_SESSION["USER_ID"])){
            //foreach($aryReportSetting as $oReportSetting){
                $sql = "  insert into rpt_report_reservation_map ";
                $sql .= " (`view_id`, `report_type_id`,`user_id`,`run_time`,`create_time`) ";
                $sql .= " values ('$oReportSetting->mViewId','$oReportSetting->mReportId','$sUser','$oReportSetting->mRunTime',current_timestamp)";
                $nDataId = $this->xInsert($sql);                
                $oReportSetting->setId($nDataId);
       }
       return $oReportSetting;
    }
    function getReportReservedByTime() {
        $aryReportSetting = array();
        $sql = "select id,view_id,report_type_id,user_id,run_time,next_time from rpt_report_reservation_map where run_time <= current_time";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $sViewId = $row[1];
                $sReportTypeId = $row[2];
                $sUserId = $row[3];
                $sRunTime = $row[4];
                $sNextTime = $row[5];
                //$sReportTypeId = $row[6];
                $oReportSetting = new CReportSetting($sId, $sViewId, $sReportTypeId,$sRunTime,$sNextTime);
                $oReportSetting->mUserId = $sUserId;
                
                array_push($aryReportSetting, $oReportSetting);
            }
        }
         if (sizeof($aryReportSetting)) {
            return $aryReportSetting;
        } else {
            return null;
        }
    }
    function getChartDataByReport($sView,$aryColumns){
         $aryData = array();
         $sql = "";
         foreach($aryColumns as $sColumn){
           $sql.= ",".$sColumn;
         }
         $sql = substr($sql, 1);
         $sql = "select ".$sql." from ".$sView;
         if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
             while ($row = mysqli_fetch_row($result)) {
                 $dataIndex = 0;
                 $data=array();
                 for(;$dataIndex<count($aryColumns);$dataIndex++){
                     array_push($data,$row[$dataIndex]);
                 }
                  array_push($aryData,$data);
             }
        }
        return $aryData;
    
    }
     public function setReportSetting(CReportSetting $oReportSetting) {
        
        if($oReportSetting->mReportId ==1)
        $sql = " update rpt_report_reservation_map set run_time=null ";            
        else
        $sql = " update rpt_report_reservation_map set run_time='$oReportSetting->mRunTime',next_time='$oReportSetting->mNextTime'";
        
        $sql .= " where id='$oReportSetting->mId'; ";
        //echo $sql;
        $this->xExecute($sql);
    }
    /*public function addReportParam(CReportParam $oReportParam) {
        
        $sql = " insert rpt_report_reservation_map set run_time='$oReportSetting->getRunTime()',next_time='$oReportSetting->getNextTime()'";
        $sql .= " where id='$oReportSetting->getId()'; ";
        $this->xExecute($sql);
    }*/
    public function getReservationParam(CReportSetting $oReportSetting,$sModParam) {
        //echo "reportId=" .$oReportSetting->mReportId;
        $aryParam = array();
         if($oReportSetting->mReportId >2)
            $sql = " select value from rpt_report_reservation_param where reservation_id='$oReportSetting->mId' and mod(configure_id,2) =$sModParam";
         else
            $sql = " select value from rpt_report_reservation_param where reservation_id='$oReportSetting->mId'"; 
         //echo $sql;
         if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
             while ($row = mysqli_fetch_row($result)) {
                 $val = $row[0];
                 array_push($aryParam,$val);
             }
         }
        if (sizeof($aryParam)) {
            if(sizeof($aryParam)==1){
                return $aryParam[0];
            }else{
                return $aryParam;
            }
        } else {
            return null;
        }
    }
    public function addReservationParam(CReportParam $oReportParam) {
         $sql = "  insert into rpt_report_reservation_param ";
         $sql .= " (`reservation_id`, `configure_id`,`value`) ";
         $sql .= " values ('$oReportParam->mReservationId','$oReportParam->mConfigureId','$oReportParam->mValue')";
         $nId = $this->xInsert($sql);
         $oReportParam->setId($nId); 
         return $oReportParam;
    }
    public function getAllReportTypeConfigure(){
        $aryReportTypeConfigure = array();
        $sql = " select id,type_id,comparison_unit,base_unit,min_value,max_value,description from rpt_report_type_configure ";
         if ($this->xQuery($sql)) {
            $result = $this->mResultSet;  
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $sTypeId = $row[1];
                $sComparisonUnit = $row[2];
                $sBaseUnit = $row[3];
                $sMinValue = $row[4];
                $sMaxValue = $row[5];
                $sDescription = $row[6];
                $oReportTypeConfigure = new CReportTypeConfigure($sId, $sTypeId, $sComparisonUnit,$sBaseUnit,$sMinValue,$sMaxValue,$sDescription);
                array_push($aryReportTypeConfigure, $oReportTypeConfigure);
             }
         }
        if (sizeof($aryReportTypeConfigure)) {
                return $aryReportTypeConfigure;
        } else {
            return null;
        }
    }
    
    public function getReportTypeConfigureById($sId){
        $sql = " select id,type_id,comparison_unit,base_unit,min_value,max_value,description from rpt_report_type_configure where id='$sId' ";
        if ($this->xQuery($sql)) {
                $result = $this->mResultSet; 
                $row = mysqli_fetch_row($result);
                $sId = $row[0];
                $sTypeId = $row[1];
                $sComparisonUnit = $row[2];
                $sBaseUnit = $row[3];
                $sMinValue = $row[4];
                $sMaxValue = $row[5];
                $sDescription = $row[6];
                $oReportTypeConfigure = new CReportTypeConfigure($sId, $sTypeId, $sComparisonUnit,$sBaseUnit,$sMinValue,$sMaxValue,$sDescription);
                return $oReportTypeConfigure;
        }else{
            return null;
        }
    }
    
    public function getAllReportTypeConfigureParam(CReportTypeConfigure $oReportTypeConfigure,$sUserId,$sViewId){
          $aryReportTypeConfigureParam = array();
          $sql = " select  ta1.id,ta1.configure_id,ta1.`value`,ta1.`name`,ta2.value val ";
          $sql .= " from rpt_report_type_configure_param ta1 left join (select TT1.* from rpt_report_reservation_param TT1 left join rpt_report_reservation_map TT2 ";
          $sql .= " on TT1.reservation_id = TT2.id where TT2.user_id='$sUserId' and TT2.view_id='$sViewId') ta2 ";
          $sql .= " on ta1.configure_id = ta2.configure_id and ta1.value=ta2.value ";
          $sql .= " where ta1.configure_id = '$oReportTypeConfigure->mId' order by ta1.id ";
          //echo $sql;
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;  
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $sConfigureId = $row[1];
                $sValue = $row[2];
                $sName = $row[3];
                $sVal = $row[4];                
                $oReportTypeConfigureParam = new CReportTypeConfigureParam($sId, $sConfigureId, $sValue,$sName,$sVal);
                array_push($aryReportTypeConfigureParam, $oReportTypeConfigureParam);
             }
         }
        if (sizeof($aryReportTypeConfigureParam)) {
                return $aryReportTypeConfigureParam;
        } else {
            return null;
        }
    }
    function getSpecificReport($reportType) {
        $aryReportSetting = array();
        $sql = "select id,view_id,report_type_id,user_id from rpt_report_reservation_map where report_type_id in (select id from rpt_report_type where name='$reportType')";
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            while ($row = mysqli_fetch_row($result)) {
                $sId = $row[0];
                $sViewId = $row[1];
                $sReportTypeId = $row[2];
                $sUserId = $row[3];
                $oReportSetting = new CReportSetting($sId, $sViewId, $sReportTypeId);
                $oReportSetting->mUserId = $sUserId;
                array_push($aryReportSetting, $oReportSetting);
            }
        }
         if (sizeof($aryReportSetting)) {
            return $aryReportSetting;
        } else {
            return null;
        }
    }
    function getReportConfigureId($sName){
        $sql = "select id from rpt_report_type where name='$sName'";
         if ($this->xQuery($sql)) {
                $result = $this->mResultSet; 
                $row = mysqli_fetch_row($result);
                $sId = $row[0];
                return $sId;
         }else {
             return null;
         
         }
    }
    function getReportSettingByName($sName,$sUserId,$sViewId){
        
        $sId = $this->getReportConfigureId($sName);
        $sql = "select id,view_id,report_type_id,user_id,run_time,next_time from rpt_report_reservation_map where view_id='$sViewId' and user_id='$sUserId' and report_type_id='$sId'";
        //echo $sql;
        if ($this->xQuery($sql)) {
            $result = $this->mResultSet;
            $row = mysqli_fetch_row($result);
                $sId = $row[0];
                $sViewId = $row[1];
                $sReportTypeId = $row[2];
                $sUserId = $row[3];
                $sRunTime = $row[4];
                $sNextTime = $row[5];
                //$sReportTypeId = $row[6];
                $oReportSetting = new CReportSetting($sId, $sViewId, $sReportTypeId,$sRunTime,$sNextTime);
                $oReportSetting->mUserId = $sUserId;
                return $oReportSetting;
        }else{
            return null;
        }
        
    }
}


