<?php
function db_select($id,$by,$create,$sel,$arry_val,$arry_txt,$arry_op=''){
//-------------------------------------------------------
//函式: db_select()
//用途: 資料繫結下拉選單
//-------------------------------------------------------
//$id       下拉選單 id
//$by       val=值比對,txt=文字比對
//$create   是否新建
//$sel      選取值
//$arry_val 選項值陣列
//$arry_txt 選項文字陣列
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
    $arry_val="['".implode("','",$arry_val)."']";
    $arry_txt="['".implode("','",$arry_txt)."']";

    //函數名稱
    $func_id='db_select_'.substr($str,0,3).join($rnd);
?>
    <script type="text/javascript">
    //---------------------------------------------------
    //資料繫結下拉選單
    //---------------------------------------------------
    var id      ='<?php echo addslashes($id);?>';
    var by      ='<?php echo addslashes($by);?>';
    var create  ='<?php echo addslashes($create);?>';
    var sel     ='<?php echo addslashes($sel);?>';
    var arry_val=<?php echo ($arry_val);?>;
    var arry_txt=<?php echo ($arry_txt);?>;

    <?php echo $func_id;?>(id,by,create,sel,arry_val,arry_txt);

    function <?php echo $func_id;?>(id,by,create,sel,arry_val,arry_txt){

        //取物件
        var oSel=document.getElementById(id);
        if(!oSel){
            return false;
        }

        //核取處理
        for(var i=0;i<arry_val.length;i++){

            //是否新建
            if(create==1){
                var oOpt=document.createElement('option');
                oOpt.value=arry_val[i];
                oOpt.text =arry_txt[i];

                oSel.options.add(oOpt);
            }else{
                try{
                    var oOpt=oSel.options[i];
                }catch(e){}
            }

            try{
                if(by=='val'){
                //選項值比對
                    if(arry_val[i]==sel){
                        oSel.focus();
                        oOpt.selected=true;
                    }
                }else{
                //選項文字比對
                    if(arry_txt[i]==sel){
                        oSel.focus();
                        oOpt.selected=true;
                    }
                }
            }catch(e){}
        }

        //額外設定
        try{
            <?php if(!empty($arry_op)):?>
                <?php if($arry_op['disabled']===1):?>
                oSel.disabled=true;
                <?php endif;?>
            <?php endif;?>
        }catch(e){}
    }
    </script>

<?php } ;?>