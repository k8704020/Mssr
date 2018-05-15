<?php
function db_radio($id,$sel,$arry_op=''){
//-------------------------------------------------------
//函式: db_radio()
//用途: 資料繫結圈選方塊
//-------------------------------------------------------
//$id       圈選方塊 id
//$sel      選取值
//$arry_op  設定參數陣列,可用參數如下,0停用,1啟用
//          'disabled'=>0|1
//-------------------------------------------------------

    //隨機id
    mt_srand(time());
    $rnd=mt_rand();
    $rnd=str_split(strval($rnd),1);
    $str=str_shuffle('abcdefghijklmnopqrstuvwxyz');
    shuffle($rnd);

    //參數處理

    //函數名稱
    $func_id='db_radio_'.substr($str,0,3).join($rnd);
?>
    <script type="text/javascript">
    //---------------------------------------------------
    //資料繫結圈選方塊
    //---------------------------------------------------
    var id  ='<?php echo addslashes($id);?>';
    var sel ='<?php echo addslashes($sel);?>';

    <?php echo $func_id;?>(id,sel);

    function <?php echo $func_id;?>(id,sel){

        //取物件
        var oRds=document.getElementsByName(id);
        if(!oRds){
            return false;
        }

        //核取處理
        for(var i=0;i<oRds.length;i++)
        {
            var oRd=oRds[i];

            if(oRd.value==sel){
                oRd.checked=true;
            }

            //額外設定
            <?php if(!empty($arry_op)):?>
                <?php if($arry_op['disabled']===1):?>
                oRd.disabled=true;
                <?php endif;?>
            <?php endif;?>
        }
    }
    </script>

<?php } ;?>