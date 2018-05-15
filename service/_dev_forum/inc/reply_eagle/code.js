//-------------------------------------------------------
//函式: reply_eagle()
//用途: 回文鷹架
//-------------------------------------------------------

    function reply_eagle(){
    //---------------------------------------------------
    //函式: reply_eagle()
    //用途: 回文鷹架
    //---------------------------------------------------
    //
    //---------------------------------------------------

        var $reply_article          =$('#reply_article');
        var eagle_val               =$.trim($reply_article.find('SELECT:not(#book_sid) :selected').text());   
        var eagle_code              =$.trim($reply_article.find('SELECT:not(#book_sid) :selected').val());   
        var arry_eagle_val          =eagle_val.split("……");
        var arry_my_borrow_length   =0;
        for(key1 in arry_my_borrow){arry_my_borrow_length++}

        var next_eagle_html='';

        next_eagle_html+=   '<div class="row reply_eagle" style="position:relative;margin-top:0px;">';
        next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
        next_eagle_html+=           '<form id="Form1" name="Form1" method="post" onsubmit="return false;">';

        next_eagle_html+=               '<div class="row chosen" style="position:relative;margin-bottom:15px;">';
        next_eagle_html+=                   '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
        
        if(arry_my_borrow_length>1){
            next_eagle_html+=                       '<select class="form-control" id="book_sid" name="book_sid">';
            next_eagle_html+=                           '<option value="" disabled="disabled" selected>請選擇一本書來發文......</option>';
            for(key1 in arry_my_borrow){
                next_eagle_html+=                       '<option value="'+trim(key1)+'">'+trim(arry_my_borrow[key1])+'</option>';
            }
            next_eagle_html+=                       '</select>';        
        }else{
            next_eagle_html+=                       '<select class="form-control" id="book_sid" name="book_sid" style="display:none;">';
            for(key1 in arry_my_borrow){
                next_eagle_html+=                       '<option value="'+trim(key1)+'" selected>'+trim(arry_my_borrow[key1])+'</option>';
            }
            next_eagle_html+=                       '</select>';            
        }

        next_eagle_html+=                   '</div>';
        next_eagle_html+=               '</div>';

        next_eagle_html+=               '<div class="form-group">';
        next_eagle_html+=                   '<textarea class="form-control" id="reply_content[]" name="reply_content[]" rows="6" placeholder="'+trim(eagle_val)+'">'+trim(eagle_val)+'</textarea>';
        next_eagle_html+=               '</div>';      

        //for(key1 in arry_eagle_val){
        //    if(trim(arry_eagle_val[key1])!==''){
        //        next_eagle_html+=       '<div class="form-group">';
        //        next_eagle_html+=           '<textarea class="form-control" id="reply_content[]" name="reply_content[]" rows="6" placeholder="'+trim(arry_eagle_val[key1])+'">'+trim(arry_eagle_val[key1])+'</textarea>';
        //        next_eagle_html+=       '</div>';                    
        //    }
        //}

        next_eagle_html+=               '<div class="checkbox">';
        next_eagle_html+=                   '<label>';
        next_eagle_html+=                       '<input type="checkbox" id="send_chk">我已閱讀過並同意遵守討論區規則';
        next_eagle_html+=                   '</label>';
        next_eagle_html+=                   ', <a target="_blank" href="forum.php?method=view_mssr_forum_article_reply_rule" style="color:#428bca;">按這裡檢視討論區規則</a>';
        next_eagle_html+=               '</div>';

        next_eagle_html+=               '<hr></hr>';
        next_eagle_html+=               '<button style='+'margin-bottom:20px;'+' type="button" class="btn btn-default pull-right" onclick="Btn_reply_article();void(0);">送出</button>';

        next_eagle_html+=               '<div class="form-group hidden">';
        next_eagle_html+=                   '<input type="hidden" class="form-control" name="article_id" value="'+get_article_id+'">';
        next_eagle_html+=                   '<input type="hidden" class="form-control" name="eagle_code" value="'+eagle_code+'">';
        next_eagle_html+=                   '<input type="hidden" class="form-control" name="reply_from" value="'+parseInt(get_from)+'">';
        next_eagle_html+=                   '<input type="hidden" class="form-control" name="group_id" value="'+parseInt(get_group_id)+'">';
        next_eagle_html+=                   '<input type="hidden" class="form-control" name="send_url" value="'+send_url+'">';
        next_eagle_html+=                   '<input type="hidden" class="form-control" name="method" value="reply_article">';
        next_eagle_html+=               '</div>';

        next_eagle_html+=           '</form>';
        next_eagle_html+=       '</div>';
        next_eagle_html+=   '</div>';

        //清除
        $('.reply_eagle').remove();

        //附加
        $reply_article.append(next_eagle_html);       
    }