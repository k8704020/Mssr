<?php
//-------------------------------------------------------
//社區社群:個人資訊設定
//-------------------------------------------------------

    //---------------------------------------------------
    //設定與引用
    //---------------------------------------------------

        //SESSION
        session_start();

        //外掛通用設定檔
        require_once('../../config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'lib/public/robots',
                    APP_ROOT.'admin/login/islogin',
                    APP_ROOT."admin/inc/header_slogan",
                    APP_ROOT."admin/inc/footer"
                    );
        func_load($funcs,true);
        //session_print();
        //server_print();
        //const_print();
        //func_print();

    //---------------------------------------------------
    //有無登入
    //---------------------------------------------------

        islogin('rd',1);

    //---------------------------------------------------
    //有無權限
    //---------------------------------------------------

        $type='';
        if(isset($_SESSION['sys_account']['type'])){
            $type=(int)$_SESSION['sys_account']['type'];

            //判斷帳戶類型
            if(!in_array($type,array(0,1,99))){
            //0 一般帳戶,1 系統管理者,99 最高管理者
                die();
            }
        }

    //---------------------------------------------------
    //有無帳戶主索引
    //---------------------------------------------------

        if(!isset($_SESSION['sys_account']['id'])){
            die();
        }else{
            $account_id=(int)($_SESSION['sys_account']['id']);

            if($account_id===0){
                die();
            }
        }

    //---------------------------------------------------
    //通用設定
    //---------------------------------------------------

        $title="社區社群:個人資訊設定";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<Html>
<Head>
    <Title><?php echo $title;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo robots($allow=false);?>

    <link rel="stylesheet" type="text/css" href="../css/site.css" media="all" />
</Head>

<Body>

<div id="header"><!-- header 開始 -->
    <a href="#"><img id="logo" src="../css/home.gif"></a>

    <span id="title"><?php echo header_slogan() ;?></span>

    <ul id="navbar">
        <li><a href="#" class="current"><span>個人資訊設定&nbsp;</span></a></li>
        <li><a href="../index.php"><span>回主控台&nbsp;</span></a></li>
        <li><a href="../help/system/user_info/index.php"><span>說明&nbsp;</span></a></li>
        <li><a href="../../index.php"><span>首頁&nbsp;</span></a></li>
        <li><a href="../login/logout.php"><span>登出&nbsp;</span></a></li>
    </ul>
</div><!-- header 結束 -->

<div id="content"><!-- content 開始 -->

    <table id='Tbl0' width='100%' border='0' align='center'>
        <tr align="left" valign="top">
            <!-- 查詢區塊 -->
            <td width="320px">
                <iframe id="IFL" name="IFL" src="left.php" frameborder="0"
                style="width:100%;height:600px;overflow:hidden;overflow-y:auto"></iframe>
            </td>

            <!-- 內容區塊 -->
            <td>
                <iframe id="IFC" name="IFC" src="content.php" frameborder="0"
                style="width:100%;height:600px;overflow:hidden;overflow-y:auto"></iframe>
            </td>
        </tr>
    </table>
</div><!-- content 結束 -->

<div id="footer"><!-- footer 開始 -->
    <?php footer();?>
</div><!-- footer 開始 -->


</Body>
</Html>