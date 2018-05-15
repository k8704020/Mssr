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
        require_once('../../../config/config.php');

        //外掛函式檔
        $funcs=array(
                    APP_ROOT.'lib/db/db_result',
                    APP_ROOT.'lib/db/mysql_prep',
                    APP_ROOT.'lib/form/db_txt',
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
    //接收參數
    //---------------------------------------------------
    //account_id    帳戶主索引

        $get_chk=array(
            'account_id'
        );
        foreach($get_chk as $get){
            if(!isset($_GET[trim($get)])){
                die();
            }
        }

    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //account_id    帳戶主索引

        //GET

        //SESSION
        $account_id=(int)($_SESSION['sys_account']['id']);

        //分頁
        //$psize=(isset($_GET['psize']))?(int)$_GET['psize']:10;
        //$pinx =(isset($_GET['pinx']))?(int)$_GET['pinx']:1;
        //$psize=($psize===0)?10:$psize;
        //$pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //account_id    帳戶主索引

        $arry_err=array();
        if($account_id===''){
           $arry_err[]='帳戶主索引,未輸入!';
        }else{
           $account_id=(int)$account_id;
           if($account_id===0){
              $arry_err[]='帳戶主索引,不為整數!';
           }
        }

        if(count($arry_err)!==0){
            if(1==2){//除錯用
                echo "<pre>";
                print_r($arry_err);
                echo "</pre>";
            }
            die();
        }

    //---------------------------------------------------
    //資料庫
    //---------------------------------------------------

        //-----------------------------------------------
        //通用
        //-----------------------------------------------

            $conn=@mysql_connect(db_host,db_user,db_pass) or
            die('db connect fail');

            @mysql_set_charset(db_encode) or
            die('db set charset fail');

            @mysql_select_db(db_name) or
            die('db select db fail');

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------
        //account_id    帳戶主索引

            $account_id=mysql_prep($account_id);

            $sql="SELECT * FROM `sys_account` WHERE 1=1
                AND `id`='{$account_id}'
                AND `state`=1
            ";

            $arrys_result=db_result($conn,$sql,$arry_limit=array(0,1),$arry_conn);

            if(empty($arrys_result)){
                die('db no exists record');
            }

            @mysql_close($conn);

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

    <link rel="stylesheet" type="text/css" href="../../css/site.css" media="all" />

    <script type="text/javascript" src="../../../js/string/trim.js"></script>
    <script type="text/javascript" src="../../../js/public/go.js"></script>

    <style>
        body{
            margin:0;
        }
        img,div {
            behavior: url(../../../js/image/iepngfix/iepngfix.htc);
        }
        .bar1{
            background:url('../../img/bg/bg02.gif');
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

        <table id="tbl1" border="1" width="100%" class="table_style1">
            <tr height='30px' class="bar1">
                <td>設定個人密碼</td>
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
                        <tr>
                            <td width="120px" class='fc_green1'>
                            <span class="fc_red1">*</span>
                            新密碼
                            </td>
                            <td>
                                <input type="password" id="pwd1" name="pwd1" value="" size="10" maxlength="32" style="width:130px">
                            </td>
                        </tr>
                        <tr>
                            <td width="120px" class='fc_green1'>
                            <span class="fc_red1">*</span>
                            確認密碼
                            </td>
                            <td>
                                <input type="password" id="pwd2" name="pwd2" value="" size="10" maxlength="32" style="width:130px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" id="id" name="id" value="<?php echo $id;?>">
                                <input type="hidden" id="uid" name="uid" value="<?php echo $uid;?>">

                                <input type="button" id="BtnB" name="BtnB" value="返回">
                                <input type="button" id="BtnS" name="BtnS" value="送出">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </form>

    <?php
    //-------------------------------------------------------
    //資料繫結
    //-------------------------------------------------------
    //id          主索引    v
    //uid         帳號      v
    //pwd         密碼
    //name        姓名
    //state       狀態
    //ip          登打ip
    //cdate       建立時間
    //edate       存取時間
    //type        帳戶類型
    //ip_arry     允許ip
    //auth_arry   允許權限

        db_txt('id',$id,$arry_op=array('readonly'=>1,'disabled'=>0));
        db_txt('uid',$uid,$arry_op=array('readonly'=>1,'disabled'=>0));
    ?>

<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------
//id   主索引   v
//uid  帳號     v
//pwd1 新密碼   v
//pwd2 確認密碼 v

    var oForm1=document.getElementById('Form1');    //表單
    var oBtnS =document.getElementById('BtnS');     //送出
    var oBtnB =document.getElementById('BtnB');     //返回

    var o_id   =document.getElementById('id');
    var o_uid  =document.getElementById('uid');
    var o_pwd1 =document.getElementById('pwd1');
    var o_pwd2 =document.getElementById('pwd2');

    oBtnS.onclick=function(){
    //送出
        var nl='\r\n';
        var arry_err=[];

        if(trim(o_pwd1.value)==''){
            arry_err.push('請輸入新密碼!');
        }
        if(trim(o_pwd2.value)==''){
            arry_err.push('請輸入確認密碼!');
        }
        if(trim(o_pwd1.value)!=trim(o_pwd2.value)){
            arry_err.push('新密碼與確認密碼需相同!');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            if(confirm('你確定要修改嗎?')){
                oForm1.action='pwdA.php'
                oForm1.submit();
                return true;
            }
            else{
                alert('修改動作已取消!');
                return false;
            }
        }
    }

    oBtnB.onclick=function(){
    //返回

        var url ='';
        var page='../content.php';
        var arg ='';

        if(arg.length!==0){
            url=page+'?'+arg;
        }else{
            url=page;
        }
        go(url,'self');
    }
</script>

</Body>
</Html>