<?php
function db_checkbox($id,$arry_sel,$arry_op=''){
//-------------------------------------------------------
//函式: db_checkbox()
//用途: 資料繫結核取方塊
//-------------------------------------------------------
//$id       核取方塊 id
//$arry_sel 選取值陣列
//$arry_op  設定參數陣列,可用參數如下,0停用,1啟用
//          'disabled'=>0|1
//-------------------------------------------------------

    //隨機id
    mt_srand(time());
    $rnd=mt_rand();
    $rnd=str_split(strval($rnd),1);
    $str=str_shuffle('abcdefghijklmnopqrstuvwxyz');
    shuffle($rnd);

    //參數處理(轉js陣列)
    $arry_sel="['".implode("','",$arry_sel)."']";

    //函數名稱
    $func_id='db_checkbox_'.substr($str,0,3).join($rnd);
?>
    <script type="text/javascript">
    //---------------------------------------------------
    //資料繫結核取方塊
    //---------------------------------------------------
    var id      ='<?php echo addslashes($id);?>';
    var arry_sel=<?php echo ($arry_sel);?>;

    <?php echo $func_id;?>(id,arry_sel);

    function <?php echo $func_id;?>(id,arry_sel){

        //取物件
        var oChs=document.getElementsByName(id);
        if(!oChs){
            return false;
        }

        //核取處理
        for(var i=0;i<oChs.length;i++)
        {
            var oCh=oChs[i];
            for(var j=0;j<arry_sel.length;j++)
            {
                if(oCh.value==arry_sel[j]){
                    oCh.checked=true;
                    break;
                }
            }

            //額外設定
            <?php if(!empty($arry_op)):?>
                <?php if($arry_op['disabled']===1):?>
                oCh.disabled=true;
                <?php endif;?>
            <?php endif;?>
        }
    }
    </script>

<?php } ;?>