function fast_area_config(obj,_top,_right){    
//---------------------------------------------------
//快速切換設置
//---------------------------------------------------
//obj       容器
//_top      距離上方邊距
//_right    距離右方邊距
//---------------------------------------------------
//回傳值
//---------------------------------------------------

    //-----------------------------------------------
    //參數設置
    //-----------------------------------------------

        var $win         = $(window),
            $fast_area   = $(obj).show(),
            $fast_content=$('#fast_content').hide(),
            
            _top         = _top,    //距離上方之邊距
            _right       = _right   //距離右方之邊距

            _movespeed   = 1;       //移動的速度

    //-----------------------------------------------
    //移動到定點
    //-----------------------------------------------

        $fast_area.css({
            top:_top,
            right:_right
        });

        $fast_content.css({
            top:_top,
            right:-$(this).width()
        });

    //-----------------------------------------------
    //事件處理
    //-----------------------------------------------
        
        //滑鼠移入移出
        $fast_area.mouseover(function(){
            $fast_content.stop().animate({
                top:_top,
                right:_right+15,
                opacity:1 
            }, _movespeed,function(){$fast_content.show()});      
        }).mouseout(function(){
            $fast_content.stop().animate({
                top:_top,
                right:-$(this).width(),
                opacity:0
            }, _movespeed,function(){$fast_content.hide()});          
        });

        $fast_content.mouseover(function(){
            $(this).stop().animate({
                top:_top,
                right:_right+15,
                opacity:1 
            }, _movespeed,function(){$fast_content.show()});      
        }).mouseout(function(){
            $(this).stop().animate({
                top:_top,
                right:-$(this).width(),
                opacity:0
            }, _movespeed,function(){$fast_content.hide()});           
        });

        ////scroll, resize 事件
        //$win.bind('scroll resize', function(){
        //    var $this = $(this);
        //    //控制移動
        //    $change_identity.stop().animate({
        //        top: $this.scrollTop() + $this.height() - _height - _diffY,
        //        left: $this.scrollLeft() + $this.width() - _width - _diffX
        //    }, _moveSpeed);
        //}).scroll()
}