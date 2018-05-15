<?php
function server_print(){
//-------------------------------------------------------
//函式: server_print()
//用途: 列印出SERVER變數
//日期: 2013年1月23日
//作者: jeff@max-life
//-------------------------------------------------------
?>
    <table id="" width="100%" align="center" border="1">
        <?php foreach($_SERVER as $key=>$val){ ?>
        <?php
        if(is_array($val)){
            $val=empty($val)?'&nbsp;':"<pre>".print_r($val,true)."</pre>";
        }else{
            $val=(trim($val)=='')?'&nbsp;':$val;
        }

        ?>
            <tr>
                <td><?php echo $key;?></td>
                <td><?php echo $val;?></td>
            </tr>
        <?php } ?>
    </table>
<?php
    die();
}
?>