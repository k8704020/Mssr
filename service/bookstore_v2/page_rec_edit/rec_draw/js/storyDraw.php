<?php

require_once("functions.php");
session_start();

?>
<!DOCTYPE html> 
<html onselectstart="return false" ondragstart="return false">
<head>
<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

<title>DrawStory Draw</title>
 
<link href="css/main2.css" rel="stylesheet" type="text/css">
<link href="js/jquery-ui-1.8.18.custom/css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css"> 

<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="js/jquery.tabSlideOut.v1.3.js"></script>
<script src="js/jquery.blockUI.js"></script>


<script>
var storyId = <? echo $_POST["book_ID"] ?>;
var page_num = <? echo "1";?>;
var drawPage = <? echo "1"; ?>;
</script>

<script src="js/drawDefine.js"></script> 
<script src="js/drawFunction.js"></script>

</head>
<body>
<? echo $_POST["book_ID"]; ?>
	<div class="wrap">
       
        <div id="home" class="topright fbutton">
            <img style src="img/1331868202_9.png" height="32px" title="回列表"/>
        </div>
        <!--<div id="toWrite" style="border:2px solid red;" class="topright fbutton">
            <img style src="img/CtoWrite.png" height="32px" title="切換至寫作區" />  -->
        </div>


        <!--<div id="d4" class="topright fbutton switch">4</div>
        <div id="d3" class="topright fbutton switch">3</div>
        <div id="d2" class="topright fbutton switch">2</div>
        <div id="d1" class="topright fbutton switch">1</div>-->

        <div id="nickname">作品名稱: <? echo 'now'; ?> | 正在編輯 Page<?php echo 'now'; ?></div>

        <div class="toolbar" id="ftool">
            <div id="undo" class="fbutton">
                <img src="img/trans.png" alt="undo" height="32px" title="回復筆畫"/>
            </div>
            <div id="newPage" class="fbutton">
                <img src="img/1330996603_Default_Document-64.png" alt="new"
                    height="32px" title="清空畫布"/>
            </div>
            <div id="chBcColor" class="fbutton">
                <img src="img/1331629987_22.png" alt="save" height="32px" title="更換底色"/>
            </div>
            <div id="saveImg" class="fbutton">
                <img src="img/1330996597_Floppy-64.png" alt="save" height="32px" title="存檔"/>
            </div>
            <!--<div id="addPic" class="fbutton">
                <img src="img/1342428344_Folder - Default.png" alt="save" height="30px" />
            </div>-->
            <canvas id="paperColor" class="bordered" width="256xpx" height="256px">
                Sorry, your browser is not supported.</canvas>
        </div>

        <canvas id="sketchpad" class="bordered" width="1010" height="490">
            Sorry, your browser is not supported.</canvas>

        <div class="toolbar" id="dtool">
            <a class="handle" href="javascript:void(0);">調色盤</a>
            <div id="lw_container">
                <div id="lw_slider"></div>
                <div id="lw_show">
                    <div></div>
                </div>
            </div>
            <div id="pen" class="dbutton">
                <img src="img/1331014433_brush.png" />
            </div>
            <div id="eraser" class="dbutton">
                <img src="img/1331023433_clear.png" width="80" />
            </div>

            <div id="cp_container">
                <!--<div id="cp_slide">
                    <div id="red"></div>
                    <div id="green"></div>
                    <div id="blue"></div>
                    </div>
                    <div id="swatch" class="ui-widget-content ui-corner-all"></div>-->

                <canvas id="cStamp" class="bordered" width="182px" height="182px">
                    Sorry, your browser is not supported.</canvas>
            </div>

            <div id="toolLock"></div>
        </div>
        <input style="display:none;" id="upload" type="file" multiple/>

	</div>
    <div style="display:none;" id="wait">123</div>
</body>
</html>