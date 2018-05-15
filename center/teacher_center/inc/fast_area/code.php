<?php
//-------------------------------------------------------
//函式: fast_area()
//用途: 快速切換
//日期: 2013年9月20日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function fast_area($rd=0){
    //---------------------------------------------------
    //函式: fast_area()
    //用途: 快速切換
    //---------------------------------------------------
    //$rd   層級指標,預設0,表示在目前目錄下
    //---------------------------------------------------

        //-----------------------------------------------
        //接收參數
        //-----------------------------------------------

            global $_SESSION;
            global $arry_conn_user;

        //-----------------------------------------------
        //設定參數
        //-----------------------------------------------

        //-----------------------------------------------
        //檢驗參數
        //-----------------------------------------------

            if(!isset($rd)){
                $rd=0;
                $path='';
            }else{
                $rd=(int)$rd;
                $path=str_repeat('../',$rd);
            }

            if((!isset($_SESSION['tc']))||(!is_array($_SESSION['tc']))){
                return false;
            }else{
                if(empty($_SESSION['tc'])){
                    return false;
                }
            }

            if((isset($_SESSION['tc']['t|dt']))&&(is_array($_SESSION['tc']['t|dt']))){
                $sess_school_code   =trim($_SESSION['tc']['t|dt']['school_code']);
                if(!empty($_SESSION['tc']['t|dt']['arrys_class_code'])){
                    $sess_class_code    =trim($_SESSION['tc']['t|dt']['arrys_class_code'][0]['class_code']);
                    $sess_class_category=(int)$_SESSION['tc']['t|dt']['arrys_class_code'][0]['class_category'];
                    $sess_grade         =(int)$_SESSION['tc']['t|dt']['arrys_class_code'][0]['grade'];
                    $sess_classroom     =(int)$_SESSION['tc']['t|dt']['arrys_class_code'][0]['classroom'];
                }else{
                    return false;
                }
            }else{
                return false;
            }

        //-----------------------------------------------
        //外掛自訂函式檔
        //-----------------------------------------------

            if(!function_exists("get_class_code_info")){
                if(false===@include_once($path.'../../inc/get_class_code_info/code.php')){
                    return false;
                }
            }

            //轉換班級名稱
            if((isset($_SESSION['tc']['t|dt']))&&(is_array($_SESSION['tc']['t|dt']))){
                $new_classroom='';
                $get_class_code_infos=get_class_code_info('',$sess_school_code,$sess_grade,$compile_class_code_name=true,$arry_conn_user);
                foreach($get_class_code_infos as $inx=>$get_class_code_info){
                    $rs_class_code  =trim($get_class_code_info['class_code']);
                    $rs_classroom   =trim($get_class_code_info['classroom']);
                    if($rs_class_code===$sess_class_code){
                        $new_classroom=$rs_classroom;
                    }else{
                        $new_classroom=$sess_classroom;
                    }
                }
            }else{
                return false;
            }

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            //初始化, 輸出內容
            $output="";

            //初始化, 身份參考陣列, 1(校長使用者) | 2(主任使用者) | 3(老師使用者)
            $arrys_identity=array(1,2,3);

            //初始化, 身份數量
            $identity_cno=0;

            //初始化, 班級數量
            $class_code_cno=0;

            //-------------------------------------------
            //身份數量統計
            //-------------------------------------------

                //身份數量統計
                foreach($_SESSION['tc'] as $responsibilities=>$arry_login_info){
                    $responsibilities=(int)$responsibilities;
                    if(in_array($responsibilities,$arrys_identity)){
                        $identity_cno++;
                    }
                }

            //-------------------------------------------
            //班級數量統計
            //-------------------------------------------

                $responsibilities_flag=0;

                //校長班級數量統計
                if(isset($_SESSION['tc'][1])){
                    if((isset($_SESSION['tc'][1]['arrys_class_code']))&&(!empty($_SESSION['tc'][1]['arrys_class_code']))){
                        $class_code_cno=count($_SESSION['tc'][1]['arrys_class_code']);
                    }else{
                        $class_code_cno=0;
                    }
                    $responsibilities_flag=1;
                }

                //主任班級數量統計
                if(isset($_SESSION['tc'][2])){
                    if((isset($_SESSION['tc'][2]['arrys_class_code']))&&(!empty($_SESSION['tc'][2]['arrys_class_code']))){
                        $class_code_cno=count($_SESSION['tc'][2]['arrys_class_code']);
                    }else{
                        $class_code_cno=0;
                    }
                    $responsibilities_flag=2;
                }

                //老師班級數量統計
                if(isset($_SESSION['tc'][3])){
                    if((isset($_SESSION['tc'][3]['arrys_class_code']))&&(!empty($_SESSION['tc'][3]['arrys_class_code']))){
                        $class_code_cno=count($_SESSION['tc'][3]['arrys_class_code']);
                    }else{
                        $class_code_cno=0;
                    }
                    $responsibilities_flag=3;
                }

        //-----------------------------------------------
        //設置
        //-----------------------------------------------
        //if($identity_cno>1){
        //    $output.="
        //                <!-- <a href='javascript:void(0);'>
        //                    <div class='fc_blue0 fsize_12' style='position:relative;margin-top:0px;display:none;' onclick='choose_identity();'>切換身份</div>
        //                </a> -->
        //    ";
        //}
        //if($class_code_cno>1){
        //    if(isset($_SESSION['tc']['t|dt'])){
        //        $output.="
        //                <!-- <a href='javascript:void(0);'>
        //                    <div class='fc_blue0 fsize_12' style='position:relative;margin-top:0px;' onclick='choose_class_code();'>切換班級</div>
        //                </a> -->
        //        ";
        //    }
        //
        //}

            if(($identity_cno<=1)&&($class_code_cno<=1)){
                $output="";
            }else{
                $output="";
                $output.="
                    <table id='fast_area' cellpadding='0' cellspacing='0' border='0' width='100%'/>
                        <tr>
                            <td align='center'>
                                <img src='{$path}../../img/icon/arrow_left.gif' width='4' height='7' border='0' alt='快速切換'/>
                            </td>
                        </tr>
                        <tr>
                            <td align='center'>
                                <!-- <span class='fc_red1 fsize_12'>{$sess_grade}年{$new_classroom}班</span> -->
                                <span class='fc_red1 fsize_12'>快速切換班級</span>
                            </td>
                        </tr>
                    </table>
                ";

                if($class_code_cno>1){
                    $output.="
                        <table id='fast_content' align='center' cellpadding='0' cellspacing='0' border='0' width='100%' height='55px'/>
                    ";
                    foreach($_SESSION['tc'][$responsibilities_flag]['arrys_class_code'] as $inx=>$arry_class_code){
                        $class_code     =trim($arry_class_code['class_code']);
                        $class_category =(int)$arry_class_code['class_category'];
                        $grade          =(int)$arry_class_code['grade'];
                        $classroom      =(int)$arry_class_code['classroom'];
                        $semester_code  =trim($arry_class_code['semester_code']);

                        //轉換班級名稱
                        $tmp_classroom='';
                        $get_class_code_infos=get_class_code_info('',$sess_school_code,$grade,$compile_class_code_name=true,$arry_conn_user);
                        foreach($get_class_code_infos as $inx=>$get_class_code_info){
                            $rs_class_code  =trim($get_class_code_info['class_code']);
                            $rs_classroom   =trim($get_class_code_info['classroom']);
                            if($rs_class_code===$class_code){
                                $tmp_classroom=$rs_classroom;
                            }else{
                                $tmp_classroom=$classroom;
                            }
                        }

                        $output.="
                            <tr>
                                <td align='center' valign='middle'>
                        ";
                                    if($tmp_classroom===$new_classroom){
                                        $output.='<span class="fc_red1 fsize_16" onclick="';
                                        $output.="void(0);";
                                        $output.='"';
                                        $output.=">✔ {$grade}年{$tmp_classroom}班</span>";
                                    }else{
                                        $output.='<span class="fc_blue1 fsize_16" style="cursor:pointer;" onclick="';
                                        $output.="sel_class_code({$rd},'{$class_code}',{$class_category},{$grade},{$classroom},'{$semester_code}');";
                                        $output.='" onmouseover="this.style.cursor';
                                        $output.="='pointer'";
                                        $output.='"';
                                        $output.=">{$grade}年{$tmp_classroom}班</span>";
                                    }
                        $output.="
                                </td>
                            </tr>
                        ";
                    }
                    $output.="
                        </table>
                    ";
                }else{
                    $output.="";
                }
            }

            //輸出
            echo $output;
    }
?>

