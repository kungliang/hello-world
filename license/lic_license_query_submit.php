       bcompiler v0.22s	        ����           �       �       (                              �      <!DOCTYPE html>
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
        �                                             I                                    ../class/CSession.php                                             I       (                            ../class/CDatabase.php                                             I       P                            ../class/CProduct.php                                              I       x                            ../class/CLicense.php                                      !       I       �                            ../class/CUser.php                                      "       P       �                             _POST                                       #       Q       �                      �                             queryBy                #       &                                                 �                      #       P       @                            _POST                                       $       Q       h                     @                     
       queryValue
                $       &       �                                         h                     $       m       �                                           	       CDatabase	               &       D       �                     �                                           &       =                                                                          &       &       0                                         �                      &              X                                                  license                (       .       X                     X                                            (       4       X                                                                  (       +                              X                                            (       p       �                                                 getLicenseByContent                )       B                                                                  =       )       =       �                                                                   )       &       �                                         �                     )       .       �                                                                 *       R                                                         mActive                *       4       �                                                                  *       +                              �                     }                       *       p       H                                                 getProductByLicense                +       B                                                                  =       +       =       p                                                                   +       &       �                                         p                     +       p       �                                                 getProductType                ,       ^       �                                                 mType               ,       B                              �                                   =       ,       =                                                                          ,       &       8                                                              ,       (                              !       <h2>TTDD License Information</h2>!                                       -       (                                     <form><table>                                       .       (                              M        <tr><td colspan=2><div class="step_guide">License Infomation</div></td></tr>M                                       0       R       `                                                 mType                1       R       �                     `                            mName                1              �                     '        <tr><th class="vertical">Type</th><td>'               �                     1              �                     �                     
       </td></tr>
                1       (                              �                                             1       R                                                         mOwner                2       R       (                                                  mName                2              P                     (        <tr><th class="vertical">Owner</th><td>(               (                     2              x                     P                     
       </td></tr>
                2       (                              x                                             2       R       �                                          
       mStartDate
                3              �                     -        <tr><th class="vertical">Start Date</th><td>-               �                     3              �                     �                     
       </td></tr>
                3       (                              �                                             3       R                                                        mEndDate                4              @                     +        <tr><th class="vertical">End Date</th><td>+                                    4              h                     @                     
       </td></tr>
                4       (                              h                                             4       (                              &        <tr><th class="vertical">Content</th>&                                       5       R       �                                                 mLicenseContent                6              �                     +            <td><textarea cols=30 rows=4 readonly>+               �                     6              �                     �                            </textarea><br>                6       (                              �                                             6       (                                      </tr>                                       8       (                              L        <tr><td colspan=2><div class="step_guide">Licensed Product:</div></td></tr>L                                       :       R                                                        mName                ;              0                     '        <tr><th class="vertical">Type</th><td>'                                    ;              X                     0                     
       </td></tr>
                ;       (                              X                                             ;       R       �                                                 mName                <              �                     *        <tr><th class="vertical">Product</th><td>*               �                     <              �                     �                     
       </td></tr>
                <       (                              �                                             <       (                              *        <tr><th class="vertical">Modules</th><td>*                                       =       R       �                                                 mModules                >       M                             �                    [                       >       N       H                                           [                       >       �                                                                             >       &       p                                         H                      >       p       �                                                 isLicensedProductModule                ?       B                                                                  =       ?       =       �                                                                   ?       +                              �                    W                       ?       R       �                                                 mName                @              	                     (       <input type="checkbox" disabled checked>(               �                     @       (                              	                                             @       *                              Z                                              A       R       8	                                                 mName                B              `	                             <input type="checkbox" disabled>                8	                     B       (                              `	                                             B       *                              L                                              E       1                                                                           E       (                                      </td></tr>                                       G       p       �	                                                 getMacAddressByLicense                I       B                                                                  =       I       =       �	                                                                   I       &       �	                                         �	                     I       B                                                                  <       J       <        
                            sizeof               5��    ��             J       &       (
                                          
                     J       (                              J        <tr><td colspan=2><div class="step_guide">Mac Addresses: </div></td></tr>J                                       K       7       P
                                            +        <tr><th class="vertical">Quantity</th><td>+                L       8       P
                     P
                                            L       6       P
                     P
                     /                L       R       x
                                          	       mQuantity	                L              �
                     P
                     x
                     L              �
                     �
                     
       </td></tr>
                L       (                              �
                                             L       +                                                   {                       M       (                              .        <tr><th class="vertical">Mac address</th><td>.                                       N       M       �
                                          x                       O       N                            �
                     x                       O       �                                                                             O       &       @                    	                                           O       7       �                                                   <li><input type="text" value="                P       R       h                     	                             mAddress                P       8       �                     �                     h                      P       7       �                     �                            " readonly><br>                P       (                              �                                             P       *                              o                                              Q       1                              �
                                            Q       (                                      </td></tr>                                       R       *                              {                                              S       (                                     </table></form>                                       T       *                              ~                                              U       O                              =       <script>alert("License not found!!");window.close();</script>=                                       V       *                                                                            X              �                                           
       macAddress
                Y       .       �                     �                     �                       Y       4       �                                                                  Y       +                              �                     �                       Y       p       �                                                 getLicenseByMacAddress                Z       B                                                                  =       Z       =                                                                          Z       &       0                    
                                          Z       B                              
                                    <       [       <       X                            sizeof               5��    ��             [       &       �                                         X                     [       +                                                   �                       \       (                                      <h2>TTDD License Query List</h2>                                        ]       (                                     <form>                                       ^       (                                     <table>                                       _       7       �                                            6        <tr><td colspan=6><div class="step_guide">Query By : 6                `       8       �                     �                                             `       7       �                     �                             (                `       8       �                     �                                            `       7       �                     �                            )</div></td></tr>                `       (                              �                                             `       (                              %        <tr><th class="horizontal">Type</th>%                                       a       (                              "        <th class="horizontal">Owner</th>"                                       b       (                              %        <th class="horizontal">Quantity</th>%                                       c       (                              '        <th class="horizontal">Start Date</th>'                                       d       (                              %        <th class="horizontal">End Date</th>%                                       e       (                                     </tr>                                       f       M       �                     
                     �                       g       N       �                     �                     �                       g       �                                                                             g       &                                                 �                      g       (                                      <tr>                                       h       7       p                                            &        <td><a href="javascript:showLicense('&                i       R       H                                                  mLicenseContent                i       8       p                     p                     H                      i       7       p                     p                            ')">                i       R       �                                                 mType                i       R       �                     �                            mName                i              �                     p                     �                     i                                   �                     	       </a></td>	                i       (                                                                           i       R       8                                                 mOwner                j       R       `                     8                            mName                j              �                             <td>               `                     j              �                     �                            </td>                j       (                              �                                             j       R       �                                          	       mQuantity	                k                                            <td>               �                     k              (                                                  </td>                k       (                              (                                             k       R       P                                          
       mStartDate
                l              x                             <td>               P                     l              �                     x                            </td>                l       (                              �                                             l       R       �                                                 mEndDate                m              �                             <td>               �                     m                                   �                            </td>                m       (                                                                           m       (                                     </tr>                                       n       *                              �                                              o       1                              �                                            o       7       @                                            B        <tr><td colspan=6 style="text-align:left;padding-top:30px;">Find B                p       8       @                     @                                            p       7       @                     @                             record(s)</td></tr>                p       (                              @                                             p       (                                     </table></form>                                       q       *                              �                                              r       O                              =       <script>alert("License not found!!");window.close();</script>=                                       s       *                              �                                              v       (                              ^              <form action="lic_license_extend.php" method="POST" style="display:none;">
            <input type="hidden" id="licenseString" name="licenseString" value="">
            <input type="hidden" id="queryValue" name="queryValue" value="">
            <input type="hidden" id="queryBy" name="queryBy" value="">
        </form>
    </body>
</html>
^                                             >                                                                            i              ����    O   L   [   ����r   o   x   �����   �   �   ����  /       /var/www/html/blog/lic_license_query_submit.php����                             ����                                queryBy�x�x� 
       
       queryValue]��)���              oDbz�|                 oLicenseQ�q�w              oLicenseProduct�0��              oProductType�]����              oModule��Y�               aryMacAddress�N��|�]              aryMacAddressSizeC��FH��              oMacAddress+b.C'���
       
       aryLicense�F��6]p�              nLicenseCount�5�-�$�         