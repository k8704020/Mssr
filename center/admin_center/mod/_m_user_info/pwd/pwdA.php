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
                    APP_ROOT.'lib/public/robots',
                    APP_ROOT.'lib/db/db_result',
                    APP_ROOT.'lib/db/mysql_prep',
                    APP_ROOT.'lib/session/session_end',
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
    //id    主索引      v
    //uid   帳號        v
    //pwd1  新密碼      v
    //pwd2  確認密碼    v

        $post_chk=array(
            'id  ',
            'uid ',
            'pwd1',
            'pwd2'
        );
        foreach($post_chk as $post){
            if(!isset($_POST[trim($post)])){
                die();
            }
        }


    //---------------------------------------------------
    //設定參數
    //---------------------------------------------------
    //id    主索引      v
    //uid   帳號        v
    //pwd1  新密碼      v
    //pwd2  確認密碼    v

        //POST
        $id  =trim($_POST['id']);
        $uid =trim($_POST['uid']);
        $pwd1=trim($_POST['pwd1']);
        $pwd2=trim($_POST['pwd2']);

        //強制以SESSION的為主,避免串改
        $id  =(int)$_SESSION['sys_account']['id'];
        $uid =trim($_SESSION['sys_account']['uid']);

        //分頁
        //$psize=(isset($_POST['psize']))?(int)$_POST['psize']:10;
        //$pinx =(isset($_POST['pinx']))?(int)$_POST['pinx']:1;
        //$psize=($psize===0)?10:$psize;
        //$pinx =($pinx===0)?1:$pinx;

    //---------------------------------------------------
    //檢驗參數
    //---------------------------------------------------
    //id    主索引      v
    //uid   帳號        v
    //pwd1  新密碼      v
    //pwd2  確認密碼    v

        $arry_err=array();

        if($id===''){
           $arry_err[]='主索引,未輸入!';
        }else{
           $id=(int)$id;
           if($id===0){
              $arry_err[]='主索引,不為整數!';
           }
        }

        if($uid===''){
           $arry_err[]='帳號,未輸入!';
        }

        if(($pwd1=='')||($pwd2=='')){
           $arry_err[]='新密碼或確認密碼,未輸入!';
        }else{
            if($pwd1!=$pwd2){
               $arry_err[]='新密碼,比對確認密碼失敗!';
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

            $tbl_name="sys_account";
            $sql ="";
            $sql.="SELECT * FROM `{$tbl_name}` WHERE 1=1"." ";
            $sql.="AND `id`   ='".mysql_prep($id )."'"." ";
            $sql.="AND `uid`  ='".mysql_prep($uid)."'"." ";
            $sql.="AND `state`=1"." ";
            //echo $sql.'<br/>';

            $arrys_result=db_result($conn,$sql,$arry_limit=array(0,1),$arry_conn);

            if(empty($arrys_result)){
                die();
            }

        //-----------------------------------------------
        //脫序
        //-----------------------------------------------

            $id  =mysql_prep($id  );    //主索引
            $uid =mysql_prep($uid );    //帳號
            $pwd =md5($pwd1);           //密碼

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $tbl_name="sys_account";
            $sql ="";
            $sql.="UPDATE `{$tbl_name}` SET "."{$nl}";
            $sql.="`pwd`     ='{$pwd }'"." "."{$nl}";
            $sql.="WHERE `id`='{$id  }'"." "."{$nl}";
            $sql.="AND `uid` ='{$uid }'"." "."{$nl}";
            $sql.="AND `state`=1"." ";
            $sql.="LIMIT 1"." "."{$nl}";
            //echo $sql.'<br/>';

            @mysql_query($sql,$conn) or
            die('db query fail');

            @mysql_close($conn);

    //---------------------------------------------------
    //清除
    //---------------------------------------------------

        //清除所有SESSION
        session_end(TZone);

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
    </style>
</Head>

<Body>


<script type="text/javascript">
//-------------------------------------------------------
//頁面初始
//-------------------------------------------------------

    var oForm1=document.getElementById('Form1');    //表單
    var nl="\r\n";

    window.onload=function(){

        var arry_msg=[
            '您的資料已經更新完畢,系統已將您登出,請重新登入!',
        ];
        alert(arry_msg.join(nl));

        var url ='';
        var page='../../login/loginF.php';
        var arg ='';

        if(arg.length!==0){
            url=page+'?'+arg;
        }else{
            url=page;
        }
        go(url,'parent');
    }
</script>

</Body>
</Html>