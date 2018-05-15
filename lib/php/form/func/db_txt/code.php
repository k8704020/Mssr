<?php
function db_txt($id,$val,$arry_op=''){
//-------------------------------------------------------
//函式: db_txt()
//用途: 資料庫繫結文字欄位
//-------------------------------------------------------
//$id       文字欄位id
//$val      欄位值
//$arry_op  設定參數陣列,可用參數如下,0停用,1啟用
//          'disabled'=>0|1
//          'readonly'=>0|1
//-------------------------------------------------------

    //隨機id
    mt_srand(time());
    $rnd=mt_rand();
    $rnd=str_split(strval($rnd),1);
    $str=str_shuffle('abcdefghijklmnopqrstuvwxyz');
    shuffle($rnd);

    //函數名稱
    $func_id='db_txt_'.substr($str,0,3).join($rnd);
?>
    <script type="text/javascript">
    //---------------------------------------------------
    //資料庫繫結文字欄位
    //---------------------------------------------------
    var id ='<?php echo addslashes($id);?>';
    var val='<?php echo addslashes($val);?>';
    <?php echo $func_id;?>(id,val);

    function <?php echo $func_id;?>(id,val){
        var color="#f1f1f1";
        var oTxt =document.getElementById(id);

        if(oTxt){
            oTxt.value=val;
        }

        <?php if(!empty($arry_op)):?>
            <?php if($arry_op['readonly']===1):?>
            oTxt.readOnly=true;
            oTxt.style.backgroundColor=color;
            <?php endif;?>

            <?php if($arry_op['disabled']===1):?>
            oTxt.disabled=true;
            oTxt.style.backgroundColor=color;
            <?php endif;?>
        <?php endif;?>
    }
    </script>

<?php } ;?>