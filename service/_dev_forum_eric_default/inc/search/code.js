//-------------------------------------------------------
//函式: search()
//用途: 搜尋
//-------------------------------------------------------

    function search(){
    //---------------------------------------------------
    //函式: search()
    //用途: 搜尋
    //---------------------------------------------------
    //
    //---------------------------------------------------

        var nl='\r\n';
        var $search_value=$.trim($('.search_value').eq(1).val());
        var $search_type =$.trim($('.search_type').eq(1).val());
        if($search_value==='')$search_value=$('.search_value')[0].value;
        if($search_type==='')$search_type=$('.search_type')[0].value;
        var arry_err     =[];
        var url          ='query.php?search_value='+encodeURI($search_value)+'&search_type='+encodeURI($search_type);

        if($search_value===''){
            arry_err.push('請輸入搜尋條件');
        }

        if(arry_err.length!=0){
            alert(arry_err.join(nl));
            return false;
        }else{
            location.href=url;
            return true;
        }
    }

    $(document).on("keypress",".search_value",function(e){
    //---------------------------------------------------
    //函式: search()
    //用途: 搜尋
    //---------------------------------------------------
    //
    //---------------------------------------------------

        if(e.which==13){
            var nl='\r\n';
            var $search_value=$.trim($(this).val());
            var $search_type =$.trim($('.search_type').eq(1).val());
            if($search_type==='')$search_type=$('.search_type')[0].value;
            var arry_err     =[];
            var url          ='query.php?search_value='+encodeURI($search_value)+'&search_type='+encodeURI($search_type);

            if($search_value===''){
                arry_err.push('請輸入搜尋條件');
            }

            if(arry_err.length!=0){
                alert(arry_err.join(nl));
                return false;
            }else{
                location.href=url;
                return true;
            }
        }
    });