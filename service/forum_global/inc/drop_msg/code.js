//-------------------------------------------------------
//函式: drop_msg()
//用途: 通知下拉選單相關函式
//-------------------------------------------------------

    $(document).on("click","body",function(e){
        var drop_flag=$.trim($('#drop_obj').attr('drop_flag'));
        if(typeof(e.target)==='object' && typeof($('body')[0])==='object' && drop_flag==='down' && !$(e.target).hasClass('drop_obj')){
            close_drop_msg();
        }
    });

    function drop_msg(obj){
    //---------------------------------------------------
    //函式: drop_msg()
    //用途: 通知下拉選單
    //---------------------------------------------------
    //obj   選單物件
    //
    //---------------------------------------------------

        var obj=obj;
        var drop_flag=$.trim($(obj).attr('drop_flag'));
        
        if(drop_flag==='up'){
            //$('.drop_msg').dropdown('toggle');
            //$('.drop_msg').show(); 
            //$(obj).css("background-color","#e5e5e5");
            //$(obj).attr('drop_flag','down');
            open_drop_msg(obj);
        }else if(drop_flag==='down'){
            $('.drop_msg').removeClass('open');
            $('.drop_msg').hide();   
            $(obj).css("background-color","#e9e9e9");
            $(obj).attr('drop_flag','up');   
        }else{
            return false;                
        }

        $.ajax({
        //參數設置
            async      :true,
            cache      :false,
            global     :true,
            timeout    :50000,
            contentType:"application/x-www-form-urlencoded; charset=UTF-8",
            url        :'../pages/require/msg/code.php',
            type       :"POST",
            data       :{},

        //事件
            success     :function(respones){
            //成功處理
                $('.drop_msg').empty().append(respones);
            }
        });
    }

    function open_drop_msg(obj){
    //---------------------------------------------------
    //函式: open_drop_msg()
    //用途: 開啟通知下拉選單
    //---------------------------------------------------
    
        $('.drop_msg').dropdown('toggle');
        $('.drop_msg').show(); 
        $(obj).css("background-color","#e5e5e5");
        $(obj).attr('drop_flag','down');
    }

    function close_drop_msg(){
    //---------------------------------------------------
    //函式: close_drop_msg()
    //用途: 關閉通知下拉選單
    //---------------------------------------------------
    
        $('.drop_msg').removeClass('open');
        $('.drop_msg').hide();   
        $('#drop_obj').css("background-color","#e9e9e9");
        $('#drop_obj').attr('drop_flag','up');   

        //if(obj!==null){
        //    $('.drop_msg').removeClass('open');
        //    $('.drop_msg').hide();   
        //    $(obj).css("background-color","#e9e9e9");
        //    $(obj).attr('drop_flag','up');        
        //}else{
        //    $('.drop_msg').removeClass('open');
        //    $('.drop_msg').hide();   
        //    $('#drop_obj').css("background-color","#e9e9e9");
        //    $('#drop_obj').attr('drop_flag','up');              
        //}
    }
