<?php
//-------------------------------------------------------
//函式: modal_bookstore_rec()
//用途: 書店推薦模態框
//日期: 2016年3月25日
//作者: mssr_team@cl_ncu
//-------------------------------------------------------

    function modal_bookstore_rec($rd=0){
    //---------------------------------------------------
    //函式: modal_bookstore_rec()
    //用途: 書店推薦模態框
    //---------------------------------------------------
    //$rd   層級指標,預設0,表示在目前目錄下
    //---------------------------------------------------

        //-----------------------------------------------
        //參數檢驗
        //-----------------------------------------------

            if(!isset($rd)||(int)$rd===0){
                $rd='';
            }else{
                $rd=str_repeat('../',$rd);
            }

        //-----------------------------------------------
        //設定
        //-----------------------------------------------

            global $arrys_sess_login_info;
            global $conn_mssr;
            global $arry_conn_mssr;

            $sess_user_id=(isset($arrys_sess_login_info[0]['uid']))?(int)$arrys_sess_login_info[0]['uid']:0;
            if($sess_user_id===0)die('您沒有權限進入，請洽詢明日星球團隊人員!');

            //$sql="
            //    SELECT `mssr`.`mssr_book_borrow_log`.`book_sid`
            //    FROM `mssr`.`mssr_book_borrow_log`
            //    WHERE 1=1
            //        AND `mssr`.`mssr_book_borrow_log`.`user_id`={$sess_user_id}
            //    GROUP BY `mssr`.`mssr_book_borrow_log`.`user_id`,
            //             `mssr`.`mssr_book_borrow_log`.`book_sid`
            //    ORDER BY `mssr`.`mssr_book_borrow_log`.`borrow_sdate` DESC
            //";
            //$book_results=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

            //-------------------------------------------
            //html  內容
            //-------------------------------------------

                $html ="";

                $html.="
                    <div class='modal fade bs-example-modal-lg' id='modal_bookstore_rec' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
                        <div class='modal-dialog modal-lg'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <div class='row'>
                                        <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>
                                            <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class='col-xs-12 col-sm-4 col-md-4 col-lg-4'>
                                            <h4 class='modal-title' style='color:#ec6c01;font-weight:bold;'>請選擇推薦類型...</h4>
                                        </div>
                                        <div class='col-xs-12 col-sm-5 col-md-5 col-lg-5'>
                                            <!-- <div class='input-group'>
                                                <input type='text' class='input-sm form-control' placeholder='Search' name='srch-term' id='srch-term'>
                                                <div class='input-group-btn'>
                                                    <button class='btn btn-sm btn-default' type='submit'><i class='glyphicon glyphicon-search'></i></button>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class='modal-body'>
                                    <div class='row'>
                ";
                                        $html.="
                                            <div class='col-xs-2 col-sm-2 col-md-2 col-lg-2'>
                                                <div style='margin:5px 5px;'>
                                                    <button type='button' class='btn btn-primary btn-md hidden-xs' onclick='show_bookstore_rec(1);'>畫圖</button>
                                                    <button type='button' class='btn btn-primary btn-xs visible-xs' onclick='show_bookstore_rec(1);'>畫圖</button>
                                                </div>
                                                <div style='margin:5px 5px;'>
                                                    <button type='button' class='btn btn-primary btn-md hidden-xs' onclick='show_bookstore_rec(2);'>文字</button>
                                                    <button type='button' class='btn btn-primary btn-xs visible-xs' onclick='show_bookstore_rec(2);'>文字</button>
                                                </div>
                                                <div style='margin:5px 5px;'>
                                                    <button type='button' class='btn btn-primary btn-md hidden-xs' onclick='show_bookstore_rec(3);'>錄音</button>
                                                    <button type='button' class='btn btn-primary btn-xs visible-xs' onclick='show_bookstore_rec(3);'>錄音</button>
                                                </div>
                                            </div>
                                            <div class='col-xs-10 col-sm-10 col-md-10 col-lg-10'>
                                                <div class='show_bookstore_rec_content row' style='max-height:300px;overflow-y:auto;'></div>
                                            </div>
                                        ";
                                        //foreach($book_results as $book_result):
                                        //    $rs_book_sid=trim($book_result['book_sid']);
                                        //    if($rs_book_sid!==''){
                                        //        $arry_book_infos=get_book_info($conn_mssr,$rs_book_sid,$array_filter=array('book_name'),$arry_conn_mssr);
                                        //        if(empty($arry_book_infos))continue;
                                        //        $rs_book_name=trim($arry_book_infos[0]['book_name']);
                                        //        if(mb_strlen($rs_book_name)>8){
                                        //            $rs_book_name=mb_substr($rs_book_name,0,8)."..";
                                        //        }
                                        //        if(trim($rs_book_name)==='')continue;
                                        //        $rs_book_img    ='../img/default/book.png';
                                        //        if(file_exists("../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg")){
                                        //            $rs_book_img="../../../info/book/{$rs_book_sid}/img/front/simg/1.jpg";
                                        //        }
                                        //        if(preg_match("/^mbu/i",$rs_book_sid)){
                                        //            $get_book_info=get_book_info($conn_mssr,trim($rs_book_sid),$array_filter=array('book_verified'),$arry_conn_mssr);
                                        //            if(!empty($get_book_info)){
                                        //                $rs_book_verified=(int)$get_book_info[0]['book_verified'];
                                        //                if($rs_book_verified===2)continue;
                                        //            }else{continue;}
                                        //        }
                                        //    }
                                            //$html.="
                                            //    <div class='col-xs-6 col-sm-3 col-md-3 col-lg-3'>
                                            //        <div class='thumbnail text-center'>
                                            //            <img width='80' height='80' style='weight:80px;height:80px;' src='{$rs_book_img}' alt='{$rs_book_name}'>
                                            //            <div class='caption text-center'>
                                            //                {$rs_book_name}
                                            //                <div>
                                            //                    <button type='button' class='btn btn-primary btn-xs'>畫圖</button>
                                            //                    <button type='button' class='btn btn-primary btn-xs'>文字</button>
                                            //                    <button type='button' class='btn btn-primary btn-xs'>錄音</button>
                                            //                </div>
                                            //            </div>
                                            //        </div>
                                            //    </div>
                                            //";
                                        //endforeach;
                $html.="
                                    </div>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-default' data-dismiss='modal'>關閉</button>
                                </div>
                            </div>
                        </div>
                    </div>
                ";

        //-----------------------------------------------
        //處理
        //-----------------------------------------------

            if(1==2){ //除錯用
                echo '<pre>';
                print_r($html);
                echo '</pre>';
            }

        //-----------------------------------------------
        //回傳
        //-----------------------------------------------

            return $html;
    }
?>