//-------------------------------------------------------
//新增分店動作log
//-------------------------------------------------------

    function add_action_branch_log(
        process_url,
        action_code,
        user_id,
        visit_to,
        book_sid,
        friend_ident,
        branch_id,
        branch_state,
        branch_rank,
        branch_lv,   
        task_ident,  
        task_state,  
        cat_id,    
        task_sdate,  
        go_url
    ){
    //---------------------------------------------------
    //函式: add_action_branch_log()
    //用途: 新增分店動作log
    //---------------------------------------------------
    //process_url       處理頁面
    //action_code       動作代號
    //user_id           動作執行者主索引
    //visit_to          被拜訪人主索引
    //book_sid          書籍識別碼
    //friend_ident      是否為朋友(1=是,0=不是)
    //branch_id         分店主索引
    //branch_state      分店狀態(啟用,停用)
    //branch_rank       分店等級
    //branch_lv         分店圈數
    //task_ident        分店有無可接任務(1=有,0=無)
    //task_state        分店有無進行中的任務(1=有,0=無)
    //cat_id            分店相關類別id
    //task_sdate        任務起始時間
    //go_url            動作完畢, 跳轉頁面
    //---------------------------------------------------
//alert(branch_id);
//return true;
        //參數檢驗
        if((process_url===undefined)||(process_url==='')){
            alert('ADD_ACTION_BRANCH_LOG:NO PROCESS_URL');
            return false;
        }

        if((action_code===undefined)||(action_code==='')){
            alert('ADD_ACTION_BRANCH_LOG:NO ACTION_CODE');
            return false;
        }

        if((user_id===undefined)||(user_id==='')){
            alert('ADD_ACTION_BRANCH_LOG:NO USER_ID');
            return false;
        }else{
            user_id=parseInt(user_id);
            if(isNaN(user_id)){
                alert('ADD_ACTION_BRANCH_LOG:USER_ID IS INVALID');
                return false;                
            }
        }

        if(go_url===undefined){
            alert('ADD_ACTION_BRANCH_LOG:NO GO_URL');
            return false;
        }


        //ajax設置
        //process_url       處理頁面
        //action_code       動作代號
        //user_id           動作執行者主索引
        //visit_to          被拜訪人主索引
        //book_sid          書籍識別碼
        //friend_ident      是否為朋友(1=是,0=不是)
        //branch_id         分店主索引
        //branch_state      分店狀態(啟用,停用)
        //branch_rank       分店等級
        //branch_lv         分店圈數
        //task_ident        分店有無可接任務(1=有,0=無)
        //task_state        分店有無進行中的任務(1=有,0=無)
        //cat_id            分店相關類別id
        //task_sdate        任務起始時間
        //go_url            動作完畢, 跳轉頁面

            //參數
            var $url        =process_url;
            var $type       ="POST";
            var $datatype   ="json";
            
            //啟用
            $.ajax({
            //
                async      :true,
                cache      :false,
                global     :true,
                timeout    :10000,
                contentType:"application/x-www-form-urlencoded;charset=UTF-8",
                url        :$url,
                type       :$type,
                datatype   :$datatype,

                data       :{
                    action_code :encodeURI(action_code          ) ,
                    user_id     :encodeURI(parseInt(user_id     )),
                    visit_to    :encodeURI(parseInt(visit_to    )),
                    book_sid    :encodeURI(book_sid             ) ,
                    friend_ident:encodeURI(parseInt(friend_ident)),
                    branch_id   :encodeURI(parseInt(branch_id   )),
                    branch_state:(branch_state                  ) ,
                    branch_rank :encodeURI(parseInt(branch_rank )),
                    branch_lv   :encodeURI(parseInt(branch_lv   )),
                    task_ident  :encodeURI(parseInt(task_ident  )),
                    task_state  :encodeURI(parseInt(task_state  )),
                    cat_id      :encodeURI(parseInt(cat_id      )),
                    task_sdate  :(task_sdate                    ) 
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理
                //alert(respones);
                //return true;
                    //var respones        =jQuery.parseJSON(respones);
                    if(go_url!==''){
                        location.href=go_url;
                    } 
                    return true;
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){
                        console.log('ADD_ACTION_BRANCH_LOG:TIMEOUT');
                        return false;
                    }else{
                        console.log('ADD_ACTION_BRANCH_LOG:TIMEOUT');
                        return false;
                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });
    }