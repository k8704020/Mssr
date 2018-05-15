<?php

            //學生資訊
            $students = student($conn_user,$arry_conn_user,$class_code);

            //學生小組發文篇數
            foreach($students as $k=>$v){
                $forum_po_cno[] = forum_po_cno($v['uid']);
            }

            //print_r($forum_po_cno);

            //學生回文篇數
            foreach($students as $k=>$v){
                $forum_repo_cno[] = forum_repo_cno($v['uid']);
            }


            //學生書籍發文篇數
            foreach($students as $k=>$v){
                $book_po_cno[] = book_po_cno($v['uid']);
            }

            // //print_r($forum_po_cno);

            //學生書籍回文篇數
            foreach($students as $k=>$v){
                $book_repo_cno[] = book_repo_cno($v['uid']);
            }


            //學生發文平均
            foreach($students as $k=>$v){
                if(avg_po_cno($v['uid']) !=0){
                    $sum = book_po_cno($v['uid'])+forum_po_cno($v['uid']);
                    $avg          = avg_po_cno($v['uid'])/$sum*$sum;
                    $avg_po_cno[] = round($avg,0);
                }else{
                    $avg_po_cno[] =0;
                }
            }

            //學生回文平均
            foreach($students as $k=>$v){
                if(avg_repo_cno($v['uid']) !=0){
                    $sum            = forum_repo_cno($v['uid'])+book_repo_cno($v['uid']);
                    $avg            = avg_repo_cno($v['uid'])/$sum*$sum;
                    $avg_repo_cno[] = round($avg,0);

                }else{
                     $avg_repo_cno[] =0;
                }
            }


            //評價率
            foreach($students as $k=>$v){
                 $report[]         = report_po_cno($v['uid'])+report_repo_cno($v['uid']);
                 $like[]           = like_po_cno($v['uid'])+like_repo_cno($v['uid']);
            }

            //請求率
            foreach($students as $k=>$v){
                if(request($v['uid'])!=0){
                     $request_success[]   = round(request_success($v['uid'])/request($v['uid'])*100,0);
                }else{
                     $request_success[]    = 0;
                }
            }

            foreach($students as $k=>$v){
                if(request($v['uid'])!=0){
                     $request[]   = request($v['uid']);
                }else{
                     $request[]    = 0;
                }
            }

            foreach($students as $k=>$v){
                if(request($v['uid'])!=0){
                    //echo "<Pre>";
                    //print_r(accept_success($v['uid']));
                    //echo "</Pre>";
                    //echo "<Pre>";
                    //print_r(accept($v['uid']));
                    //echo "</Pre>";
                    //die();
                    if((int)accept($v['uid'])===0){
                        $accept_success[]    = 0;
                    }else{
                        $accept_success[]   = round((int)accept_success($v['uid'])/(int)accept($v['uid'])*100,0);
                    }
                }else{
                    $accept_success[]    = 0;
                }
            }

            foreach($students as $k=>$v){
                if(accept($v['uid'])!=0){
                     $accept[]    = (int)accept($v['uid']);
                }else{
                     $accept[]    = 0;
                }
            }



           //積極度
            foreach($students as $k=>$v){
                 $Actively[] =Actively($v['uid']);
            }

            //常看
            foreach($students as $k=>$v){
                 $like_look[] =like_look($v['uid']);
            }




            //  //學生書頁發文回文
            // foreach($students as $k=>$v){
            //      $book_po_cno[]   = book_po_cno($v['uid'])+book_repo_cno($v['uid']);
            // }

            // //讚數
            // foreach($students as $k=>$v){
            //      $like_po_cno[]   = like_po_cno($v['uid'])+like_repo_cno($v['uid']);
            // }

            // //請求率
            // foreach($students as $k=>$v){
            //     if(request($v['uid'])!=0){
            //          $request[]   = round(request_success($v['uid'])/request($v['uid'])*100,0);
            //     }else{
            //         $request[]    = 0;
            //     }
            // }

            // //喜歡的小組
            // foreach($students as $k=>$v){
            //      $like_group[]   = like_group($v['uid']);
            // }
            // //喜歡的書
            // foreach($students as $k=>$v){
            //      $like_book[]   = like_book($v['uid']);
            // }

             //檢舉文章
            // foreach($students as $k=>$v){
            //      $report[]   =  report($v['uid']);
            // }
































?>