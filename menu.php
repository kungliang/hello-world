
<div id="menu" style="width:100%; border-bottom:1px solid silver;">
    <ul class="menu">
        <li><img width=255 src="/TSMS/images/TTDD.png" style="margin-right:15px"></li>
        <li style="padding-top:5px"><a href="/TSMS/home.php"><span style="color:olive;">HOME</span></a></li>
        <li style="padding-top:5px"><a href=""><span style="color:olive;">LICENSE</span></a>
            <div>
                <ul>
                    <li style="border-bottom:1px dotted silver;"><a href="/TSMS/license/lic_license_query.php"><span>License Query</span></a></li>
                </ul>
            </div>
        </li>
        <li style="padding-top:5px"><a href="/TSMS/logout.php"><span style="color:olive;">LOGOUT</span></a></li>
    </ul>
</div>
<div id="copyright" style="display:none">Copyright &copy; 2014 <a href="http://apycom.com/">Apycom jQuery Menus</a></div>
<script>    
    $("div#menu").css("background-color","#fff");
    $("ul.menu").css("height","40px").css("margin-top","10px").css("background-color","#fff").css("width","100%").css("font-size","13px").hover(function(){
//        $(this).css("background-color","wheat");
    },function(){
        $(this).css("background-color","white");
    });
    $("ul.menu span").css("color","olive").hover(function(){
        $(this).css("color","blue");
        $(this).css("background-color","#FFFACD");
    },function(){
        $(this).css("color","olive");
        $(this).css("background-color","#fff");
    });
{{{{{{{{{{{{{{</script>
