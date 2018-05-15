function sel_class_code(rd,class_code,class_category,grade,classroom,semester_code){
//---------------------------------------------------
//選擇班級
//---------------------------------------------------
//rd                層級指標,預設0,表示在目前目錄下
//class_code        班級代號
//class_category    班級類別
//grade             年級
//classroom         班級
//semester_code     學期代號
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
    var $_url           =rd+'user_info/sel_class_code/sel_class_codeA.php';
    var $_type          ="GET";
    var $_datatype      ="json";

    //送出
    _ajax($_url,$_type,$_datatype,class_code,class_category,grade,classroom,semester_code);

    function _ajax($_url,$_type,$_datatype,class_code,class_category,grade,classroom,semester_code){
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
                class_code:encodeURI(trim(class_code)),
                class_category:encodeURI(trim(class_category)),
                grade:encodeURI(trim(grade)),
                classroom:encodeURI(trim(classroom)),
                semester_code:encodeURI(trim(semester_code))
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
                    alert('班級更換失敗!');
                    //重整當前頁面
                    location.reload();
                    return false;
                }else{
                    alert('班級更換失敗!');
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