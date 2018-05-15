<?php
//-------------------------------------------------------
//物件: page_plugin
//用途: 粉絲專頁
//-------------------------------------------------------

    namespace inc\fb_api;

    class page_plugin{
    //---------------------------------------------------
    //粉絲專頁
    //---------------------------------------------------
    //
    //---------------------------------------------------

        public static function page_plugin_init(){

            $html='
                <div id="fb-root"></div>
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = "//connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v2.5";
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, "script", "facebook-jssdk"));</script>
            ';

            //回傳
            return $html;
        }

        public static function page_plugin_show(){

            $html='
                <div class="fb-page" data-href="https://www.facebook.com/schools.of.tomorrow.tw" data-tabs="timeline" data-width="180" data-height="500" data-small-header="true"
                data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"
                style="position:relative;right:7px;margin-bottom:25px;">
                    <div class="fb-xfbml-parse-ignore">
                        <blockquote cite="https://www.facebook.com/schools.of.tomorrow.tw">
                            <a href="https://www.facebook.com/schools.of.tomorrow.tw">明日學校</a>
                        </blockquote>
                    </div>
                </div>
            ';

            //回傳
            return $html;
        }

        //建構子
        function __construct(){

        }

        //解構子
        function __destruct(){

        }
    }
?>