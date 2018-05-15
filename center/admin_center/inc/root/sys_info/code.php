<?php
function sys_info(){
//-------------------------------------------------------
//系統資訊
//-------------------------------------------------------

    //模組個數
    $cno=1;

    //設定
    $version ="版本";
    $author  ="作者";
    $sysadmin="管理";
    $mod_nos ="模組";

    $arry=array(
        "{$version }"=>"1.0, 2015/7/31",
        "{$author  }"=>"Tardor, Peter",
        "{$sysadmin}"=>"mssr@cl.ncu",
        "{$mod_nos }"=>"{$cno}"
    );
?>
    <ol style="list-style:none;padding:0;margin:0;">
    <?php foreach($arry as $key=>$val) :?>
        <li style="margin-top:5px;margin-left:5px;margin-bottom:5px;" <?php if($key==='模組')echo 'mod_nos="1"';?>>
            <?php echo $key;?>:<?php echo $val;?>
        </li>
    <?php endforeach ;?>
    </ol>
<?php } ?>
