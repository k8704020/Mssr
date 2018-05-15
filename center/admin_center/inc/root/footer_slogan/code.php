<?php
//-------------------------------------------------------
//註腳
//-------------------------------------------------------

    function footer_slogan(){
        $year=date("Y");
        $year=((int)$year)-1;
        $corp="國立中央大學 版權所有 © 2008-{$year} National Central University All Rights Reserved.";
?>
    <p><a href="javascript:void(0);"><?php echo $corp;?></a></p>
<?php }?>