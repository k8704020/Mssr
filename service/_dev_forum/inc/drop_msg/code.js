//-------------------------------------------------------
//函式: drop_msg()
//用途: 通知下拉選單
//-------------------------------------------------------

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
            $('.drop_msg').dropdown('toggle');
            $('.drop_msg').show(); 
            $(obj).css("background-color","#e5e5e5");
            $(obj).attr('drop_flag','down')
        }else if(drop_flag==='down'){
            $('.drop_msg').removeClass('open');
            $('.drop_msg').hide();   
            $(obj).css("background-color","#e9e9e9");
            $(obj).attr('drop_flag','up')
        }else{
            return false;                
        }
    }