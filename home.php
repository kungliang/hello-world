<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Welcome to TTDD TSMS </title>
        <link rel="stylesheet" href="css/demos.css">
        <link rel="stylesheet" href="css/tsms.base.css">
        <link rel="stylesheet" href="css/tsms.textfield.css">
        <link rel="stylesheet" href="css/tsms.select.css">
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="js/libs/jqueryui/css/ui-lightness/jquery.ui.all.css">
        <link rel="stylesheet" href="js/libs/datatables/css/jquery.dataTables.css">
        <script src="js/libs/jquery/jquery.js"></script>
        <script src="js/libs/jqueryui/jquery-ui.js"></script>
        <script src="js/libs/datatables/jquery.dataTables.js"></script>
        <script src="js/tsms.formcheck.js"></script>     
        <script src="js/menu.js"></script>
    </head>
    <body>
        <?php
        include "class/CSession.php";
        include "menu.php";
        echo "Welcome ".$_SESSION["USER_NAME"];
        ?>
    </body>
</html>
