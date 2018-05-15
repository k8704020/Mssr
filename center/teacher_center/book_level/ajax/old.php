 $sql="
                SELECT user_id,
                       book_sid,
                       book_isbn_10,
                       book_isbn_13,
                       book_library_code,
                       book_language,
                       bopomofo, 
                       major_topic,
                       sub_topic,
                       minor_topic,
                       tag1,
                       tag2,
                       tag3,
                       tag4,
                       tag5,
                       pages,
                       words,
                       hard_level,
                       lexile_level
                              
                FROM `mssr_idc_reading_log_spreadsheet`             
               
                order by book_sid desc
                        
        ";


        $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);

    
        if(!empty($result)){


                    
                foreach($result as $key=>$arry_result){


                        $array_output[$key]['user_id']        =trim($arry_result['user_id']);
                        $array_output[$key]['book_sid']       =trim($arry_result['book_sid']);

                        if(!in_array($array_output[$key]['book_sid'], $array_book_sid)){
                                $arr=array_push($array_book_sid, $array_output[$key]['book_sid']);
                        }

                        $array_output[$key]['book_isbn_13']        =trim($arry_result['book_isbn_13']);
                        $array_output[$key]['book_isbn_10']        =trim($arry_result['book_isbn_10']);
                        $array_output[$key]['book_library_code']   =trim($arry_result['book_library_code']);
                        $array_output[$key]['tag1']   =trim($arry_result['tag1']);
                        $array_output[$key]['tag2']   =trim($arry_result['tag2']);
                        $array_output[$key]['tag3']   =trim($arry_result['tag3']);
                        $array_output[$key]['tag4']   =trim($arry_result['tag4']);
                        $array_output[$key]['tag5']   =trim($arry_result['tag5']);
                        $array_output[$key]['pages']   =trim($arry_result['pages']);
                        $array_output[$key]['lexile_level']   =trim($arry_result['lexile_level']);
                        
                        //======================
                        //尋找誰寫出這筆資料 
                        //======================
                 
                        $user_sql="

                            SELECT name
                            FROM  `member`  
                            WHERE  uid='{$array_output[$key]['user_id']}'   
                        " ; 
                        $user_result=db_result($conn_type='pdo',$conn_user,$user_sql,array(),$arry_conn_user);
                        if(!empty($user_result)){

                           $array_output[$key]['name']  =trim($user_result[0]['name']);
                                               
                        }

                        //======================
                        //尋找貼紙編號及貼紙顏色
                        //======================

                        $sticker_sql="
                                SELECT `sticker_color`,`sticker_number` 
                                FROM `mssr_idc_book_sticker_level_info`
                                WHERE book_sid='{$array_output[$key]['book_sid']}'

                        ";

                        $sticker_result=db_result($conn_type='pdo',$conn_mssr,$sticker_sql,array(),$arry_conn_mssr);
                        
                        if(!empty($sticker_result)){

                            foreach ($sticker_result as $index => $value) {

                                $array_output[$key]['sticker_number']=trim($value['sticker_number']);

                                $sticker_color_sql="
                                    SELECT color
                                    FROM  `mssr_idc_book_sticker_color`
                                    WHERE  color_id='{$value['sticker_color']}'   
                                " ; 

                                $sticker_color_result=db_result($conn_type='pdo',$conn_mssr,$sticker_color_sql,array(),$arry_conn_mssr);
                                if(!empty($sticker_color_result)){

                                        $array_output[$key]['sticker_color'] =trim($sticker_color_result[0]['color']);
        
                                }
                            }

                        }else{
                            $array_output[$key]['sticker_color']   ="";
                            $array_output[$key]['sticker_number']  ="";
                        }   

                        //======================
                        //是否有注音
                        //======================
                        $array_output[$key]['bopomofo']=$topic_options[0]['topic3_options'][$arry_result['bopomofo']];
                        //======================
                        //書本的語言
                        //======================
                        $array_output[$key]['book_language']=$topic_options[0]['topic2_options'][$arry_result['book_language']];
                        //======================
                        //書本的大主題
                        //======================
                        if($arry_result['major_topic']){
                            $major_topic=explode(",",$arry_result['major_topic']);
                            
                            foreach ($major_topic as $index_key => $array_major) {

                                $major_topic[$index_key]=$topic_options[0]['topic4_options'][$array_major];
                                $array_output[$key]['major_topic']=$major_topic;

                            }
                        }
                        //======================
                        //書本的中主題
                        //======================
                        if($arry_result['sub_topic']){
                            $sub_topic=explode(",",$arry_result['sub_topic']);
                            foreach ($sub_topic as $index_key => $array_sub) {

                                $sub_topic[$index_key]=$topic_options[0]['topic5_options'][$array_sub];
                                $array_output[$key]['sub_topic']=$sub_topic;

                            }
                        }
                        //======================
                        //書本的小主題
                        //======================
                        // print_r($arry_result['minor_topic']);
                        if($arry_result['minor_topic']){
                                $minor_topic=explode(",",$arry_result['minor_topic']);
                                foreach ($minor_topic as $index_key => $array_minor) {

                                    $minor_topic[$index_key]=$topic_options[0]['topic6_options'][$array_minor];
                                    $array_output[$key]['minor_topic']=$minor_topic;

                                }
                         }

                        //======================
                        //單人等級
                        //======================
                        if($arry_result['hard_level']){
                            $hard_level=explode(",",$arry_result['hard_level']);
                            foreach ($hard_level as $index_key => $array_hard) {

                                $hard_level[$index_key]=$topic_options[0]['topic14_options'][$array_hard];
                                $array_output[$key]['hard_level']=$hard_level;

                            }
                        }
                        //======================
                        //單人等級
                        //======================

                        if($arry_result['hard_level']){
                            $hard_level=explode(",",$arry_result['hard_level']);
                            foreach ($hard_level as $index_key => $array_hard) {

                                $hard_level[$index_key]=$topic_options[0]['topic14_options'][$array_hard];
                                $array_output[$key]['hard_level']=$hard_level;

                            }
                        }

                        //======================
                        //平均等級
                        //======================

                        if($array_output[$key]['book_sid']){

                            $book_hard_sql="
                                             SELECT `avg_level` 
                                             FROM `mssr_idc_book_sticker_level_info` 
                                             WHERE  `book_sid` ='{$array_output[$key]['book_sid']}'           
                                            
                            ";
                            $book_hard_result=db_result($conn_type='pdo',$conn_mssr,$book_hard_sql,array(),$arry_conn_mssr);
                            
                            if(!empty($book_hard_result)){
                                 $array_output[$key]['avg_level']=$book_hard_result[0]['avg_level'];
                            }


                        }







                }

                //======================
                //每本書登記的等級平均
                //======================
                foreach ($array_book_sid as $key => $about_book_sid) {
            
                            $book_hard_sql="
                                             SELECT hard_level                                  
                                             FROM `mssr_idc_reading_log_spreadsheet` 
                                             WHERE book_sid='{$about_book_sid}'           
                                             ORDER BY book_sid desc
                                            
                                        ";
                            $book_hard_result=db_result($conn_type='pdo',$conn_mssr,$book_hard_sql,array(),$arry_conn_mssr);

                            $people_level=0;

                            if(count($book_hard_result)>1){

                                    foreach ($book_hard_result as $index_key => $array_book_hard) {
                              
                                            $hard_level[$index_key]=explode(",",$array_book_hard['hard_level']);
                                            if($hard_level[$index_key][0]&&$hard_level[$index_key][1]){
                                                $level=(int)$hard_level[$index_key][0]+(int)$hard_level[$index_key][1];
                                                $one_person=$level/2;

                                              }

                                            $people_level+=$one_person;
                                    }

                                    $avg_level=$people_level/2;
                            
                            }else{

                                foreach ($book_hard_result as $index_key => $array_book_hard) {
                              
                                            $hard_level[$index_key]=explode(",",$array_book_hard['hard_level']);
                                            if($hard_level[$index_key][0]&&$hard_level[$index_key][1]){
                                                $level=(int)$hard_level[$index_key][0]+(int)$hard_level[$index_key][1];
                                                $one_person=$level/2;
                                              }
                                            $avg_level=$one_person;

                                }


                            }

                            $sql="
                                
                                    SELECT * 
                                    FROM `mssr_idc_book_sticker_level_info` 
                                    WHERE book_sid='{$about_book_sid}'
                            ";

                            $result=db_result($conn_type='pdo',$conn_mssr,$sql,array(),$arry_conn_mssr);
                         
                            if(!empty($result)){

                                $book_sticker_sql="

                                             UPDATE `mssr_idc_book_sticker_level_info` 
                                             SET `avg_level`='{$avg_level}'
                                             WHERE book_sid='{$about_book_sid}'
                                                      
                                ";
                                $book_sticker_result=db_result($conn_type='pdo',$conn_mssr,$book_sticker_sql,array(),$arry_conn_mssr);


                            }else{

                                $book_sticker_sql="
                                             INSERT INTO `mssr_idc_book_sticker_level_info`(
                                             `edit_by`, 
                                             `user_id`, 
                                             `book_sid`, 
                                             `sticker_id`, 
                                             `sticker_color`, 
                                             `sticker_number`, 
                                             `avg_level`, 
                                             `keyin_cdate`, 
                                             `keyin_mdate`
                                             ) 
                                             VALUES 
                                             ('0',
                                             '0',
                                             '{$about_book_sid}',
                                             '0',
                                             '0',
                                             '0',
                                             '{$avg_level}',
                                             NOW(),
                                             NOW()
                                             );

                                            
                                ";
                                $book_sticker_result=db_result($conn_type='pdo',$conn_mssr,$book_sticker_sql,array(),$arry_conn_mssr);


                            }

}}