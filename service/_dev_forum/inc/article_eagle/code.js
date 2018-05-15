//-------------------------------------------------------
//函式: article_eagle()
//用途: 發文鷹架
//-------------------------------------------------------

    function article_eagle(eagle_lv){
    //---------------------------------------------------
    //函式: article_eagle()
    //用途: 發文鷹架
    //---------------------------------------------------
    //eagle_lv  選單階層   
    //---------------------------------------------------

        var eagle_lv=parseInt(eagle_lv);
        var $add_article=$('#add_article');

        switch(eagle_lv){
            case 1:
                var eagle_lv1_val       =$.trim($('.eagle_lv_1 :selected').text());
                var next_eagle_content  =article_eagle_content[eagle_lv1_val];

                var next_eagle_html='<div class="row eagle_lv_2 select_eagle_lv_2" style="margin-top:10px;">';
                next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                next_eagle_html+=           '<select class="form-control eagle_lv_2" onchange="article_eagle(eagle_lv=2);void(0);">';
                next_eagle_html+=               '<option disabled="disabled" selected>你想要做甚麼?</option>';
                for(key1 in next_eagle_content){
                    next_eagle_html+=           '<option>'+trim(key1)+'</option>';
                }
                next_eagle_html+=           '</select>';
                next_eagle_html+=       '</div>';
                next_eagle_html+=   '</div>';

                //清除
                $('div.eagle_lv_2').remove();
                $('div.eagle_lv_3').remove();
                $('div.eagle_lv_4').remove();
                //$('div.eagle_lv_5').remove();
                $('div.chosen').remove();

                //附加
                $('div.eagle_lv_1').after(next_eagle_html);
            break;

            case 2:
                var eagle_lv1_val       =$.trim($('.eagle_lv_1 :selected').text());
                var eagle_lv2_val       =$.trim($('.eagle_lv_2 :selected').text());
                var next_eagle_content  =article_eagle_content[eagle_lv1_val][eagle_lv2_val];

                var next_eagle_html='<div class="row eagle_lv_3 select_eagle_lv_3" style="margin-top:10px;">';
                next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                next_eagle_html+=           '<select class="form-control eagle_lv_3" onchange="article_eagle(eagle_lv=3);void(0);">';
                next_eagle_html+=               '<option disabled="disabled" selected>你可以參考下列問題</option>';
                for(key1 in next_eagle_content){
                    next_eagle_html+=           '<option>'+trim(key1)+'</option>';
                }
                next_eagle_html+=           '</select>';
                next_eagle_html+=       '</div>';
                next_eagle_html+=   '</div>';

                //清除
                $('div.eagle_lv_3').remove();
                $('div.eagle_lv_4').remove();
                //$('div.eagle_lv_5').remove();
                $('div.chosen').remove();

                //附加
                $('div.eagle_lv_2').after(next_eagle_html);
            break;

            case 3:
                var eagle_lv1_val       =$.trim($('.eagle_lv_1 :selected').text());
                var eagle_lv2_val       =$.trim($('.eagle_lv_2 :selected').text());
                var eagle_lv3_val       =$.trim($('.eagle_lv_3 :selected').text());
                var next_eagle_content  =article_eagle_content[eagle_lv1_val][eagle_lv2_val][eagle_lv3_val];

                var next_eagle_html='<div class="row eagle_lv_4 select_eagle_lv_4" style="margin-top:10px;">';
                next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                next_eagle_html+=           '<select class="form-control eagle_lv_4" onchange="article_eagle(eagle_lv=4);void(0);">';

                if(eagle_lv2_val==='我想要問'){
                    next_eagle_html+=               '<option disabled="disabled" selected>你也許可以這樣問</option>';
                }else{
                    next_eagle_html+=               '<option disabled="disabled" selected>你也許可以這樣說</option>';
                }

                for(key1 in next_eagle_content){
                    next_eagle_html+=           '<option>'+trim(next_eagle_content[key1])+'</option>';
                }
                next_eagle_html+=           '</select>';
                next_eagle_html+=       '</div>';
                next_eagle_html+=   '</div>';

                //清除
                $('div.eagle_lv_4').remove();
                //$('div.eagle_lv_5').remove();
                $('div.chosen').remove();

                //附加
                $('div.eagle_lv_3').after(next_eagle_html);
            break;

            case 4:
                var eagle_lv1_val        =$.trim($('.eagle_lv_1 :selected').text());
                var eagle_lv2_val        =$.trim($('.eagle_lv_2 :selected').text());
                var eagle_lv3_val        =$.trim($('.eagle_lv_3 :selected').text());
                var eagle_lv4_val        =$.trim($('.eagle_lv_4 :selected').text());               
                var arry_eagle_lv4_val   =eagle_lv4_val.split("……");
                var eagle_lv4_index      =parseInt($('.eagle_lv_4 :selected').index());    
                var eagle_code           =parseInt(article_eagle_code[eagle_lv1_val][eagle_lv2_val][eagle_lv3_val][eagle_lv4_index-1]);
                var arry_my_borrow_length=0;
                for(key1 in arry_my_borrow){arry_my_borrow_length++}
                
                var next_eagle_html='';
                
                var oarticle_content=document.getElementById('article_content[]');
                for(key1 in arry_eagle_lv4_val){
                    if(trim(arry_eagle_lv4_val[key1])!==''){                     
                        $(oarticle_content).val($.trim($(oarticle_content).val())+$.trim(arry_eagle_lv4_val[key1])+'……');                         
                    }
                }
                oarticle_content.focus();
                $('#eagle_code').val($('#eagle_code').val()+eagle_code+',');
                //$('.select_eagle_lv_1').toggle('hide');
                $('.select_eagle_lv_1>option:eq(0)').attr('selected', true);
                $('div.eagle_lv_2').remove();
                $('div.eagle_lv_3').remove();
                $('div.eagle_lv_4').remove();
                $('div.chosen').remove();
                
                var article_category=[]
                var $eagle_codes=$.trim($('#eagle_code').val());
                var arry_eagle_codes=$eagle_codes.split(",");
                for(key1 in arry_eagle_codes){
                    if(in_array(parseInt($.trim(arry_eagle_codes[key1])),[1,2,3,4,5,6,19,20,21,22,23,24,47,48,49,50,51,52,53,54,55,56,57,58,59])){
                        article_category.push(1);
                    }else if(in_array(parseInt($.trim(arry_eagle_codes[key1])),[7,8,9,10,11,12,13,14,15,16,17,18,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46])){
                        article_category.push(2);
                    }else{}
                }
                article_category=$.unique(article_category);
                var lv=0;
                for(key2 in article_category){
                    lv=lv+parseInt(article_category[key2]);
                }
                if(lv===1){
                    $('#article_category>option:eq(2)').attr('selected', true);
                }
                if(lv===2){
                    $('#article_category>option:eq(3)').attr('selected', true);
                }
                if(lv===3){
                    $('#article_category>option:eq(1)').attr('selected', true);
                }   

                //next_eagle_html+=   '<div class="row chosen hidden-xs hidden-sm hidden-md hidden-lg" style="position:relative;margin-top:35px;margin-bottom:-15px;">';
                //next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                //next_eagle_html+=           '<select data-placeholder="請選擇一本書來發文......" class="form-control chosen-select" multiple>';
                //next_eagle_html+=               '<option value=""></option>';
                //
                //for(key1 in arry_my_borrow){
                //    next_eagle_html+=           '<option value="'+trim(key1)+'">'+trim(arry_my_borrow[key1])+'</option>';
                //}
                //
                //next_eagle_html+=           '</select>';
                //next_eagle_html+=       '</div>';
                //next_eagle_html+=   '</div>';
                //
                ////chosen_start
                //var config = {
                //    '.chosen-select':{
                //        allow_single_deselect:true,
                //        disable_search_threshold:0,
                //        no_results_text:'Oops, 查無 ',
                //        search_contains:true
                //    }
                //}
                //for (var selector in config) {
                //    $(selector).chosen(config[selector]);
                //}

                //next_eagle_html+=   '<div class="row eagle_lv_5">';
                //next_eagle_html+=       '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                //next_eagle_html+=           '<form id="Form1"  name="Form1" method="post" onsubmit="return false;">';
                //
                //next_eagle_html+=               '<hr></hr>';
                //next_eagle_html+=               '<div class="row chosen" style="position:relative;margin-top:30px;margin-bottom:15px;">';
                //next_eagle_html+=                   '<div class="col-xs-12 col-sm-12 col-md-12 col-lg12">';
                //
                //if(arry_my_borrow_length>1){
                //    next_eagle_html+=                       '<select class="form-control" id="book_sid" name="book_sid">';
                //    next_eagle_html+=                           '<option value="" disabled="disabled" selected>請選擇一本書來發文......</option>';
                //    for(key1 in arry_my_borrow){
                //        next_eagle_html+=                       '<option value="'+trim(key1)+'">'+trim(arry_my_borrow[key1])+'</option>';
                //    }
                //    next_eagle_html+=                       '</select>';                
                //}else{
                //    next_eagle_html+=                       '<select class="form-control" id="book_sid" name="book_sid" style="display:none;">';
                //    for(key1 in arry_my_borrow){
                //        next_eagle_html+=                       '<option value="'+trim(key1)+'" selected>'+trim(arry_my_borrow[key1])+'</option>';
                //    }
                //    next_eagle_html+=                       '</select>';                
                //
                //}
                //
                //next_eagle_html+=                   '</div>';
                //next_eagle_html+=               '</div>';
                //
                //next_eagle_html+=               '<div class="form-group">';
                //next_eagle_html+=                   '<input type="text" id="article_title" name="article_title" class="form-control" placeholder="請輸入文章標題">';
                //next_eagle_html+=               '</div>';
                //
                //for(key1 in arry_eagle_lv4_val){
                //    if(trim(arry_eagle_lv4_val[key1])!==''){
                //        next_eagle_html+=       '<div class="form-group">';
                //        next_eagle_html+=           '<textarea class="form-control" id="article_content[]" name="article_content[]" rows="3" placeholder="'+trim(arry_eagle_lv4_val[key1])+'">'+trim(arry_eagle_lv4_val[key1])+'</textarea>';
                //        next_eagle_html+=       '</div>';                    
                //    }
                //}
                //
                //next_eagle_html+=               '<div class="checkbox">';
                //next_eagle_html+=                   '<label>';
                //next_eagle_html+=                       '<input type="checkbox" id="send_chk">我已閱讀過並同意遵守討論區規則';
                //next_eagle_html+=                   '</label>';
                //next_eagle_html+=                   ', <a target="_blank" href="forum.php?method=view_mssr_forum_article_reply_rule" style="color:#428bca;">按這裡檢視討論區規則</a>';
                //next_eagle_html+=               '</div>';
                //
                //next_eagle_html+=               '<hr></hr>';
                //next_eagle_html+=               '<button type="button" class="btn btn-default pull-right" onclick="Btn_add_article();void(0);">送出</button>';
                //
                //next_eagle_html+=               '<div class="form-group hidden">';
                //next_eagle_html+=                   '<input type="hidden" class="form-control" name="eagle_code" value="'+eagle_code+'">';
                //next_eagle_html+=                   '<input type="hidden" class="form-control" name="article_from" value="'+parseInt(get_from)+'">';
                //next_eagle_html+=                   '<input type="hidden" class="form-control" name="group_id" value="'+parseInt(get_group_id)+'">';
                //next_eagle_html+=                   '<input type="hidden" class="form-control" name="send_url" value="'+send_url+'">';
                //next_eagle_html+=                   '<input type="hidden" class="form-control" name="method" value="add_article">';
                //next_eagle_html+=               '</div>';
                //
                //next_eagle_html+=           '</form>';
                //next_eagle_html+=       '</div>';
                //next_eagle_html+=   '</div>';
                //
                ////清除
                //$('div.eagle_lv_5').remove();
                //$('div.chosen').remove();
                //
                ////附加
                //$add_article.append(next_eagle_html);               
            break;

            default:
                return false;
            break;
        }
    }