<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Welcome to TTDD License Query</title>
    </head>
        <link rel="stylesheet" href="../css/demos.css">
        <link rel="stylesheet" href="../css/tsms.base.css">
        <link rel="stylesheet" href="../css/tsms.textfield.css">
        <link rel="stylesheet" href="../css/tsms.select.css">
        <link rel="stylesheet" href="../css/menu.css">        
        <link rel="stylesheet" href="../js/libs/jqueryui/css/ui-lightness/jquery.ui.all.css">
        <link rel="stylesheet" href="../js/libs/datatables/css/jquery.dataTables.css">
        <script src="../js/libs/jquery/jquery.js"></script>
        <script src="../js/libs/jqueryui/jquery-ui.js"></script>
        <script src="../js/libs/datatables/jquery.dataTables.js"></script>
        <script src="../js/tsms.formcheck.js"></script>  
        <script src="../js/menu.js"></script>  
        <script>
            $(function(){
               var queryValue = $("#queryValue");
               setInEditClass(queryValue);
               $("input[name='queryBy']").click(function(){
                   $(queryValue).val("");
               });
              $("button").css("font-size","12px").button().click(function(){
                  try{
                      if($("input[name='queryBy']:checked").val()=="macAddress"){
                          var regEx=/^([0-9A-F]{2}[-]){5}([0-9A-F]{2})$/ //mac address;
                          checkIsEmpty($(queryValue),"Mac address is empty!!");
                          checkIsFormat($(queryValue),"Mac address format incorrect, \nEx:AA-BB-CC-DD-EE-FF",regEx);
                      }else{
                          checkIsEmpty($(queryValue),"License is empty!!");
                      }
                      var w =(screen.width-500)/2;
                      var h= ((screen.height-500)/2) -10;
                      var state_str = "width=900, height=768, toolbar=no,resizable=no";
                         state_str = state_str + ",left=" + w + ",top=" +h;
                      var licenseQueryWindow = window.open("","licenseQueryWindow",state_str);
                          licenseQueryWindow.focus();
                          document.forms[0].submit();
                  }catch(e){
                      alert(e.message);
                  }
              }); 
               
            });
        </script>
    <body>
        <?php
            include "../class/CSession.php";
            include "../menu.php";
        // put your code here
        ?>
        <h2>Welcome to TTDD License Query</h2>
        <form target="licenseQueryWindow" action="lic_license_query_submit.php" method="POST" id="requestForm">
            <table>
                <tr>
                    <th>
                        <div class="step_guide">Query By : </div>
                    </th>
                </tr>                
                <tr>
                    <td>
                        <input type="radio" name="queryBy" value="license" checked id="queryByLicense">License 
                        <input type="radio" name="queryBy" value="macAddress" id="queryByMacAddress">Mac Address
                    </td>
                </tr>
                 <tr id="licenseCodeRow">
                     <td>
                        <textarea id="queryValue" cols=30 rows=6 name="queryValue"></textarea>
                        (Enter license code or mac address to query)
                    </td>
                </tr>    
                 <tr>
                     <td>
                        <button type="button"><img class="icon" src="../images/icons/Ok-icon.png">Submit</button>
                    </td>
                </tr>                  
        </form>
    </body>
</html>
