<?php
//-------------------------------------------------------
//函式: choose_class_code()
//用途: 切換班級
//日期: 2013年9月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function choose_class_code($rd=0){
    //---------------------------------------------------
    //函式: choose_class_code()
    //用途: 切換班級
    //---------------------------------------------------
    //$rd   層級指標,預設0,表示在目前目錄下
    //---------------------------------------------------

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

            if((!isset($_SESSION['tc'][3]))||(!is_array($_SESSION['tc'][3]))){
                die();
            }else{
                if(empty($_SESSION['tc'][3])){
                    die();
                }
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            $output='';

            foreach($_SESSION['tc'][3]['arrys_class_code'] as $inx=>$arry_class_code){
                $class_code     =trim($arry_class_code['class_code']);
                $class_category =(int)$arry_class_code['class_category'];
                $grade          =(int)$arry_class_code['grade'];
                $classroom      =(int)$arry_class_code['classroom'];
                $semester_code  =trim($arry_class_code['semester_code']);

                $output.='<a href="javascript:';
                $output.="sel_class_code({$rd},'{$class_code}',{$class_category},{$grade},{$classroom},'{$semester_code}');";
                $output.='">';

                $output.='<table width="100px" cellpadding="0" cellspacing="0" border="1" align="center" onclick="';
                $output.="sel_class_code({$rd},'{$class_code}',{$class_category},{$grade},{$classroom},'{$semester_code}');";
                $output.='"';
                $output.="
                        style='position:relative;float:left;margin:10px 0;margin-left:20px;background-color:#c6ecff;border-color:#c6ecff;'/>
                            <tr align='center'>
                                <td height='100px'>
                                    <span class='fc_gray0'>
                ";
                $output.="{$grade}年{$classroom}班";
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

            echo $output;
    }
?>

