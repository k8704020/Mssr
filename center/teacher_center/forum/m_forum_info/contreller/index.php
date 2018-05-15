<?php

            //學生資訊
            $students = student($conn_user,$arry_conn_user,$class_code); 

            //學生發文篇數
            foreach($students as $k=>$v){
                $po_cno[] = po_cno($v['uid']);
            }
            
            //學生回文篇數
            foreach($students as $k=>$v){
                $repo_cno[] = repo_cno($v['uid'],$conn_mssr,$arry_conn_mssr);
            }
            
            //學生發文平均
            foreach($students as $k=>$v){
                if(avgpo_cno($v['uid']) !=0){
                    $avg         = avgpo_cno($v['uid'])/po_cno($v['uid']);
                    $avgpo_cno[] = round($avg,0);
                }else{
                     $avgpo_cno[] =0;
                }        
            }

            //學生回文平均
            foreach($students as $k=>$v){
                if(avgrepo_cno($v['uid']) !=0){
                    $avg         = avgrepo_cno($v['uid'],$conn_mssr,$arry_conn_mssr)/repo_cno($v['uid'],$conn_mssr,$arry_conn_mssr);
                    $avgrepo_cno[] = round($avg,0);

                }else{
                     $avgrepo_cno[] =0;
                }        
            }

            //學生小組發文回文
            foreach($students as $k=>$v){
                 $group_po_cno[]   = group_po_cno($v['uid'])+group_repo_cno($v['uid']);      
            }

             //學生書頁發文回文
            foreach($students as $k=>$v){
                 $book_po_cno[]   = book_po_cno($v['uid'])+book_repo_cno($v['uid']);      
            }

            //讚數
            foreach($students as $k=>$v){
                 $like_po_cno[]   = like_po_cno($v['uid'])+like_repo_cno($v['uid']);      
            }

            //請求率
            foreach($students as $k=>$v){
                if(request($v['uid'])!=0){
                     $request[]   = round(request_success($v['uid'])/request($v['uid'])*100,0);      
                }else{
                    $request[]    = 0;
                }
            }
    
            //喜歡的小組
            foreach($students as $k=>$v){
                 $like_group[]   = like_group($v['uid']);      
            }
            //喜歡的書
            foreach($students as $k=>$v){
                 $like_book[]   = like_book($v['uid']);      
            }

             //檢舉文章
            foreach($students as $k=>$v){
                 $report[]   =  report($v['uid']);      
            }

           






























?>