//-------------------------------------------------------
//函式: quick_response_msg()
//用途: 快速回應通知選單
//-------------------------------------------------------

    function quick_edit_ok_request_rec_us_book(obj){
    //回覆好友推薦一本書籍給你

        var request_id=parseInt($(obj).attr('request_id'));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/edit.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                request_id  :encodeURI(trim(request_id                      )),
                method      :encodeURI(trim('edit_ok_request_rec_us_book'   )),
                send_url    :encodeURI(trim(send_url                        ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                $(obj).parent().find('button').remove();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function quick_request_article_rev(obj){
    //快速回覆文章邀請

        var request_id=parseInt($(obj).attr('request_id'));

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/edit.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                request_id  :encodeURI(trim(request_id              )),
                method      :encodeURI(trim('edit_request_article'  )),
                send_url    :encodeURI(trim(send_url                ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                $(obj).parent().find('button').remove();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function quick_request_create_group(obj,request_state){
    //快速回覆聯署建立小組

        var request_id   =parseInt($(obj).attr('request_id'));
        var request_state=parseInt(request_state);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/edit.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                request_id      :encodeURI(trim(request_id                 )),
                request_state   :encodeURI(trim(request_state              )),
                method          :encodeURI(trim('edit_request_create_group')),
                send_url        :encodeURI(trim(send_url                   ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                $(obj).parent().find('button').remove();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function quick_request_join_to_group(obj,request_state){
    //快速回覆申請加入小組

        var request_id   =parseInt($(obj).attr('request_id'));
        var request_state=parseInt(request_state);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/edit.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                request_id      :encodeURI(trim(request_id                  )),
                request_state   :encodeURI(trim(request_state               )),
                method          :encodeURI(trim('edit_request_join_to_group')),
                send_url        :encodeURI(trim(send_url                    ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                $(obj).parent().find('button').remove();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function quick_request_join_us_group(obj,request_state){
    //快速回覆邀請加入小組

        var request_id   =parseInt($(obj).attr('request_id'));
        var request_state=parseInt(request_state);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/edit.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                request_id      :encodeURI(trim(request_id                  )),
                request_state   :encodeURI(trim(request_state               )),
                method          :encodeURI(trim('edit_request_join_us_group')),
                send_url        :encodeURI(trim(send_url                    ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                $(obj).parent().find('button').remove();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function quick_request_friend(obj,friend_state){
    //快速回覆交友邀請

        var create_by   =parseInt($(obj).attr('create_by'));
        var user_id     =parseInt($(obj).attr('user_id'));
        var friend_id   =parseInt($(obj).attr('friend_id'));
        var friend_state=parseInt(friend_state);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"../controller/edit.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                create_by       :encodeURI(trim(create_by               )),
                user_id         :encodeURI(trim(user_id                 )),
                friend_id       :encodeURI(trim(friend_id               )),
                friend_state    :encodeURI(trim(friend_state            )),
                method          :encodeURI(trim('edit_request_friend'   )),
                send_url        :encodeURI(trim(send_url                ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                alert(respones);
                $(obj).parent().find('button').remove();
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }

    function quick_request_group_mission(obj,u_mission_state){
    //快速回覆推播任務邀請

        var u_task_id       =parseInt($(obj).attr('u_task_id'));
        var i_deliver_uid   =parseInt($(obj).attr('i_deliver_uid'));
        var request_to      =parseInt($(obj).attr('request_to'));
        var u_mission_state =parseInt(u_mission_state);

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :"Eric/initial_mission.php",
            type       :"POST",
            datatype   :"json",
            data       :{
                u_task_id       :encodeURI(trim(u_task_id       )),
                i_deliver_uid   :encodeURI(trim(i_deliver_uid   )),
                u_mission_state :encodeURI(trim(u_mission_state ))
            },

        //事件
            beforeSend  :function(){
            //傳送前處理
            },
            success     :function(respones){
            //成功處理
                //alert(respones);
                $(obj).parent().find('button').remove();
                location.href='user.php?user_id='+request_to+'&tab=10';
                return true;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                return false;
            },
            complete    :function(){
            //傳送後處理
            }
        });
    }