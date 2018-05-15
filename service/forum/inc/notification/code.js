//-------------------------------------------------------
//函式: notification()
//用途: 瀏覽器桌面提醒
//-------------------------------------------------------

    function notification(){
    //---------------------------------------------------
    //函式: notification()
    //用途: 瀏覽器桌面提醒
    //---------------------------------------------------
    //
    //---------------------------------------------------
        if(Notification && Notification.permission !== "granted"){
            Notification.requestPermission(function(status){
                if(Notification.permission !== status){
                    Notification.permission = status;
                }
            });
        }
        this.show_notification=function(notification_tag, notification_title, notification_icon, notification_content){
            var notification_options={
                tag : notification_tag,     //消息uid，可覆蓋相同uid的訊息
                dir : "ltr",                //文字方向
                lang: "utf-8",              //語系
                icon: notification_icon,    //圖標
                body: notification_content  //訊息內容
            };
            if(Notification && Notification.permission === "granted"){
                var n = new Notification(notification_title,notification_options);
                return n;
            }
        }
    }