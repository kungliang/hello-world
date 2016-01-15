<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Welcome to TTDD License Query Result</title>
    </head>
        <link rel="stylesheet" href="../css/demos.css">
        <link rel="stylesheet" href="../css/tsms.base.css">
        <link rel="stylesheet" href="../css/tsms.textfield.css">
        <link rel="stylesheet" href="../css/tsms.select.css">
        <link rel="stylesheet" href="../js/libs/jqueryui/css/ui-lightness/jquery.ui.all.css">
        <script src="../js/libs/jquery/jquery.js"></script>
        <script src="../js/libs/jqueryui/jquery-ui.js"></script>
        <script src="../js/tsms.formcheck.js"></script>    
        <script>
            function showLicense(license){
                   $("#queryValue").val(license);
                   $("#queryBy").val("license");
                   document.forms[1].action="lic_license_query_submit.php";
                   document.forms[1].submit();                
            }
        </script>
    <body style="margin-top:20px">
        <?php
        // put your code here        
        include "../class/CSession.php";
        include "../class/CDatabase.php";
        include "../class/CProduct.php";
        include "../class/CLicense.php";
        include "../class/CUser.php";
        $queryBy = $_REQUEST["queryBy"];
        $queryValue = $_REQUEST["queryValue"];

        $oDb = new CDatabase();
        //query by license
        if($queryBy == "license" && $queryValue){
            $oLicense = $oDb->getLicenseByContent($queryValue);
//            print_r($oLicense);
            if($oLicense && $oLicense->mActive){
                $oLicenseProduct = $oDb->getProductByLicense($oLicense);
                $oProductType = $oDb->getProductType($oLicenseProduct->mType);
                echo "<h2>TTDD License Information</h2>";
                echo "<form><table>";
                //License information
                echo " <tr><td colspan=2><div class=\"step_guide\">License Infomation</div></td></tr>";
                echo " <tr><th class=\"vertical\">Type</th><td>".$oLicense->mType->mName."</td></tr>";
                echo " <tr><th class=\"vertical\">Owner</th><td>".$oLicense->mOwner->mName."</td></tr>";
                echo " <tr><th class=\"vertical\">Start Date</th><td>".$oLicense->mStartDate."</td></tr>";
                echo " <tr><th class=\"vertical\">End Date</th><td>".$oLicense->mEndDate."</td></tr>";
                echo " <tr><th class=\"vertical\">Content</th>";
                echo "     <td><textarea cols=30 rows=4 readonly>".$oLicense->mLicenseContent."</textarea><br>";
                
                echo " </tr>";
                
                //Product information
                echo " <tr><td colspan=2><div class=\"step_guide\">Licensed Product:</div></td></tr>";
                echo " <tr><th class=\"vertical\">Type</th><td>".$oProductType->mName."</td></tr>";
                echo " <tr><th class=\"vertical\">Product</th><td>".$oLicenseProduct->mName."</td></tr>";
                echo " <tr><th class=\"vertical\">Modules</th><td>";
                    foreach($oLicenseProduct->mModules as $oModule){
                        if($oLicenseProduct->isLicensedProductModule($oModule)){
                            echo "<input type=\"checkbox\" disabled checked>".$oModule->mName;    
                        }else{
                            echo "<input type=\"checkbox\" disabled>".$oModule->mName;    
                        }
                        
                    }
                    
                echo " </td></tr>";          
                //Mac address list
                $aryMacAddress = $oDb->getMacAddressByLicense($oLicense);
                $aryMacAddressSize = sizeof($aryMacAddress);
                echo " <tr><td colspan=2><div class=\"step_guide\">Mac Addresses: </div></td></tr>";
                echo " <tr><th class=\"vertical\">Quantity</th><td>$aryMacAddressSize/".$oLicense->mQuantity."</td></tr>";
                if($aryMacAddressSize){
                echo " <tr><th class=\"vertical\">Mac address</th><td>";
                    foreach($aryMacAddress as $oMacAddress){
                        echo "<li><input type=\"text\" value=\"$oMacAddress->mAddress\" readonly><br>";
                    }
                    echo " </td></tr>";
                }
                echo "</table></form>";
            }else{
                exit( "<script>alert(\"License not found!!\");window.close();</script>");
            }
        }    
        if($queryBy == "macAddress" && $queryValue){
            $aryLicense = $oDb->getLicenseByMacAddress($queryValue);
            $nLicenseCount = sizeof($aryLicense);
            if($nLicenseCount){
                echo "<h2>TTDD License Query List</h2>";
                echo "<form>";
                echo "<table>";
                echo " <tr><td colspan=6><div class=\"step_guide\">Query By : $queryBy ($queryValue)</div></td></tr>";
                echo " <tr><th class=\"horizontal\">Type</th>";
                echo " <th class=\"horizontal\">Owner</th>";
                echo " <th class=\"horizontal\">Quantity</th>";
                echo " <th class=\"horizontal\">Start Date</th>";
                echo " <th class=\"horizontal\">End Date</th>";
//                echo " <th>Content</th>";
                echo "</tr>";            
                foreach($aryLicense as $oLicense){
                    echo " <tr>";
                    echo " <td><a href=\"javascript:showLicense('$oLicense->mLicenseContent')\">".$oLicense->mType->mName."</a></td>";
                    echo " <td>".$oLicense->mOwner->mName."</td>";
                    echo " <td>".$oLicense->mQuantity."</td>";
                    echo " <td>".$oLicense->mStartDate."</td>";
                    echo " <td>".$oLicense->mEndDate."</td>";
//                    echo " <td><textarea cols=30 rows=3 readonly>".$oLicense->mLicenseContent."</textarea></td>";
                    echo "</tr>";
                }
                echo " <tr><td colspan=6 style=\"text-align:left;padding-top:30px;\">Find $nLicenseCount record(s)</td></tr>";
                echo "</table></form>";     
            }else{
                exit( "<script>alert(\"License not found!!\");window.close();</script>");
            }
            
        }
        ?>
        <form action="lic_license_extend.php" method="POST" style="display:none;">
            <input type="hidden" id="licenseString" name="licenseString" value="">
            <input type="hidden" id="queryValue" name="queryValue" value="">
            <input type="hidden" id="queryBy" name="queryBy" value="">
        </form>
    </body>
</html>
