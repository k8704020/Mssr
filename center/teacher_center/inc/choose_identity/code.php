<?php
//-------------------------------------------------------
//函式: choose_identity()
//用途: 切換身份
//日期: 2013年9月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function choose_identity($rd=0){
    //---------------------------------------------------
    //函式: choose_identity()
    //用途: 切換身份
    //---------------------------------------------------
    //$rd   層級指標,預設0,表示在目前目錄下
    //---------------------------------------------------
            return false;
        //-----------------------------------------------
        //接收參數
        //-----------------------------------------------

            global $_SESSION;

        //-----------------------------------------------
        //設定參數
        //-----------------------------------------------

        //-----------------------------------------------
        //檢驗參數
        //-----------------------------------------------

            if(!isset($rd)){
                $rd=0;
            }else{
                $rd=(int)$rd;
            }

            if((!isset($_SESSION['tc']))||(!is_array($_SESSION['tc']))){
                die();
            }else{
                if(empty($_SESSION['tc'])){
                    die();
                }
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $output='';

            foreach($_SESSION['tc'] as $identity=>$arry_tc){
                $identity=(int)$identity;

                $identity_ch='';
                switch($identity){
                    case 2:
                        $identity_ch='主任';
                    break;

                    case 3:
                        $identity_ch='老師';
                    break;
                }
                if(($identity===2)||($identity===3)){

                    $output.="
                        <a href='javascript:sel_identity({$rd},{$identity});'>
                    ";
                    $output.="
                            <table width='100px' cellpadding='0' cellspacing='0' border='1' align='center' onclick='sel_identity({$rd},{$identity});'
                            style='position:relative;float:left;margin:10px 0;margin-left:20px;background-color:#c6ecff;border-color:#c6ecff;'/>
                                <tr align='center'>
                                    <td height='100px'>
                                        <span class='fc_gray0'>
                    ";
                    $output.="{$identity_ch}";
                    $output.='
                                        </span>
                                    </td>
                                </tr>
                            </table>
                    ';
                    $output.='
                        </a>
                    ';
                }
            }

            echo $output;
    }
?>

