//-------------------------------------------------------
//新增聊書動作log
//-------------------------------------------------------

    function add_action_forum_log(process_url,action_code,action_from,user_id_1,user_id_2,book_sid_1,book_sid_2,forum_id_1,forum_id_2,article_id,reply_id,go_url){
    //---------------------------------------------------
    //函式: add_action_forum_log()
    //用途: 新增聊書動作log
    //---------------------------------------------------
    //process_url       處理頁面
    //action_code       動作代號
    //action_from       動作來源使用者主索引
    //user_id_1         動作指向使用者主索引1
    //user_id_2         動作指向使用者主索引2
    //book_sid_1        書籍識別碼1
    //book_sid_2        書籍識別碼2
    //forum_id_1        社團主索引1
    //forum_id_2        社團主索引2
    //article_id        文章主索引
    //reply_id          回覆主索引
    //go_url            跳轉頁面
    //---------------------------------------------------

        //參數檢驗
        if((process_url===undefined)||(process_url==='')){
            alert('ADD_ACTION_FORUM_LOG:NO PROCESS_URL');
            return false;
        }

        if((action_code===undefined)||(action_code==='')){
            alert('ADD_ACTION_FORUM_LOG:NO ACTION_CODE');
            return false;
        }

        if((action_from===undefined)||(action_from==='')){
            alert('ADD_ACTION_FORUM_LOG:NO action_from');
            return false;
        }else{
            action_from=parseInt(action_from);
            if(isNaN(action_from)){
                alert('ADD_ACTION_FORUM_LOG:ACTION_FROM IS INVALID');
                return false;                
            }
        }

        if(go_url===undefined){
            alert('ADD_ACTION_FORUM_LOG:NO GO_URL');
            return false;
        }


        //ajax設置

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
                contentType:"application/x-www-form-urlencoded; charset=UTF-8",
                url        :$url,
                type       :$type,
                datatype   :$datatype,

                data       :{
                    action_code :encodeURI(action_code          ) ,
                    action_from :encodeURI(parseInt(action_from )),

                    user_id_1   :encodeURI(parseInt(user_id_1   )),
                    user_id_2   :encodeURI(parseInt(user_id_2   )),
                    book_sid_1  :encodeURI(book_sid_1           ) ,
                    book_sid_2  :encodeURI(book_sid_2           ) ,
                    forum_id_1  :encodeURI(parseInt(forum_id_1  )),
                    forum_id_2  :encodeURI(parseInt(forum_id_2  )),

                    article_id  :encodeURI(parseInt(article_id  )),
                    reply_id    :encodeURI(parseInt(reply_id    ))
                },

            //事件
                beforeSend  :function(){
                //傳送前處理
                },
                success     :function(respones){
                //成功處理
                //alert(respones);
                //return false;
                    //var respones        =jQuery.parseJSON(respones);
                    if(go_url!==''){
                        location.href=go_url;
                    } 
                    return false;
                },
                error       :function(xhr, ajaxoptions, thrownerror){
                //失敗處理
                    if(ajaxoptions==='timeout'){
                        alert('ADD_ACTION_FORUM_LOG:TIMEOUT');
                        return false;
                    }else{
                        alert('ADD_ACTION_FORUM_LOG:TIMEOUT');
                        return false;
                    }
                },
                complete    :function(){
                //傳送後處理
                }
            });
    }