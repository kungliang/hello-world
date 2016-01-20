<?php
include "../class/CDatabase.php";
include "../class/CLicense.php";
include "../class/CProduct.php";
include "../class/CUser.php";

function startTag($tag) {
        $str = "<$tag";
        $str .= '>';
        return $str;
    }
    function endTag($tag = '') {
        $str = $tag? "</$tag>": "";
        return $str;
    }
    
    function addEmptyTag($tag, $attr_ar = array() ) {
        $str = "<$tag/>";        
        return $str;
    }


if(!$_GET||!isset($_GET["Content"])||!$_GET["Content"])
exit();

$content = $_GET["Content"];
$xml = simplexml_load_string($content);

$oDb = new CDatabase();
$struct_license =$xml->xpath("/doc_root/repository/License");
$struct_mac_list =$xml->xpath("/doc_root/repository/MacAddressList");
$struct_product_list =$xml->xpath("/doc_root/repository/ProductList");

$nSize=count($struct_license);
$sLicenseContent="";
for($i=0;$i<$nSize;$i++){
  $o = $struct_license[$i];
  
  foreach($o as $k => $v){
	if (strcmp("LicenseTypeId", $k) === 0) {
	    $nLicenseTypeId = $v;
	}elseif (strcmp("OwnerId", $k) === 0) {
	    $nOwnerId = $v;
	}elseif (strcmp("LicenseContent", $k) === 0) {
	    $sLicenseContent = $v;
	}elseif (strcmp("CreateTime", $k) === 0) {
	    $sCreateTime = $v;
	}
  }
  $oLicenseType = $oDb->getLicenseTypeById($nLicenseTypeId);
  $oOwner = $oDb->getUserById($nOwnerId);
  $oLicense = new CLicense(0, $oLicenseType, $oOwner, "", "",null,null, 0);
  $oLicense->mLicenseContent = $sLicenseContent;
  if($oDb->isLicenseExist($sLicenseContent))
  $oDb->delLicense($sLicenseContent);
  $oDb->addLicense($oLicense); 
  $oDb->setLicenseExtend($oLicense);
  $m = $struct_mac_list[$i];
  $tmpAryMacAddress = array();
  foreach($m as $k => $v){
  	 $macAddress = trim($v);
	 if($macAddress)
	    array_push($tmpAryMacAddress, new CMacAddress(0, $macAddress));
  }
  $aryMacAddress = $tmpAryMacAddress; //CMacAddress Array.

  // Add or get mac_address_id to bind license
  $tmpMacAddress=null;
	for($j=0;$j<sizeof($aryMacAddress);$j++){
	    if($tmpMacAddress = $oDb->isMacAddressExist($aryMacAddress[$j])){
	        $aryMacAddress[$j]=$tmpMacAddress; // get mac_address_id
	    }else{
	        $oDb->addMacAddress($aryMacAddress[$j]); // generate mac_address_id;
	    }
	    if($aryMacAddress[$j]){ // Bind mac address to license
	        $oDb->bindLicenseMacAddress($oLicense, $aryMacAddress[$j]);
	    }
	}
  $p = $struct_product_list[$i];
  
  $tmpAryProduct = array();
  $sProduct = "";
  $sModule = "";
  foreach($p as $k => $v){	
        if (strcmp("Product", $k) === 0) {
	    $sProduct = $v;
		}elseif (strcmp("Module", $k) === 0) {
		    $sModule = $v;
		}
		if($sProduct && $sModule){
			$oProduct = new CProduct($sProduct, "", "");
			array_push($oProduct->mModules, new CProductModule($sModule, ""));
			$oDb->bindLicenseProduct($oLicense, $oProduct);   
			$sProduct = "";
  			$sModule = "";
		}

  }
	    
}

$oLicense = $oDb->getLicenseByContent($sLicenseContent);
if($oLicense->mId){
$param  = startTag("doc_root"); 
$param .= startTag("repository");
$param .= startTag("License");
$param .= startTag("LicenseContent");
$param .=$oLicense->mLicenseContent;
$param .= endTag("LicenseContent");
$param .= endTag("License");  			
$param .= endTag("repository"); 
$param .= endTag("doc_root");
header( "refresh:0;url=http://172.16.33.201/TSMS/license/xml_license_ux.php?Content=".$param); 
}


?>

