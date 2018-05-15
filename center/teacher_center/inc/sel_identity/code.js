function sel_identity(rd,identity){
//---------------------------------------------------
//選擇身分
//---------------------------------------------------
//rd            層級指標,預設0,表示在目前目錄下
//identity      身分代號
//---------------------------------------------------
//回傳值
//---------------------------------------------------
//
//---------------------------------------------------

    //參數
    if(rd===0){
        rd='';
    }else{
        rd=str_repeat('../',rd);
    }

    //ajax設定
    var $_url           =rd+'user_info/sel_identity/sel_identityA.php';
    var $_type          ="GET";
    var $_datatype      ="json";

    //送出
    _ajax($_url,$_type,$_datatype,identity);

    function _ajax($_url,$_type,$_datatype,identity){
    //ajax設置
        $.ajax({
        //參數設置
            async      :true,      //瀏覽器在發送的時候，停止任何其他的呼叫。
            cache      :false,     //快取的回應。
            global     :true,      //是否使用全局 AJAX 事件。
            timeout    :10000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",

            url        :$_url,     //請求的頁面
            type       :$_type,    //GET or POST
            datatype   :$_datatype,
            data       :{
                identity:encodeURI(trim(identity))
            },

        //事件
            beforeSend  :function(){//傳送前處理
            },
            success     :function(respones){
            //成功處理

                respones=jQuery.parseJSON(respones);
                var msg=respones.msg;   //訊息

                alert(msg);

                //重整當前頁面
                location.reload();
                return false;
            },
            error       :function(xhr, ajaxoptions, thrownerror){
            //失敗處理
                if(ajaxoptions==='timeout'){
                    alert('身分更換失敗!');
                    //重整當前頁面
                    location.reload();
                    return false;
                }else{
                    alert('身分更換失敗!');
                    //重整當前頁面
                    location.reload();
                    return false;
                }
            },
            complete    :function(){//傳送後處理
            }
        });
    }
}