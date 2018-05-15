//-------------------------------------------------------
//函式: reply_eagle()
//用途: 回文鷹架
//-------------------------------------------------------

    function reply_eagle(eagle_lv){
    //---------------------------------------------------
    //函式: reply_eagle()
    //用途: 回文鷹架
    //---------------------------------------------------
    //eagle_lv  選單階層   
    //---------------------------------------------------
    
        var eagle_lv=parseInt(eagle_lv);

        switch(eagle_lv){
            case 1:
                    var next_eagle_content=reply_eagle_content;

                    var next_eagle_html='<div class="row reply_eagle_lv_1" style="margin-top:15px;">';
                    next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                    next_eagle_html+=           '<select class="form-control reply_eagle_lv_1" onchange="reply_eagle(eagle_lv=2);void(0);">';
                    next_eagle_html+=               '<option disabled="disabled" selected>請選擇</option>';
                    for(key1 in next_eagle_content){
                        next_eagle_html+=           '<option>&nbsp;&nbsp;'+trim(key1)+'</option>';
                    }
                    next_eagle_html+=           '</select>';
                    next_eagle_html+=       '</div>';
                    next_eagle_html+=   '</div>';

                    //清除
                    $('div.reply_eagle_lv_1').remove();
                    $('div.reply_eagle_lv_2').remove();
                    $('div.reply_eagle_lv_3').remove();
                    $('div.reply_eagle_lv_4').remove();
                    $('div.reply_eagle_lv_5').remove();

                    //附加
                    $('div.div_textarea').before(next_eagle_html);
                break;

            case 2:
                    var reply_eagle_lv_1=$.trim($('.reply_eagle_lv_1 :selected').text());
                    var next_eagle_content=reply_eagle_content[reply_eagle_lv_1];

                    var next_eagle_html='<div class="row reply_eagle_lv_2" style="margin-top:15px;">';
                    next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                    next_eagle_html+=           '<select class="form-control reply_eagle_lv_2" onchange="reply_eagle(eagle_lv=3);void(0);">';
                    next_eagle_html+=               '<option disabled="disabled" selected>請選擇</option>';
                    for(key1 in next_eagle_content){
                        next_eagle_html+=           '<option>&nbsp;&nbsp;'+trim(key1)+'</option>';
                    }
                    next_eagle_html+=           '</select>';
                    next_eagle_html+=       '</div>';
                    next_eagle_html+=   '</div>';

                    //清除
                    $('div.reply_eagle_lv_2').remove();
                    $('div.reply_eagle_lv_3').remove();
                    $('div.reply_eagle_lv_4').remove();
                    $('div.reply_eagle_lv_5').remove();

                    //附加
                    $('div.div_textarea').before(next_eagle_html);
                break;

            case 3:
                    var reply_eagle_lv_1=$.trim($('.reply_eagle_lv_1 :selected').text());
                    var reply_eagle_lv_2=$.trim($('.reply_eagle_lv_2 :selected').text());
                    var next_eagle_content=reply_eagle_content[reply_eagle_lv_1][reply_eagle_lv_2];

                    var next_eagle_html='<div class="row reply_eagle_lv_3" style="margin-top:15px;">';
                    next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                    next_eagle_html+=           '<select class="form-control reply_eagle_lv_3" onchange="reply_eagle(eagle_lv=4);void(0);">';
                    next_eagle_html+=               '<option disabled="disabled" selected>請選擇</option>';
                    for(key1 in next_eagle_content){
                        next_eagle_html+=           '<option>&nbsp;&nbsp;'+trim(key1)+'</option>';
                    }
                    next_eagle_html+=           '</select>';
                    next_eagle_html+=       '</div>';
                    next_eagle_html+=   '</div>';

                    //清除
                    $('div.reply_eagle_lv_3').remove();
                    $('div.reply_eagle_lv_4').remove();
                    $('div.reply_eagle_lv_5').remove();

                    //附加
                    $('div.div_textarea').before(next_eagle_html);
                break;

            case 4:
                    var reply_eagle_lv_1  =$.trim($('.reply_eagle_lv_1 :selected').text());
                    var reply_eagle_lv_2  =$.trim($('.reply_eagle_lv_2 :selected').text());
                    var reply_eagle_lv_3  =$.trim($('.reply_eagle_lv_3 :selected').text());
                    var next_eagle_content=reply_eagle_content[reply_eagle_lv_1][reply_eagle_lv_2][reply_eagle_lv_3];
                    if($.isArray(next_eagle_content)){
                        var next_eagle_html='<div class="row reply_eagle_lv_4" style="margin-top:15px;">';
                        next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                        next_eagle_html+=           '<select class="form-control reply_eagle_lv_4" onchange="reply_eagle(eagle_lv=5);void(0);">';
                        next_eagle_html+=               '<option disabled="disabled" selected>請選擇</option>';
                        for(key1 in next_eagle_content){
                            next_eagle_html+=           '<option>&nbsp;&nbsp;'+trim(next_eagle_content[key1])+'</option>';
                        }
                        next_eagle_html+=           '</select>';
                        next_eagle_html+=       '</div>';
                        next_eagle_html+=   '</div>';

                        //清除
                        $('div.reply_eagle_lv_4').remove();

                        //附加
                        $('div.div_textarea').before(next_eagle_html);
                    }else{
                        var next_eagle_html='<div class="row reply_eagle_lv_4" style="margin-top:15px;">';
                        next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                        next_eagle_html+=           '<select class="form-control reply_eagle_lv_4" onchange="reply_eagle(eagle_lv=5);void(0);">';
                        next_eagle_html+=               '<option disabled="disabled" selected>請選擇</option>';
                        for(key1 in next_eagle_content){
                            next_eagle_html+=           '<option>&nbsp;&nbsp;'+trim(key1)+'</option>';
                        }
                        next_eagle_html+=           '</select>';
                        next_eagle_html+=       '</div>';
                        next_eagle_html+=   '</div>';

                        //清除
                        $('div.reply_eagle_lv_4').remove();
                        $('div.reply_eagle_lv_5').remove();

                        //附加
                        $('div.div_textarea').before(next_eagle_html);
                    }
                break;
            
            case 5:
                    var reply_eagle_lv_1  =$.trim($('.reply_eagle_lv_1 :selected').text());
                    var reply_eagle_lv_2  =$.trim($('.reply_eagle_lv_2 :selected').text());
                    var reply_eagle_lv_3  =$.trim($('.reply_eagle_lv_3 :selected').text());
                    var reply_eagle_lv_4  =$.trim($('.reply_eagle_lv_4 :selected').text());
                    if($.isArray(reply_eagle_content[reply_eagle_lv_1][reply_eagle_lv_2][reply_eagle_lv_3])){
                        var reply_eagle_lv_4_index=parseInt($('.reply_eagle_lv_4 :selected').index());    
                        var eagle_code=parseInt(reply_eagle_code[reply_eagle_lv_1][reply_eagle_lv_2][reply_eagle_lv_3][reply_eagle_lv_4_index-1]);

                        $('#eagle_code').val(eagle_code);

                        var oreply_content=document.getElementById('reply_content[]');
                        for(key1 in reply_eagle_lv_4){
                            if(trim(reply_eagle_lv_4[key1])!==''){                     
                                var new_article_content=reply_eagle_lv_4[key1].replace(/●/g,"");
                                $(oreply_content).val($.trim($(oreply_content).val())+$.trim(new_article_content));                       
                            }
                        }
                        oreply_content.focus();

                        //清除
                        $('div.reply_eagle_lv_1').remove();
                        $('div.reply_eagle_lv_2').remove();
                        $('div.reply_eagle_lv_3').remove();
                        $('div.reply_eagle_lv_4').remove();
                    }else{
                        var next_eagle_content=reply_eagle_content[reply_eagle_lv_1][reply_eagle_lv_2][reply_eagle_lv_3][reply_eagle_lv_4];
                        var next_eagle_html='<div class="row reply_eagle_lv_5" style="margin-top:15px;">';
                        next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                        next_eagle_html+=           '<select class="form-control reply_eagle_lv_5" onchange="reply_eagle(eagle_lv=6);void(0);">';
                        next_eagle_html+=               '<option disabled="disabled" selected>請選擇</option>';
                        for(key1 in next_eagle_content){
                            next_eagle_html+=           '<option>&nbsp;&nbsp;'+trim(next_eagle_content[key1])+'</option>';
                        }
                        next_eagle_html+=           '</select>';
                        next_eagle_html+=       '</div>';
                        next_eagle_html+=   '</div>';

                        //清除
                        $('div.reply_eagle_lv_5').remove();

                        //附加
                        $('div.div_textarea').before(next_eagle_html);
                    }
                break;

            case 6:
                    var reply_eagle_lv_1  =$.trim($('.reply_eagle_lv_1 :selected').text());
                    var reply_eagle_lv_2  =$.trim($('.reply_eagle_lv_2 :selected').text());
                    var reply_eagle_lv_3  =$.trim($('.reply_eagle_lv_3 :selected').text());
                    var reply_eagle_lv_4  =$.trim($('.reply_eagle_lv_4 :selected').text());
                    var reply_eagle_lv_5  =$.trim($('.reply_eagle_lv_5 :selected').text());

                    var reply_eagle_lv_5_index=parseInt($('.reply_eagle_lv_5 :selected').index());    
                    var eagle_code=parseInt(reply_eagle_code[reply_eagle_lv_1][reply_eagle_lv_2][reply_eagle_lv_3][reply_eagle_lv_4][reply_eagle_lv_5_index-1]);

                    $('#eagle_code').val(eagle_code);

                    var oreply_content=document.getElementById('reply_content[]');
                    for(key1 in reply_eagle_lv_5){
                        if(trim(reply_eagle_lv_5[key1])!==''){                     
                            var new_article_content=reply_eagle_lv_5[key1].replace(/●/g,"");
                            $(oreply_content).val($.trim($(oreply_content).val())+$.trim(new_article_content));                       
                        }
                    }
                    oreply_content.focus();

                    //清除
                    $('div.reply_eagle_lv_1').remove();
                    $('div.reply_eagle_lv_2').remove();
                    $('div.reply_eagle_lv_3').remove();
                    $('div.reply_eagle_lv_4').remove();
                    $('div.reply_eagle_lv_5').remove();
                break;

            default:
                    return false;
                break;
        }
    }