<?php
//-------------------------------------------------------
//函式: google_track()
//用途: google analytics
//-------------------------------------------------------

    function google_track(){
    //---------------------------------------------------
    //函式: google_track()
    //用途: google analytics
    //---------------------------------------------------
    //
    //---------------------------------------------------

        /*$ga="
            <script>
                var _gaq = _gaq || [];

                //加強版連結歸功
                var pluginUrl = (('https:' == document.location.protocol) ?
                'https://ssl.' : 'http://www.') +
                'google-analytics.com/plugins/ga/inpage_linkid.js';
                _gaq.push(['_require', 'inpage_linkid', pluginUrl]);

                _gaq.push(['_setAccount', 'UA-69803960-1']);
                _gaq.push(['_trackPageview']);

                (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                })();

                //追蹤出站連結
                var a = document.getElementsByTagName('a');
                    for(i = 0; i < a.length; i++){
                        if (a[i].href.indexOf(location.host) == -1 && a[i].href.match(/^http://i)){
                        a[i].onclick = function(){_gaq.push(['_trackEvent', 'outgoing_links', this.href.replace(/^http://i, '')]);}
                    }
                }

                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                ga('create', 'UA-69803960-1', 'auto');
                ga('require', 'linkid');
                ga('send', 'pageview');

            </script>
        ";*/
        $ga="";

        //回傳
        return $ga;
    }
?>