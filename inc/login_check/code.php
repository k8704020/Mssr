<?php
//-------------------------------------------------------
//函式: login_check()
//用途: 登入檢核
//日期: 2013年8月12日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function login_check($types=array()){
    //---------------------------------------------------
    //函式: login_check()
    //用途: 登入檢核
    //---------------------------------------------------
    //$types    使用者類型,陣列
    //
    //本函式會檢核登入的使用者類型,如果不指定類型,只要符合
    //任何一種,便算是ok,並回傳布林值.
    //---------------------------------------------------

        //-----------------------------------------------
        //初始
        //-----------------------------------------------

            //SESSION
            @session_start();

        //-----------------------------------------------
        //檢核
        //-----------------------------------------------

            if(!isset($types)||!is_array($types)){
                $types=array();
            }

        //-----------------------------------------------
        //使用者類型
        //-----------------------------------------------
        //a           系統使用者      1           $_SESSION['a']['uid']
        //t           教師使用者      3           $_SESSION['t']['uid']
        //s           學生使用者      5           $_SESSION['s']['uid']
        //am          協會成員使用者  7           $_SESSION['am']['uid']
        //dt          主任成員使用者  13          $_SESSION['dt']['uid']

            //使用者類型
            $user_types =array(
                trim('a ')=>1,
                trim('t ')=>3,
                trim('s ')=>5,
                trim('am')=>7,
                trim('dt')=>13
            );

            //有無登入
            $is_login=false;

            if(empty($types)){
                foreach($user_types as $key=>$val){
                    if(isset($_SESSION[$key]['uid'])&&!empty($_SESSION[$key]['uid'])){
                        $is_login =true;
                        break;
                    }
                }
            }else{
                foreach($types as $key){
                    if(isset($_SESSION[$key]['uid'])&&!empty($_SESSION[$key]['uid'])){
                        $is_login=true;
                        break;
                    }
                }
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $is_login;
    }
?>


