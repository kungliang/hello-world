<?php
session_start();
  
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<html>
    <head>
        <meta charset="UTF-8">
        <title>TSMS-Login</title>
        <link rel="stylesheet" href="css/demos.css">
        <link rel="stylesheet" href="css/tsms.base.css">
        <link rel="stylesheet" href="css/tsms.textfield.css">
        <link rel="stylesheet" href="js/libs/jqueryui/css/ui-lightness/jquery.ui.all.css"> 
        <script src="js/libs/jquery/jquery.js"></script>
        <script src="js/libs/jqueryui/jquery-ui.js"></script>
        <script src="js/tsms.formcheck.js"></script>
    </head>   
    <script>
        $(function(){
            $("button").css("font-size","12px").click(function(){
               document.forms[0].submit(); 
            });
            //in tkms.formcheck.js
            setInEditClass($("#userName"));
            setInEditClass($("#userPassword"));
        });
        
    </script>
    <style>
        #loginTable {
            /*border:4px double lightblue;*/
            padding:30px;
        }
        #loginMessage {
            color:red;
            text-align:left;
            pdding:0 25px;
        }
        th {
            padding-right: 20px;
        }
        body {
            margin: 50;
            text-align: center;
            padding:100;
                
        }
        #TSMS_HOME{
            position: relative;
            margin: 0 auto;
            width: 500px; 
            text-align: left;
        }
        h2 {
            margin-left:20px;
        }
        sub{
            font-style:italic;
            font-size:12px;
        }
        form {
            width:400px;
            padding:20px;
            padding-top:30px;
            border:1px solid black;
        }
    </style>    
    <body>
        <?php
        // put your code here
        ?>
        <div id="TSMS_HOME">
            <h2>Welcome to TSMS &nbsp;&nbsp;<sub>&alpha;1.10</sub></h2>
            <form action="login_submit.php" method="POST" style="border:none;">
            <table id="loginTable">
                <tr>
                    <th>Name</th>
                    <th><input type="text" id="userName" name="userName" value="<?php if(isset($userName)) echo $userName;?>"></th>
                </tr>
                <tr>
                    <th>Password</th>
                    <th><input type="password" id="userPassword" name="userPassword" value=""></th>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right;padding-right:20px;padding-top:10px;">
                        <button type="button" id="login" name="loginBn">Login</button>
                        <!--<button type="submit" id="login" name="loginBn">Login</button>-->
                </tr>
                <tr>
                    <td colspan="2" id="loginMessage">
                        <?php if(isset($_SESSION)&& $_SESSION["USER_ERR"]) { echo $_SESSION["USER_ERR"]; } ?>
                    </td>
                </tr>
            </table>
        </form>
        </div>        
    </body>
</html>
