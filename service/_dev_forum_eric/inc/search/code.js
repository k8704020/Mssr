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
        var $search_value=$.trim($('.search_value').val());
        var $search_type =$.trim($('.search_type').val());
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

    $('.search_value').keypress(function(e){
    //---------------------------------------------------
    //函式: search()
    //用途: 搜尋
    //---------------------------------------------------
    //
    //---------------------------------------------------

        if(e.which==13){
            var nl='\r\n';
            var $search_value=$.trim($(this).val());
            var $search_type =$.trim($('.search_type').val());
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