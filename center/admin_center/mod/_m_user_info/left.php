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
    //功能陣列
    //---------------------------------------------------
    //設定個人密碼  user_pwd_setup

        $arrys_func=array(
            'user_pwd_setup'=>array(
                'func_state'=>'on',
                'func_cname'=>'設定個人密碼',
                'func_img'  =>'img/user_pwd_setup.png',
                'func_path' =>'pwd/pwdF.php'
            )
        );

    //---------------------------------------------------
    //通用設定
    //---------------------------------------------------

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
    <script type="text/javascript" src="../../js/table/table_hover.js"></script>

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

    <form name="Form1" id="Form1" action="" method="POST" onsubmit="return false">

        <table id="tbl1" border="1" width="300px" class="table_style1">
            <tr height='30px' align='center' valign='center' class="bar1">
                <td>
                    請選擇欲執行的工作
                </td>
            </tr>

            <?php foreach($arrys_func as $key=>$arry_fun) :?>
            <?php
            $func_name  =$key;
            $func_state =$arry_fun['func_state'];
            $func_cname =$arry_fun['func_cname'];
            $func_img   =$arry_fun['func_img'];
            $func_path  =$arry_fun['func_path'];

            $url ="";
            $page=$func_path;
            $arg =array(
                'account_id'=>$account_id,
                'rnd'       =>mt_rand()
            );
            $url="{$page}?".http_build_query($arg);

            if($func_state=='off'){
                continue;
            }
            ?>
            <tr height='30px' align='left' valign='center'>
                <td>
                    <img src="<?php echo $func_img;?>" alt="功能圖示" style='margin:0px 10px;vertical-align:baseline'>
                    <a href='<?php echo $url;?>' target="IFC"><?php echo $func_cname;?></a>
                </td>
            </tr>
            <?php endforeach ;?>
        </table>

    </form>


<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------
    var oForm1=document.getElementById("Form1");

    table_hover(tbl_id='tbl1',c_odd='#ffffff',c_even='#ebece6',c_on='#99ccff');
</script>

</Body>
</Html>