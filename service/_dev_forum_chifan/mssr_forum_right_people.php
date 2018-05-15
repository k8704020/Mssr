
<?php if($sess_uid == $user_id){ ?>
<div class="aside">

<?php
$oright_side=new right_side($sess_uid,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
$arry_member_selfs=$oright_side->member_self($user_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);

//echo "<Pre>";
//print_r($arry_member_selfs);
//echo "</Pre>";

foreach($arry_member_selfs as $inx=>$arry_memberSelf){
    if(!empty($arry_memberSelf)){
        switch($inx){
           case 0:
?>
      <table class="table">
        <thead>
          <tr align="center">
            <TD COLSPAN=2>我可能有興趣的人</TD>
          </tr>
        </thead>
        <tbody>

<?php
                foreach($arry_memberSelf as $arry_memberSelf_k =>$arry_memberSelf_v){

                    foreach($arry_memberSelf_v as $k =>$v){
                        if($arry_memberSelf_k == 'main'){
?>
            <tr>
                <td>
                  <img src="image/boy.jpg" alt=""  width="40" height="40"/>
                </td>
                <td>
                    <?php echo $arry_memberSelf['main'][$k];
                            echo "<br/><span style='font-size:8px'>你們共同讀過:",$arry_memberSelf['submain'][$k],"</span>";
                     }?>
                </td>
          </tr>
<?php
                }
             }
           break;
           case 1:
?>
        <table class="table">
        <thead>
          <tr align="center">
            <TD COLSPAN=2>我可能有興趣的書籍</TD>
          </tr>
        </thead>
        <tbody>
        <?php
                foreach($arry_memberSelf as $arry_memberSelf_k =>$arry_memberSelf_v){

                    foreach($arry_memberSelf_v as $k =>$v){
                        if($arry_memberSelf_k == 'main'){
?>
            <tr>
                <td>
                  <img src="image/book.jpg" alt=""  width="40" height="40"/>
                </td>
                <td>
                    <?php
                       if($arry_memberSelf['submain'][$k]['borrow_cno'] == 0 && empty($arry_memberSelf['submain'][$k]['user_id'])){
                            echo $arry_memberSelf['main'][$k];
                            echo "<br/><span style='font-size:8px'>沒有好友借閱過這本書";

                        }elseif($arry_memberSelf['submain'][$k]['borrow_cno'] == 0){
                             echo $arry_memberSelf['main'][$k];
                             echo "<br/><span style='font-size:8px'>",$arry_memberSelf['submain'][$k]['user_id'],"借閱過這本書";
                        }else {

                            echo $arry_memberSelf['main'][$k];
                            echo "<br/><span style='font-size:8px'>",$arry_memberSelf['submain'][$k]['user_id'],"和其他",$arry_memberSelf['submain'][$k]['borrow_cno'],"人也借過這本書";
                        }
                      }?>
                </td>
          </tr>
<?php
                }
             }
           break;
           case 1:
?>

<?php

           break;
           case 2:
?>
        <table class="table">
        <thead>
          <tr align="center">
            <TD COLSPAN=2>我可能有興趣的聊書小組</TD>
          </tr>
        </thead>
        <tbody>
<?php

                foreach($arry_memberSelf as $arry_memberSelf_k =>$arry_memberSelf_v){

                    foreach($arry_memberSelf_v as $k =>$v){
                        if($arry_memberSelf_k == 'main'){
?>
            <tr>
                <td>
                  <img src="image/group.png" alt=""  width="40" height="40"/>
                </td>
                <td>
                    <?php echo $arry_memberSelf['main'][$k];
                        $member = $arry_memberSelf['submain'][$k];
                        if(!empty($member)){
                          echo "<br/><span style='font-size:8px'>",$arry_memberSelf['submain'][$k],"也加入此小組</span>";
                        }else{
                          echo "<br/><span style='font-size:8px'>",$arry_memberSelf['submain'][$k],"沒有朋友加入此小組</span>";
                        }
                       }?>
                </td>
          </tr>
<?php
                }
             }
           break;
        }
    }
}

?>

        </tbody>
      </table>
    </div>
  </div>
<?php }else {?>

<div class="aside">

<?php
$oright_side=new right_side($sess_uid,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
$arry_member_other=$oright_side->member_other($sess_uid,$user_id,$conn_user,$conn_mssr,$arry_conn_user,$arry_conn_mssr);
//echo "<Pre>";
//print_r($arry_member_other);
//echo "</Pre>";



foreach($arry_member_other as $inx=>$arry_memberSelf){
    if(!empty($arry_memberSelf)){
        switch($inx){
           case 0:
?>
      <table class="table">
        <thead>
          <tr align="center">
            <TD COLSPAN=2>共同好友</TD>
          </tr>
        </thead>
        <tbody>

<?php
                foreach($arry_memberSelf as $arry_memberSelf_k =>$arry_memberSelf_v){

                    foreach($arry_memberSelf_v as $k =>$v){
                        if($arry_memberSelf_k == 'main'){
?>
            <tr>
                <td>
                  <img src="image/boy.jpg" alt=""  width="40" height="40"/>
                </td>
                <td>
                    <?php echo $arry_memberSelf['main'][$k];
                    if(!empty($arry_memberSelf['submain'][$k])){
                          echo "<br/><span style='font-size:8px'>你們共同讀過:",$arry_memberSelf['submain'][$k],"</span>";
                    }else{
                          echo "<br/><span style='font-size:8px'>你們沒有共同讀過的書</span>";
                    }
                          }?>
                </td>
          </tr>
<?php
                }
             }
           break;
           case 1:
?>
        <table class="table">
        <thead>
          <tr align="center">
            <TD COLSPAN=2>共同有興趣的書籍</TD>
          </tr>
        </thead>
        <tbody>
        <?php
                foreach($arry_memberSelf as $arry_memberSelf_k =>$arry_memberSelf_v){

                    foreach($arry_memberSelf_v as $k =>$v){
                        if($arry_memberSelf_k == 'main'){
?>
            <tr>
                <td>
                  <img src="image/book.jpg" alt=""  width="40" height="40"/>
                </td>
                <td>
                    <?php
                        if($arry_memberSelf['submain'][$k]['borrow_cno'] == 0 && empty($arry_memberSelf['submain'][$k]['user_id'])){
                            echo $arry_memberSelf['main'][$k];
                            echo "<br/><span style='font-size:8px'>沒有好友借閱過這本書";

                        }elseif($arry_memberSelf['submain'][$k]['borrow_cno'] == 0){
                             echo $arry_memberSelf['main'][$k];
                             echo "<br/><span style='font-size:8px'>",$arry_memberSelf['submain'][$k]['user_id'],"借閱過這本書";
                        }else {

                            echo $arry_memberSelf['main'][$k];
                            echo "<br/><span style='font-size:8px'>",$arry_memberSelf['submain'][$k]['user_id'],"和其他",$arry_memberSelf['submain'][$k]['borrow_cno'],"人也借過這本書";
                        }
                      }?>
                </td>
          </tr>
<?php
                }
             }
           break;
           case 1:
?>

<?php

           break;
           case 2:
?>
        <table class="table">
        <thead>
          <tr align="center">
            <TD COLSPAN=2>共同加入的聊書小組</TD>
          </tr>
        </thead>
        <tbody>
<?php

                foreach($arry_memberSelf as $arry_memberSelf_k =>$arry_memberSelf_v){

                    foreach($arry_memberSelf_v as $k =>$v){
                        if($arry_memberSelf_k == 'main'){
?>
            <tr>
                <td>
                  <img src="image/group.png" alt=""  width="40" height="40"/>
                </td>
                <td>
                    <?php echo $arry_memberSelf['main'][$k];
                            $member = $arry_memberSelf['submain'][$k];
                            if(empty($member)){
                                echo "<br/><span style='font-size:8px'>沒有朋友加入此小組</span>";
                            }else{
                                echo "<br/><span style='font-size:8px'>",$arry_memberSelf['submain'][$k],"也加入此小組</span>";
                            }

                       }?>
                </td>
          </tr>
<?php
                }
             }
           break;
        }
    }
}

?>

        </tbody>
      </table>
    </div>
  </div>


<?php } ?>


