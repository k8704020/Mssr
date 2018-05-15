<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
//---------------------------------------------------
//設定與引用
//---------------------------------------------------

	//SESSION
	@session_start();

	//啟用BUFFER
	@ob_start();

	//外掛設定檔
	require_once(str_repeat("../",1)."/config/config.php");

	 //外掛函式檔
	$funcs=array(
				APP_ROOT.'inc/code',
				APP_ROOT.'lib/php/array/code'
				);
	func_load($funcs,true);


	//清除並停用BUFFER
	@ob_end_clean();



//---------------------------------------------------
//END   設定與引用
//---------------------------------------------------

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
<meta name="description" content="Your description goes here" />
<meta name="keywords" content="明日星球,中央大學明日星球" />
<script src="bookstore_v2/js/select_thing.js" type="text/javascript"></script>

<title>明日閱讀</title>


<?php
    $forum_flag=true;
    if((!isset($_SESSION['class'][0][0]))||(!isset($_SESSION['class'][0][1]))){
        $forum_flag=false;
    }else{
        if((!in_array(trim($_SESSION['class'][0][0]),array('i_t','i_s')))||(!in_array(trim($_SESSION['class'][0][1]),array('test_2014_2_1_1','gcp_2014_2_5_5','gcp_2015_1_1_1','test_2015_1_1_1_1')))){
            $forum_flag=false;
        }
    }
?>
<script>
    $(document).ready(function(){
        $("title").html("明日星球:明日閱讀");
        $("#titlename").html("明日閱讀");
    });
</script>
<style>
    .buttoms{
        height: 46px;
    }
</style>
<table align="center" border="0" width="1024" bgcolor="#fff2c6" cellpadding="0" cellspacing="0">
    	<tr style="font-size:12px; color:#000099;">
        	<td valign="bottom" align="left" colspan="2" style="font-size:18px; font-weight:900; color:#006699">

           	明日書店

            <td valign="bottom" align="right" style="font-size:12px; color:#000099;">
                	<a><? echo $_SESSION["name"]?></a> | <a href='/ac/index.php' style='color:#000099' target='_self'>回明日星球首頁</a>
            </td>
            </td>
        </tr>
        <tr valign="top"  border="0">
        	<td colspan="3"><a href="../../ac/index.php" title="按此回首頁" name="按此回首頁" target="_self" border="0" ><img src="img/banner new(0120)_0217.png" border="0"></a></td>
        </tr>
        <tr valign="top"  border="0">
        	<td colspan="3" align="center"  border="0">
<table id="___01" width="798px"  border="0"  height="520" cellpadding="0" cellspacing="0" style='position:relative;'>
    <tr  border="0"  >
        <td colspan="7"  border="0">
            <img src="img/sto_01.png" width="798px" height="372px" alt=""  border="0">
        </td>
    </tr>
    <tr  border="0">
        <td class='buttoms'  border="0">
            <img src="img/sto_02.png" width="101px" height="46px" alt=""  border="0"></td>
        <td class='buttoms'  border="0">
            <a href="code.php?mode=read_the_registration" target="_self" border="0"><!--閱讀登記-->
                <img src="img/sto_03.png" width="116px" height="46px" alt="閱讀登記"  border="0">
            </a>
        </td>
        <td class='buttoms'>
            <img src="img/sto_04.png" width="160px" height="46px" alt=""  border="0"></td>
        <td class='buttoms'>
            <a href='code.php?mode=bookstore' target="_self" border= "0"><!--明日書店-->
                <img src="img/sto_05.png" width="119px" height="46px" alt="明日書店 " border="0">
            </a>
        </td>
        <td class='buttoms'>
            <img src="img/sto_06.png" width="99px" height="46px" alt="" border="0"></td>
        <td class='buttoms'>
            <a href="../../draw_story/storyBooks.php" border = "0"><!--說書人-->
                <img src="img/sto_07.png" width="118px" height="46px" alt="說書人" border="0">
            </a>

        </td>
        <td class='buttoms'>
            <img src="img/sto_08.png" width="85px" height="46px" alt="" border="0">
        </td>
    </tr>
    <tr>
        <td colspan="7">
            <img src="img/sto_09.png" width="798px" height="181px" alt="" border="0">
        </td>
    </tr>
    <?php if($forum_flag):?>
    <tr>
        <td colspan="7">
            <table cellpadding="0" cellspacing="0" border="0" width="100%" style='position:absolute;left:370px;bottom:50px;'/>
                <tr>
                    <td>
                        <a href="dev_forum/view/index.php" border="0" target='_blank'>
                            <img src="forum/image/obj/forum.png" width="137" height="59" alt="聊書" border="0">
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <?php endif;?>
</table>

 </td>
      	</tr>
        <tr>
        	<td colspan="3" background="img/copyright.png" height="79" align="center" valign="bottom" style="font-size:12px; color:#666666">
            	校址：(32001)桃園縣中壢市五權里2鄰中大路300號‧總機電話：03-4227151<br />
                國立中央大學 版權所有 &copy; 2008-2011 National Central University All Rights Reserved.
            </td>
        </tr>
    </table>
