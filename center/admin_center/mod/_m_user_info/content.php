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

        islogin('die');

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
    //SESSION
    //---------------------------------------------------

        //-----------------------------------------------
        //帳戶資料表
        //-----------------------------------------------
        //id          主索引
        //uid         帳號
        //pwd         密碼
        //name        姓名
        //state       狀態
        //ip          登打ip
        //cdate       建立時間
        //edate       存取時間
        //type        帳戶類型
        //ip_arry     允許ip
        //auth_arry   允許權限

        $id            =trim($_SESSION['sys_account']['id']);
        $uid           =trim($_SESSION['sys_account']['uid']);
        $pwd           =trim($_SESSION['sys_account']['pwd']);
        $name          =trim($_SESSION['sys_account']['name']);
        $state         =trim($_SESSION['sys_account']['state']);
        $ip            =trim($_SESSION['sys_account']['ip']);
        $cdate         =trim($_SESSION['sys_account']['cdate']);
        $edate         =trim($_SESSION['sys_account']['edate']);
        $type          =trim($_SESSION['sys_account']['type']);
        $ip_arry       =$_SESSION['sys_account']['ip_arry'];
        $auth_user_arry=$_SESSION['sys_account']['auth_arry'];

    //---------------------------------------------------
    //通用設定
    //---------------------------------------------------

        //type 帳戶類型:0 一般帳戶,1 系統管理者,99 最高管理者
        $arry_type=array(
            0 =>'一般帳戶',
            1 =>'系統管理者',
            99=>'最高管理者'
        );

        $title="個人資訊設定";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<Html>
<Head>
    <Title><?php echo "{$title}" ;?></Title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo Charset;?>">
    <meta http-equiv="Content-Language" content="<?php echo Content_Language;?>">
    <?php echo robots($allow=false);?>

    <link rel="stylesheet" type="text/css" href="../css/site.css" media="all" />

    <script type="text/javascript" src="../../js/date/WdatePicker/WdatePicker.js"></script>
    <script type="text/javascript" src="../inc/dis_txt.js"></script>

    <style>
        body{
            margin:0;
        }
        img,div {
            behavior: url(../../js/image/iepngfix/iepngfix.htc);
        }
        .bar1{
            font-size:12pt;
            color:#333;
        }
    </style>
</Head>

<Body>
<?php
//id          主索引
//uid         帳號
//pwd         密碼
//name        姓名
//state       狀態
//ip          登打ip
//cdate       建立時間
//edate       存取時間
//type        帳戶類型
//ip_arry     允許ip
//auth_arry   允許權限
?>

    <form name="Form1" id="Form1" action="" method="POST" onsubmit="return false">

        <table id="tbl1" border="1" width="650px" align="left" class="table_style1">
            <tr height='30px' class="bar1">
                <td>帳戶資訊</td>
            </tr>
            <tr height='30px'>
                <!-- 登入資訊 -->
                <td class="bg_gray1" align="left" valign="top">
                    <table id="" border="0" width="100%" class='table_style3 bg_gray1'>
                        <tr>
                            <td width="120px" class='fc_green1'>
                                <span class="fc_red1">*</span>
                                帳號
                            </td>
                            <td><?php echo htmlspecialchars($uid);?></td>
                        </tr>
                        <tr>
                            <td width="120px" class='fc_green1'>
                                <span class="fc_red1">*</span>
                                帳戶類型
                            </td>
                            <td><?php echo htmlspecialchars($arry_type[$type]);?></td>
                        </tr>
                        <tr>
                            <td width="120px" class='fc_green1'>
                                <span class="fc_red1">*</span>
                                姓名
                            </td>
                            <td><?php echo htmlspecialchars($name);?></td>
                        </tr>
                        <tr>
                            <td width="120px" class='fc_green1'>
                                建立時間
                            </td>
                            <td><?php echo htmlspecialchars($cdate);?></td>
                        </tr>
                        <tr>
                            <td width="120px" class='fc_green1'>
                                存取時間
                            </td>
                            <td><?php echo htmlspecialchars($edate);?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </form>

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------
    var oForm1=document.getElementById("Form1");

</script>

</Body>
</Html>