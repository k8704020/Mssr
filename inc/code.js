//-------------------------------------------------------
//inc
//-------------------------------------------------------
//root  根單元
//
//-------------------------------------------------------
//root  根單元
//-------------------------------------------------------
//  root/pages()        分頁列
//  root/qform()        查詢表單列
//  root/logo()         首頁logo
//  root/jquery.s2t()   簡繁轉換
//
//-------------------------------------------------------


//-------------------------------------------------------
//root  根單元
//-------------------------------------------------------

    //---------------------------------------------------
    //簡繁轉換
    //---------------------------------------------------

        /**
         * jquery-s2t v0.1.0
         *
         * https://github.com/hustlzp/jquery-s2t
         * A jQuery plugin to convert between Simplified Chinese and Traditional Chinese.
         * Tested in IE6+, Chrome, Firefox.
         *
         * Copyright 2013-2014 hustlzp
         * Released under the MIT license
         */

        (function($) {

            // 共收录2553条简繁对照
            // 尚未考证是否正确、重复、完整

            /**
             * 简体字
             * @const
             */
            var S = new String('万与丑专业丛东丝丢两严丧个丬丰临为丽举么义乌乐乔习乡书买乱争于亏云亘亚产亩亲亵亸亿仅从仑仓仪们价众优伙会伛伞伟传伤伥伦伧伪伫体余佣佥侠侣侥侦侧侨侩侪侬俣俦俨俩俪俭债倾偬偻偾偿傥傧储傩儿兑兖党兰关兴兹养兽冁内冈册写军农冢冯冲决况冻净凄凉凌减凑凛几凤凫凭凯击凼凿刍划刘则刚创删别刬刭刽刿剀剂剐剑剥剧劝办务劢动励劲劳势勋勐勚匀匦匮区医华协单卖卢卤卧卫却卺厂厅历厉压厌厍厕厢厣厦厨厩厮县参叆叇双发变叙叠叶号叹叽吁后吓吕吗吣吨听启吴呒呓呕呖呗员呙呛呜咏咔咙咛咝咤咴咸哌响哑哒哓哔哕哗哙哜哝哟唛唝唠唡唢唣唤唿啧啬啭啮啰啴啸喷喽喾嗫呵嗳嘘嘤嘱噜噼嚣嚯团园囱围囵国图圆圣圹场坂坏块坚坛坜坝坞坟坠垄垅垆垒垦垧垩垫垭垯垱垲垴埘埙埚埝埯堑堕塆墙壮声壳壶壸处备复够头夸夹夺奁奂奋奖奥妆妇妈妩妪妫姗姜娄娅娆娇娈娱娲娴婳婴婵婶媪嫒嫔嫱嬷孙学孪宁宝实宠审宪宫宽宾寝对寻导寿将尔尘尧尴尸尽层屃屉届属屡屦屿岁岂岖岗岘岙岚岛岭岳岽岿峃峄峡峣峤峥峦崂崃崄崭嵘嵚嵛嵝嵴巅巩巯币帅师帏帐帘帜带帧帮帱帻帼幂幞干并广庄庆庐庑库应庙庞废庼廪开异弃张弥弪弯弹强归当录彟彦彻径徕御忆忏忧忾怀态怂怃怄怅怆怜总怼怿恋恳恶恸恹恺恻恼恽悦悫悬悭悯惊惧惨惩惫惬惭惮惯愍愠愤愦愿慑慭憷懑懒懔戆戋戏戗战戬户扎扑扦执扩扪扫扬扰抚抛抟抠抡抢护报担拟拢拣拥拦拧拨择挂挚挛挜挝挞挟挠挡挢挣挤挥挦捞损捡换捣据捻掳掴掷掸掺掼揸揽揿搀搁搂搅携摄摅摆摇摈摊撄撑撵撷撸撺擞攒敌敛数斋斓斗斩断无旧时旷旸昙昼昽显晋晒晓晔晕晖暂暧札术朴机杀杂权条来杨杩杰极构枞枢枣枥枧枨枪枫枭柜柠柽栀栅标栈栉栊栋栌栎栏树栖样栾桊桠桡桢档桤桥桦桧桨桩梦梼梾检棂椁椟椠椤椭楼榄榇榈榉槚槛槟槠横樯樱橥橱橹橼檐檩欢欤欧歼殁殇残殒殓殚殡殴毁毂毕毙毡毵氇气氢氩氲汇汉污汤汹沓沟没沣沤沥沦沧沨沩沪沵泞泪泶泷泸泺泻泼泽泾洁洒洼浃浅浆浇浈浉浊测浍济浏浐浑浒浓浔浕涂涌涛涝涞涟涠涡涢涣涤润涧涨涩淀渊渌渍渎渐渑渔渖渗温游湾湿溃溅溆溇滗滚滞滟滠满滢滤滥滦滨滩滪漤潆潇潋潍潜潴澜濑濒灏灭灯灵灾灿炀炉炖炜炝点炼炽烁烂烃烛烟烦烧烨烩烫烬热焕焖焘煅煳熘爱爷牍牦牵牺犊犟状犷犸犹狈狍狝狞独狭狮狯狰狱狲猃猎猕猡猪猫猬献獭玑玙玚玛玮环现玱玺珉珏珐珑珰珲琎琏琐琼瑶瑷璇璎瓒瓮瓯电画畅畲畴疖疗疟疠疡疬疮疯疱疴痈痉痒痖痨痪痫痴瘅瘆瘗瘘瘪瘫瘾瘿癞癣癫癯皑皱皲盏盐监盖盗盘眍眦眬着睁睐睑瞒瞩矫矶矾矿砀码砖砗砚砜砺砻砾础硁硅硕硖硗硙硚确硷碍碛碜碱碹磙礼祎祢祯祷祸禀禄禅离秃秆种积称秽秾稆税稣稳穑穷窃窍窑窜窝窥窦窭竖竞笃笋笔笕笺笼笾筑筚筛筜筝筹签简箓箦箧箨箩箪箫篑篓篮篱簖籁籴类籼粜粝粤粪粮糁糇紧絷纟纠纡红纣纤纥约级纨纩纪纫纬纭纮纯纰纱纲纳纴纵纶纷纸纹纺纻纼纽纾线绀绁绂练组绅细织终绉绊绋绌绍绎经绐绑绒结绔绕绖绗绘给绚绛络绝绞统绠绡绢绣绤绥绦继绨绩绪绫绬续绮绯绰绱绲绳维绵绶绷绸绹绺绻综绽绾绿缀缁缂缃缄缅缆缇缈缉缊缋缌缍缎缏缐缑缒缓缔缕编缗缘缙缚缛缜缝缞缟缠缡缢缣缤缥缦缧缨缩缪缫缬缭缮缯缰缱缲缳缴缵罂网罗罚罢罴羁羟羡翘翙翚耢耧耸耻聂聋职聍联聩聪肃肠肤肷肾肿胀胁胆胜胧胨胪胫胶脉脍脏脐脑脓脔脚脱脶脸腊腌腘腭腻腼腽腾膑臜舆舣舰舱舻艰艳艹艺节芈芗芜芦苁苇苈苋苌苍苎苏苘苹茎茏茑茔茕茧荆荐荙荚荛荜荞荟荠荡荣荤荥荦荧荨荩荪荫荬荭荮药莅莜莱莲莳莴莶获莸莹莺莼萚萝萤营萦萧萨葱蒇蒉蒋蒌蓝蓟蓠蓣蓥蓦蔷蔹蔺蔼蕲蕴薮藁藓虏虑虚虫虬虮虽虾虿蚀蚁蚂蚕蚝蚬蛊蛎蛏蛮蛰蛱蛲蛳蛴蜕蜗蜡蝇蝈蝉蝎蝼蝾螀螨蟏衅衔补衬衮袄袅袆袜袭袯装裆裈裢裣裤裥褛褴襁襕见观觃规觅视觇览觉觊觋觌觍觎觏觐觑觞触觯詟誉誊讠计订讣认讥讦讧讨让讪讫训议讯记讱讲讳讴讵讶讷许讹论讻讼讽设访诀证诂诃评诅识诇诈诉诊诋诌词诎诏诐译诒诓诔试诖诗诘诙诚诛诜话诞诟诠诡询诣诤该详诧诨诩诪诫诬语诮误诰诱诲诳说诵诶请诸诹诺读诼诽课诿谀谁谂调谄谅谆谇谈谊谋谌谍谎谏谐谑谒谓谔谕谖谗谘谙谚谛谜谝谞谟谠谡谢谣谤谥谦谧谨谩谪谫谬谭谮谯谰谱谲谳谴谵谶谷豮贝贞负贠贡财责贤败账货质贩贪贫贬购贮贯贰贱贲贳贴贵贶贷贸费贺贻贼贽贾贿赀赁赂赃资赅赆赇赈赉赊赋赌赍赎赏赐赑赒赓赔赕赖赗赘赙赚赛赜赝赞赟赠赡赢赣赪赵赶趋趱趸跃跄跖跞践跶跷跸跹跻踊踌踪踬踯蹑蹒蹰蹿躏躜躯车轧轨轩轪轫转轭轮软轰轱轲轳轴轵轶轷轸轹轺轻轼载轾轿辀辁辂较辄辅辆辇辈辉辊辋辌辍辎辏辐辑辒输辔辕辖辗辘辙辚辞辩辫边辽达迁过迈运还这进远违连迟迩迳迹适选逊递逦逻遗遥邓邝邬邮邹邺邻郁郄郏郐郑郓郦郧郸酝酦酱酽酾酿释里鉅鉴銮錾钆钇针钉钊钋钌钍钎钏钐钑钒钓钔钕钖钗钘钙钚钛钝钞钟钠钡钢钣钤钥钦钧钨钩钪钫钬钭钮钯钰钱钲钳钴钵钶钷钸钹钺钻钼钽钾钿铀铁铂铃铄铅铆铈铉铊铋铍铎铏铐铑铒铕铗铘铙铚铛铜铝铞铟铠铡铢铣铤铥铦铧铨铪铫铬铭铮铯铰铱铲铳铴铵银铷铸铹铺铻铼铽链铿销锁锂锃锄锅锆锇锈锉锊锋锌锍锎锏锐锑锒锓锔锕锖锗错锚锜锞锟锠锡锢锣锤锥锦锨锩锫锬锭键锯锰锱锲锳锴锵锶锷锸锹锺锻锼锽锾锿镀镁镂镃镆镇镈镉镊镌镍镎镏镐镑镒镕镖镗镙镚镛镜镝镞镟镠镡镢镣镤镥镦镧镨镩镪镫镬镭镮镯镰镱镲镳镴镶长门闩闪闫闬闭问闯闰闱闲闳间闵闶闷闸闹闺闻闼闽闾闿阀阁阂阃阄阅阆阇阈阉阊阋阌阍阎阏阐阑阒阓阔阕阖阗阘阙阚阛队阳阴阵阶际陆陇陈陉陕陧陨险随隐隶隽难雏雠雳雾霁霉霭靓静靥鞑鞒鞯鞴韦韧韨韩韪韫韬韵页顶顷顸项顺须顼顽顾顿颀颁颂颃预颅领颇颈颉颊颋颌颍颎颏颐频颒颓颔颕颖颗题颙颚颛颜额颞颟颠颡颢颣颤颥颦颧风飏飐飑飒飓飔飕飖飗飘飙飚飞飨餍饤饥饦饧饨饩饪饫饬饭饮饯饰饱饲饳饴饵饶饷饸饹饺饻饼饽饾饿馀馁馂馃馄馅馆馇馈馉馊馋馌馍馎馏馐馑馒馓馔馕马驭驮驯驰驱驲驳驴驵驶驷驸驹驺驻驼驽驾驿骀骁骂骃骄骅骆骇骈骉骊骋验骍骎骏骐骑骒骓骔骕骖骗骘骙骚骛骜骝骞骟骠骡骢骣骤骥骦骧髅髋髌鬓魇魉鱼鱽鱾鱿鲀鲁鲂鲄鲅鲆鲇鲈鲉鲊鲋鲌鲍鲎鲏鲐鲑鲒鲓鲔鲕鲖鲗鲘鲙鲚鲛鲜鲝鲞鲟鲠鲡鲢鲣鲤鲥鲦鲧鲨鲩鲪鲫鲬鲭鲮鲯鲰鲱鲲鲳鲴鲵鲶鲷鲸鲹鲺鲻鲼鲽鲾鲿鳀鳁鳂鳃鳄鳅鳆鳇鳈鳉鳊鳋鳌鳍鳎鳏鳐鳑鳒鳓鳔鳕鳖鳗鳘鳙鳛鳜鳝鳞鳟鳠鳡鳢鳣鸟鸠鸡鸢鸣鸤鸥鸦鸧鸨鸩鸪鸫鸬鸭鸮鸯鸰鸱鸲鸳鸴鸵鸶鸷鸸鸹鸺鸻鸼鸽鸾鸿鹀鹁鹂鹃鹄鹅鹆鹇鹈鹉鹊鹋鹌鹍鹎鹏鹐鹑鹒鹓鹔鹕鹖鹗鹘鹚鹛鹜鹝鹞鹟鹠鹡鹢鹣鹤鹥鹦鹧鹨鹩鹪鹫鹬鹭鹯鹰鹱鹲鹳鹴鹾麦麸黄黉黡黩黪黾鼋鼌鼍鼗鼹齄齐齑齿龀龁龂龃龄龅龆龇龈龉龊龋龌龙龚龛龟志制咨只里系范松没尝尝闹面准钟别闲干尽脏拼');

            /**
             * 繁体字
             * @const
             */
            var T = new String('萬與醜專業叢東絲丟兩嚴喪個爿豐臨為麗舉麼義烏樂喬習鄉書買亂爭於虧雲亙亞產畝親褻嚲億僅從侖倉儀們價眾優夥會傴傘偉傳傷倀倫傖偽佇體餘傭僉俠侶僥偵側僑儈儕儂俁儔儼倆儷儉債傾傯僂僨償儻儐儲儺兒兌兗黨蘭關興茲養獸囅內岡冊寫軍農塚馮衝決況凍淨淒涼淩減湊凜幾鳳鳧憑凱擊氹鑿芻劃劉則剛創刪別剗剄劊劌剴劑剮劍剝劇勸辦務勱動勵勁勞勢勳猛勩勻匭匱區醫華協單賣盧鹵臥衛卻巹廠廳曆厲壓厭厙廁廂厴廈廚廄廝縣參靉靆雙發變敘疊葉號歎嘰籲後嚇呂嗎唚噸聽啟吳嘸囈嘔嚦唄員咼嗆嗚詠哢嚨嚀噝吒噅鹹呱響啞噠嘵嗶噦嘩噲嚌噥喲嘜嗊嘮啢嗩唕喚呼嘖嗇囀齧囉嘽嘯噴嘍嚳囁嗬噯噓嚶囑嚕劈囂謔團園囪圍圇國圖圓聖壙場阪壞塊堅壇壢壩塢墳墜壟壟壚壘墾坰堊墊埡墶壋塏堖塒塤堝墊垵塹墮壪牆壯聲殼壺壼處備複夠頭誇夾奪奩奐奮獎奧妝婦媽嫵嫗媯姍薑婁婭嬈嬌孌娛媧嫻嫿嬰嬋嬸媼嬡嬪嬙嬤孫學孿寧寶實寵審憲宮寬賓寢對尋導壽將爾塵堯尷屍盡層屭屜屆屬屢屨嶼歲豈嶇崗峴嶴嵐島嶺嶽崠巋嶨嶧峽嶢嶠崢巒嶗崍嶮嶄嶸嶔崳嶁脊巔鞏巰幣帥師幃帳簾幟帶幀幫幬幘幗冪襆幹並廣莊慶廬廡庫應廟龐廢廎廩開異棄張彌弳彎彈強歸當錄彠彥徹徑徠禦憶懺憂愾懷態慫憮慪悵愴憐總懟懌戀懇惡慟懨愷惻惱惲悅愨懸慳憫驚懼慘懲憊愜慚憚慣湣慍憤憒願懾憖怵懣懶懍戇戔戲戧戰戩戶紮撲扡執擴捫掃揚擾撫拋摶摳掄搶護報擔擬攏揀擁攔擰撥擇掛摯攣掗撾撻挾撓擋撟掙擠揮撏撈損撿換搗據撚擄摑擲撣摻摜摣攬撳攙擱摟攪攜攝攄擺搖擯攤攖撐攆擷擼攛擻攢敵斂數齋斕鬥斬斷無舊時曠暘曇晝曨顯晉曬曉曄暈暉暫曖劄術樸機殺雜權條來楊榪傑極構樅樞棗櫪梘棖槍楓梟櫃檸檉梔柵標棧櫛櫳棟櫨櫟欄樹棲樣欒棬椏橈楨檔榿橋樺檜槳樁夢檮棶檢欞槨櫝槧欏橢樓欖櫬櫚櫸檟檻檳櫧橫檣櫻櫫櫥櫓櫞簷檁歡歟歐殲歿殤殘殞殮殫殯毆毀轂畢斃氈毿氌氣氫氬氳彙漢汙湯洶遝溝沒灃漚瀝淪滄渢溈滬濔濘淚澩瀧瀘濼瀉潑澤涇潔灑窪浹淺漿澆湞溮濁測澮濟瀏滻渾滸濃潯濜塗湧濤澇淶漣潿渦溳渙滌潤澗漲澀澱淵淥漬瀆漸澠漁瀋滲溫遊灣濕潰濺漵漊潷滾滯灩灄滿瀅濾濫灤濱灘澦濫瀠瀟瀲濰潛瀦瀾瀨瀕灝滅燈靈災燦煬爐燉煒熗點煉熾爍爛烴燭煙煩燒燁燴燙燼熱煥燜燾煆糊溜愛爺牘犛牽犧犢強狀獷獁猶狽麅獮獰獨狹獅獪猙獄猻獫獵獼玀豬貓蝟獻獺璣璵瑒瑪瑋環現瑲璽瑉玨琺瓏璫琿璡璉瑣瓊瑤璦璿瓔瓚甕甌電畫暢佘疇癤療瘧癘瘍鬁瘡瘋皰屙癰痙癢瘂癆瘓癇癡癉瘮瘞瘺癟癱癮癭癩癬癲臒皚皺皸盞鹽監蓋盜盤瞘眥矓著睜睞瞼瞞矚矯磯礬礦碭碼磚硨硯碸礪礱礫礎硜矽碩硤磽磑礄確鹼礙磧磣堿镟滾禮禕禰禎禱禍稟祿禪離禿稈種積稱穢穠穭稅穌穩穡窮竊竅窯竄窩窺竇窶豎競篤筍筆筧箋籠籩築篳篩簹箏籌簽簡籙簀篋籜籮簞簫簣簍籃籬籪籟糴類秈糶糲粵糞糧糝餱緊縶糸糾紆紅紂纖紇約級紈纊紀紉緯紜紘純紕紗綱納紝縱綸紛紙紋紡紵紖紐紓線紺絏紱練組紳細織終縐絆紼絀紹繹經紿綁絨結絝繞絰絎繪給絢絳絡絕絞統綆綃絹繡綌綏絛繼綈績緒綾緓續綺緋綽緔緄繩維綿綬繃綢綯綹綣綜綻綰綠綴緇緙緗緘緬纜緹緲緝縕繢緦綞緞緶線緱縋緩締縷編緡緣縉縛縟縝縫縗縞纏縭縊縑繽縹縵縲纓縮繆繅纈繚繕繒韁繾繰繯繳纘罌網羅罰罷羆羈羥羨翹翽翬耮耬聳恥聶聾職聹聯聵聰肅腸膚膁腎腫脹脅膽勝朧腖臚脛膠脈膾髒臍腦膿臠腳脫腡臉臘醃膕齶膩靦膃騰臏臢輿艤艦艙艫艱豔艸藝節羋薌蕪蘆蓯葦藶莧萇蒼苧蘇檾蘋莖蘢蔦塋煢繭荊薦薘莢蕘蓽蕎薈薺蕩榮葷滎犖熒蕁藎蓀蔭蕒葒葤藥蒞蓧萊蓮蒔萵薟獲蕕瑩鶯蓴蘀蘿螢營縈蕭薩蔥蕆蕢蔣蔞藍薊蘺蕷鎣驀薔蘞藺藹蘄蘊藪槁蘚虜慮虛蟲虯蟣雖蝦蠆蝕蟻螞蠶蠔蜆蠱蠣蟶蠻蟄蛺蟯螄蠐蛻蝸蠟蠅蟈蟬蠍螻蠑螿蟎蠨釁銜補襯袞襖嫋褘襪襲襏裝襠褌褳襝褲襇褸襤繈襴見觀覎規覓視覘覽覺覬覡覿覥覦覯覲覷觴觸觶讋譽謄訁計訂訃認譏訐訌討讓訕訖訓議訊記訒講諱謳詎訝訥許訛論訩訟諷設訪訣證詁訶評詛識詗詐訴診詆謅詞詘詔詖譯詒誆誄試詿詩詰詼誠誅詵話誕詬詮詭詢詣諍該詳詫諢詡譸誡誣語誚誤誥誘誨誑說誦誒請諸諏諾讀諑誹課諉諛誰諗調諂諒諄誶談誼謀諶諜謊諫諧謔謁謂諤諭諼讒諮諳諺諦謎諞諝謨讜謖謝謠謗諡謙謐謹謾謫譾謬譚譖譙讕譜譎讞譴譫讖穀豶貝貞負貟貢財責賢敗賬貨質販貪貧貶購貯貫貳賤賁貰貼貴貺貸貿費賀貽賊贄賈賄貲賃賂贓資賅贐賕賑賚賒賦賭齎贖賞賜贔賙賡賠賧賴賵贅賻賺賽賾贗讚贇贈贍贏贛赬趙趕趨趲躉躍蹌蹠躒踐躂蹺蹕躚躋踴躊蹤躓躑躡蹣躕躥躪躦軀車軋軌軒軑軔轉軛輪軟轟軲軻轤軸軹軼軤軫轢軺輕軾載輊轎輈輇輅較輒輔輛輦輩輝輥輞輬輟輜輳輻輯轀輸轡轅轄輾轆轍轔辭辯辮邊遼達遷過邁運還這進遠違連遲邇逕跡適選遜遞邐邏遺遙鄧鄺鄔郵鄒鄴鄰鬱郤郟鄶鄭鄆酈鄖鄲醞醱醬釅釃釀釋裏钜鑒鑾鏨釓釔針釘釗釙釕釷釺釧釤鈒釩釣鍆釹鍚釵鈃鈣鈈鈦鈍鈔鍾鈉鋇鋼鈑鈐鑰欽鈞鎢鉤鈧鈁鈥鈄鈕鈀鈺錢鉦鉗鈷缽鈳鉕鈽鈸鉞鑽鉬鉭鉀鈿鈾鐵鉑鈴鑠鉛鉚鈰鉉鉈鉍鈹鐸鉶銬銠鉺銪鋏鋣鐃銍鐺銅鋁銱銦鎧鍘銖銑鋌銩銛鏵銓鉿銚鉻銘錚銫鉸銥鏟銃鐋銨銀銣鑄鐒鋪鋙錸鋱鏈鏗銷鎖鋰鋥鋤鍋鋯鋨鏽銼鋝鋒鋅鋶鐦鐧銳銻鋃鋟鋦錒錆鍺錯錨錡錁錕錩錫錮鑼錘錐錦鍁錈錇錟錠鍵鋸錳錙鍥鍈鍇鏘鍶鍔鍤鍬鍾鍛鎪鍠鍰鎄鍍鎂鏤鎡鏌鎮鎛鎘鑷鐫鎳鎿鎦鎬鎊鎰鎔鏢鏜鏍鏰鏞鏡鏑鏃鏇鏐鐔钁鐐鏷鑥鐓鑭鐠鑹鏹鐙鑊鐳鐶鐲鐮鐿鑔鑣鑞鑲長門閂閃閆閈閉問闖閏闈閑閎間閔閌悶閘鬧閨聞闥閩閭闓閥閣閡閫鬮閱閬闍閾閹閶鬩閿閽閻閼闡闌闃闠闊闋闔闐闒闕闞闤隊陽陰陣階際陸隴陳陘陝隉隕險隨隱隸雋難雛讎靂霧霽黴靄靚靜靨韃鞽韉韝韋韌韍韓韙韞韜韻頁頂頃頇項順須頊頑顧頓頎頒頌頏預顱領頗頸頡頰頲頜潁熲頦頤頻頮頹頷頴穎顆題顒顎顓顏額顳顢顛顙顥纇顫顬顰顴風颺颭颮颯颶颸颼颻飀飄飆飆飛饗饜飣饑飥餳飩餼飪飫飭飯飲餞飾飽飼飿飴餌饒餉餄餎餃餏餅餑餖餓餘餒餕餜餛餡館餷饋餶餿饞饁饃餺餾饈饉饅饊饌饢馬馭馱馴馳驅馹駁驢駔駛駟駙駒騶駐駝駑駕驛駘驍罵駰驕驊駱駭駢驫驪騁驗騂駸駿騏騎騍騅騌驌驂騙騭騤騷騖驁騮騫騸驃騾驄驏驟驥驦驤髏髖髕鬢魘魎魚魛魢魷魨魯魴魺鮁鮃鯰鱸鮋鮓鮒鮊鮑鱟鮍鮐鮭鮚鮳鮪鮞鮦鰂鮜鱠鱭鮫鮮鮺鯗鱘鯁鱺鰱鰹鯉鰣鰷鯀鯊鯇鮶鯽鯒鯖鯪鯕鯫鯡鯤鯧鯝鯢鯰鯛鯨鯵鯴鯔鱝鰈鰏鱨鯷鰮鰃鰓鱷鰍鰒鰉鰁鱂鯿鰠鼇鰭鰨鰥鰩鰟鰜鰳鰾鱈鱉鰻鰵鱅鰼鱖鱔鱗鱒鱯鱤鱧鱣鳥鳩雞鳶鳴鳲鷗鴉鶬鴇鴆鴣鶇鸕鴨鴞鴦鴒鴟鴝鴛鴬鴕鷥鷙鴯鴰鵂鴴鵃鴿鸞鴻鵐鵓鸝鵑鵠鵝鵒鷳鵜鵡鵲鶓鵪鶤鵯鵬鵮鶉鶊鵷鷫鶘鶡鶚鶻鶿鶥鶩鷊鷂鶲鶹鶺鷁鶼鶴鷖鸚鷓鷚鷯鷦鷲鷸鷺鸇鷹鸌鸏鸛鸘鹺麥麩黃黌黶黷黲黽黿鼂鼉鞀鼴齇齊齏齒齔齕齗齟齡齙齠齜齦齬齪齲齷龍龔龕龜誌製谘隻裡係範鬆冇嚐嘗鬨麵準鐘彆閒乾儘臟拚');

            /**
             * 转换文本
             * @param {String} str - 待转换的文本
             * @param {Boolean} toT - 是否转换成繁体
             * @returns {String} - 转换结果
             */
            function tranStr(str, toT) {
                var i;
                var letter;
                var code;
                var isChinese;
                var index;
                var src, des;
                var result = '';

                if (toT) {
                    src = S;
                    des = T;
                } else {
                    src = T;
                    des = S;
                }

                if (typeof str !== "string") {
                    return str;
                }

                for (i = 0; i < str.length; i++) {
                    letter = str.charAt(i);
                    code = str.charCodeAt(i); 
                    
                    // 根据字符的Unicode判断是否为汉字，以提高性能
                    // 参考:
                    // [1] http://www.unicode.org
                    // [2] http://zh.wikipedia.org/wiki/Unicode%E5%AD%97%E7%AC%A6%E5%88%97%E8%A1%A8
                    // [3] http://xylonwang.iteye.com/blog/519552
                    isChinese = (code > 0x3400 && code < 0x9FC3) || (code > 0xF900 && code < 0xFA6A);

                    if (!isChinese) {
                        result += letter;
                        continue;
                    }

                    index = src.indexOf(letter);

                    if (index !== -1) {
                        result += des.charAt(index);
                    } else {
                        result += letter;
                    }
                }

                return result;
            }

            /**
             * 转换HTML Element属性
             * @param {Element} element - 待转换的HTML Element节点
             * @param {String|Array} attr - 待转换的属性/属性列表
             * @param {Boolean} toT - 是否转换成繁体
             */
            function tranAttr(element, attr, toT) {
                var i, attrValue;

                if (attr instanceof Array) {
                    for(i = 0; i < attr.length; i++) {
                        tranAttr(element, attr[i], toT);
                    }
                } else {
                    attrValue = element.getAttribute(attr);

                    if (attrValue !== "" && attrValue !== null) {
                        element.setAttribute(attr, tranStr(attrValue, toT));
                    }
                }
            }

            /**
             * 转换HTML Element节点
             * @param {Element} element - 待转换的HTML Element节点
             * @param {Boolean} toT - 是否转换成繁体
             */
            function tranElement(element, toT) {
                var i;
                var childNodes;

                if (element.nodeType !== 1) {
                    return;
                }

                childNodes = element.childNodes;

                for (i = 0; i < childNodes.length; i++) {
                    var childNode = childNodes.item(i);

                    // 若为HTML Element节点
                    if (childNode.nodeType === 1) {
                        // 对以下标签不做处理
                        if ("|BR|HR|TEXTAREA|SCRIPT|OBJECT|EMBED|".indexOf("|" + childNode.tagName + "|") !== -1) {
                            continue;
                        }
                        
                        tranAttr(childNode, ['title', 'data-original-title', 'alt', 'placeholder'], toT);

                        // input 标签
                        // 对text类型的input输入框不做处理
                        if (childNode.tagName === "INPUT"
                            && childNode.value !== ""
                            && childNode.type !== "text"
                            && childNode.type !== "hidden")
                        {
                            childNode.value = tranStr(childNode.value, toT);
                        }

                        // 继续递归调用
                        tranElement(childNode, toT);
                    } else if (childNode.nodeType === 3) {  // 若为文本节点
                        childNode.data = tranStr(childNode.data, toT);
                    }
                }
            }

            // 扩展jQuery全局方法
            $.extend({
                /**
                 * 文本简转繁
                 * @param {String} str - 待转换的文本
                 * @returns {String} 转换结果
                 */
                s2t: function(str) {
                    return tranStr(str, true);
                },

                /**
                 * 文本繁转简
                 * @param {String} str - 待转换的文本
                 * @returns {String} 转换结果
                 */
                t2s: function(str) {
                    return tranStr(str, false);
                }
            });

            // 扩展jQuery对象方法
            $.fn.extend({
                /**
                 * jQuery Objects简转繁
                 * @this {jQuery Objects} 待转换的jQuery Objects
                 */
                s2t: function() {
                    return this.each(function() {
                        tranElement(this, true);
                    });
                },

                /**
                 * jQuery Objects繁转简
                 * @this {jQuery Objects} 待转换的jQuery Objects
                 */
                t2s: function() {
                    return this.each(function() {
                        tranElement(this, false);
                    });
                }
            });
        }) (jQuery);

    //---------------------------------------------------
    //分頁列
    //---------------------------------------------------

        function pages(cid,numrow,psize,pnos,pinx,sinx,einx,list_size,url_args){
        //---------------------------------------------------
        //分頁列
        //---------------------------------------------------
        //參數
        //---------------------------------------------------
        //cid           容器id
        //numrow        資料總筆數
        //psize         單頁筆數
        //pnos          分頁筆數
        //pinx          目前所在頁
        //sinx          目前所在頁,值域起始值
        //einx          目前所在頁,值域終止值
        //list_size     分頁列顯示筆數
        //url_args      連結資訊
        //---------------------------------------------------
        //回傳值
        //---------------------------------------------------
        //本函式會傳回容器物件,你可以透過 容器物件.tbl 取得
        //分頁列表格 物件.
        //---------------------------------------------------

            //分頁列區段
            var arry_list=[];   //分頁列資料陣列
            var s_sinx   =0;    //分頁列區段,值域起始值
            var s_einx   =0;    //分頁列區段,值域終止值
            var s_sinx   =(get_seinx()).s_sinx;
            var s_einx   =(get_seinx()).s_einx;

            //連結資訊
            var pinx_name =url_args.pinx_name;
            var psize_name=url_args.psize_name;
            var page_name =url_args.page_name;
            var page_args =parse_page_args(url_args.page_args);

            //容器
            var opage=document.getElementById(cid);
            opage.className="page_container";

            //表格
            var otbl =document.createElement("TABLE");
            otbl.className="page_tbl";

            //列
            var otr  =otbl.insertRow(-1);
            otr.className="page_tr";

            //資訊欄位
            var otd_info=otr.insertCell(-1);
            otd_info.className="page_info";
            //otd_info.innerHTML="第"+sinx+"筆~第"+einx+"筆"+":"+"共"+numrow+"筆";
            otd_info.innerHTML="共"+pnos+"頁";

            //第一頁
            if(s_sinx!=1){
                var otd_first=otr.insertCell(-1);
                otd_first.className="page_first";
                otd_first.innerHTML="第一頁";
                otd_first.cls="page_first";

                var _pinx =1;
                var _psize=psize;
                var _url  ="";

                if(page_args!=""){
                    _url+=page_name +"?"
                    _url+=pinx_name +"="+_pinx+"&"
                    _url+=psize_name+"="+_psize+"&"
                    _url+=page_args
                }else{
                    _url+=page_name +"?"
                    _url+=pinx_name +"="+_pinx+"&"
                    _url+=psize_name+"="+_psize
                }

                otd_first._pinx =_pinx;
                otd_first._psize=_psize;
                otd_first._url  =_url;

                otd_first.onmouseover=function(){
                    this.className="page_hover";
                    this.style.cursor="pointer";
                }
                otd_first.onmouseout=function(){
                    this.className=this.cls;
                    this.style.cursor="";
                }
                otd_first.onclick=function(){
                    var _pinx =this._pinx ;
                    var _psize=this._psize;
                    var _url  =this._url  ;
                    self.location.href=_url;
                }
            }
            //上一頁
            if(s_sinx>1){
                var otd_prev=otr.insertCell(-1);
                otd_prev.className="page_prev";
                otd_prev.innerHTML="<<";
                otd_prev.cls="page_prev";

                var _pinx =s_sinx-1;
                var _psize=psize;
                var _url  ="";

                if(page_args!=""){
                    _url+=page_name +"?"
                    _url+=pinx_name +"="+_pinx+"&"
                    _url+=psize_name+"="+_psize+"&"
                    _url+=page_args
                }else{
                    _url+=page_name +"?"
                    _url+=pinx_name +"="+_pinx+"&"
                    _url+=psize_name+"="+_psize
                }

                otd_prev._pinx =_pinx;
                otd_prev._psize=_psize;
                otd_prev._url  =_url;

                otd_prev.onmouseover=function(){
                    this.className="page_hover";
                    this.style.cursor="pointer";
                }
                otd_prev.onmouseout=function(){
                    this.className=this.cls;
                    this.style.cursor="";
                }
                otd_prev.onclick=function(){
                    var _pinx =this._pinx ;
                    var _psize=this._psize;
                    var _url  =this._url  ;
                    self.location.href=_url;
                }
            }
            //一般|現在
            for(;s_sinx<=s_einx;s_sinx++){
                if(pinx==s_sinx){
                //現在
                    var otd_current=otr.insertCell(-1);
                    otd_current.className="page_current";
                    otd_current.innerHTML=s_sinx;
                    otd_current.cls="page_current";

                    var _pinx =s_sinx;
                    var _psize=psize;
                    var _url  ="";

                    if(page_args!=""){
                        _url+=page_name +"?"
                        _url+=pinx_name +"="+_pinx+"&"
                        _url+=psize_name+"="+_psize+"&"
                        _url+=page_args
                    }else{
                        _url+=page_name +"?"
                        _url+=pinx_name +"="+_pinx+"&"
                        _url+=psize_name+"="+_psize
                    }

                    otd_current._pinx =_pinx;
                    otd_current._psize=_psize;
                    otd_current._url  =_url;

                    otd_current.onmouseover=function(){
                        this.style.cursor="pointer";
                    }
                    otd_current.onmouseout=function(){
                        this.className=this.cls;
                        this.style.cursor="";
                    }
                    otd_current.onclick=function(){
                        var _pinx =this._pinx ;
                        var _psize=this._psize;
                        var _url  =this._url  ;
                        self.location.href=_url;
                    }
                }else{
                //一般
                    var otd_normal=otr.insertCell(-1);
                    otd_normal.className="page_normal";
                    otd_normal.innerHTML=s_sinx;
                    otd_normal.cls="page_normal";

                    var _pinx =s_sinx;
                    var _psize=psize;
                    var _url  ="";

                    if(page_args!=""){
                        _url+=page_name +"?"
                        _url+=pinx_name +"="+_pinx+"&"
                        _url+=psize_name+"="+_psize+"&"
                        _url+=page_args
                    }else{
                        _url+=page_name +"?"
                        _url+=pinx_name +"="+_pinx+"&"
                        _url+=psize_name+"="+_psize
                    }

                    otd_normal._pinx =_pinx;
                    otd_normal._psize=_psize;
                    otd_normal._url  =_url;

                    otd_normal.onmouseover=function(){
                        this.className="page_hover";
                        this.style.cursor="pointer";
                    }
                    otd_normal.onmouseout=function(){
                        this.className=this.cls;
                        this.style.cursor="";
                    }
                    otd_normal.onclick=function(){
                        var _pinx =this._pinx ;
                        var _psize=this._psize;
                        var _url  =this._url  ;
                        self.location.href=_url;
                    }
                }
            }
            //下一頁
            if(s_einx<pnos){
                var otd_next=otr.insertCell(-1);
                otd_next.className="page_next";
                otd_next.innerHTML=">>";
                otd_next.cls="page_next";

                var _pinx =s_einx+1;
                var _psize=psize;
                var _url  ="";

                if(page_args!=""){
                    _url+=page_name +"?"
                    _url+=pinx_name +"="+_pinx+"&"
                    _url+=psize_name+"="+_psize+"&"
                    _url+=page_args
                }else{
                    _url+=page_name +"?"
                    _url+=pinx_name +"="+_pinx+"&"
                    _url+=psize_name+"="+_psize
                }

                otd_next._pinx =_pinx;
                otd_next._psize=_psize;
                otd_next._url  =_url;

                otd_next.onmouseover=function(){
                    this.className="page_hover";
                    this.style.cursor="pointer";
                }
                otd_next.onmouseout=function(){
                    this.className=this.cls;
                    this.style.cursor="";
                }
                otd_next.onclick=function(){
                    var _pinx =this._pinx ;
                    var _psize=this._psize;
                    var _url  =this._url  ;
                    self.location.href=_url;
                }
            }
            //最末頁
            if(s_einx<pnos){
                var otd_last=otr.insertCell(-1);
                otd_last.className="page_last";
                otd_last.innerHTML="最末頁";
                otd_last.cls="page_last";

                var _pinx =pnos;
                var _psize=psize;
                var _url  ="";

                if(page_args!=""){
                    _url+=page_name +"?"
                    _url+=pinx_name +"="+_pinx+"&"
                    _url+=psize_name+"="+_psize+"&"
                    _url+=page_args
                }else{
                    _url+=page_name +"?"
                    _url+=pinx_name +"="+_pinx+"&"
                    _url+=psize_name+"="+_psize
                }

                otd_last._pinx =_pinx;
                otd_last._psize=_psize;
                otd_last._url  =_url;

                otd_last.onmouseover=function(){
                    this.className="page_hover";
                    this.style.cursor="pointer";
                }
                otd_last.onmouseout=function(){
                    this.className=this.cls;
                    this.style.cursor="";
                }
                otd_last.onclick=function(){
                    var _pinx =this._pinx ;
                    var _psize=this._psize;
                    var _url  =this._url  ;
                    self.location.href=_url;
                }
            }

            opage.appendChild(otbl);
            opage.tbl=otbl;

            return opage;

            function get_seinx(){
            //-----------------------------------------------
            //分頁列區段,值域起始值,值域終止值
            //-----------------------------------------------

                arry_list=array_range(1,pnos);
                arry_list=array_chunk(arry_list,list_size);

                for(var i=0;i<arry_list.length;i++){
                    if(in_array(pinx,arry_list[i])){
                        var list=arry_list[i];
                        s_sinx=list[0];
                        s_einx=list[list.length-1];
                        break;
                    }
                }

                return {
                    's_sinx':s_sinx,
                    's_einx':s_einx
                };
            }

            function array_range(s,e,step){
            //-----------------------------------------------
            //值域數值陣列
            //-----------------------------------------------
            //s     起始值
            //e     終止值
            //step  遞增數,預設1,可以指定負整數
            //-----------------------------------------------

                if(!step){
                    step=1;
                }else{
                    step=parseInt(step);
                }

                var arry=[];
                while(s<=e){
                    arry.push(s);
                    s=s+step;
                }

                return arry;
            }

            function array_chunk(arry,size){
            //-----------------------------------------------
            //依長度分割陣列
            //-----------------------------------------------
            //arry  陣列
            //size  長度,預設1
            //-----------------------------------------------

                //參數檢驗
                if(!arry){
                    return [];
                }
                if(!size){
                    size=1;
                }else{
                    size=parseInt(size);
                }

                //處理
                var len     =arry.length;
                var pnos    =Math.ceil(len/size);
                var results =[];

                var inx=0;
                for(var i=1;i<=pnos;i++){
                    var result=[];
                    for(var j=0;j<size;j++){
                        var val=arry[inx];
                        if(val){
                            result[j]=val;
                        }
                        inx++;
                    }
                    //alert(result);
                    results.push(result);
                }

                //alert(results.length);

                //回傳
                return results;
            }

            function in_array(val,array){
            //-----------------------------------------------
            //檢驗元素是否在陣列裡
            //-----------------------------------------------
            //val   值
            //array 陣列
            //-----------------------------------------------

                flag=false;
                for(var i=0;i<array.length;i++){
                    if(val==array[i]){
                       flag=true;
                       break;
                    }
                }

                //回傳
                return flag;
            }

            function parse_page_args(arry){
            //-----------------------------------------------
            //處理額外參數
            //-----------------------------------------------

                var tmp=[];
                for(var key in arry){
                    var val=trim(arry[key]);
                    tmp.push(key+'='+encodeURI(val));
                }

                return tmp.join('&');

                function trim(str){
                //去除字串前後空白

                    str=str.toString();
                    str=str.replace(/^\s+/,'');
                    str=str.replace(/\s+$/,'');
                    return str;
                }
            }
        }

    //---------------------------------------------------
    //查詢表單列
    //---------------------------------------------------

        function qform(id,configs){
        //---------------------------------------------------
        //查詢表單列
        //---------------------------------------------------
        //id        容器id
        //configs   查詢總類設定
        //---------------------------------------------------
        //回傳值
        //---------------------------------------------------
        //本函式會傳回容器物件,你可以透過下列屬性,取得各個組成
        //元件.
        //
        //容器物件._createElement   _createElement()
        //容器物件.qform_form       o_qform_form
        //容器物件.qform_tbl        o_qform_tbl
        //容器物件.qform_tbl_ltd    o_qform_tbl_ltd
        //容器物件.qform_tbl_mtd    o_qform_tbl_mtd
        //容器物件.qform_tbl_rtd    o_qform_tbl_rtd
        //容器物件.qform_type       o_qform_type
        //容器物件.qform_sbtn       o_qform_sbtn
        //容器物件.qform_abtn       o_qform_abtn
        //容器物件.qform_rbtn       o_qform_rbtn
        //---------------------------------------------------

            //容器
            var o_qform =document.getElementById(id);
            var qform_id=o_qform.id;
            o_qform.className="qform_container";

            //表單
            var o_qform_form=document.createElement("FORM");
            o_qform_form.className="qform_form";
            o_qform_form.id=qform_id+"_form";

            //表格
            var o_qform_tbl =document.createElement("TABLE");
            o_qform_tbl.className="qform_tbl";
            o_qform_tbl.id=qform_id+"_tbl";

            //列
            var otr=o_qform_tbl.insertRow(-1);

            //欄位
            var o_qform_tbl_ltd=otr.insertCell(-1);
            o_qform_tbl_ltd.className="qform_tbl_ltd";
            o_qform_tbl_ltd.id=qform_id+"_tbl_ltd";

            var o_qform_tbl_mtd=otr.insertCell(-1);
            o_qform_tbl_mtd.className="qform_tbl_mtd";
            o_qform_tbl_mtd.id=qform_id+"_tbl_mtd";

            var o_qform_tbl_rtd=otr.insertCell(-1);
            o_qform_tbl_rtd.className="qform_tbl_rtd";
            o_qform_tbl_rtd.id=qform_id+"_tbl_rtd";

            //總類,下拉
            var o_qform_type=document.createElement("SELECT");
            o_qform_type.className="qform_type";
            o_qform_type.id=qform_id+"_type";
            for(var type in configs){
                var val=type;
                var txt=configs[type]['text'];
                var o_opt=document.createElement("OPTION");
                o_opt.value=val;
                o_opt.text =txt;
                o_qform_type.options.add(o_opt);
            }
            o_qform_tbl_ltd.appendChild(o_qform_type);

            //按鈕,查詢
            var o_qform_sbtn=document.createElement("INPUT");
            o_qform_sbtn.className="qform_sbtn";
            o_qform_sbtn.id   =qform_id+"_sbtn";
            o_qform_sbtn.type ="button";
            o_qform_sbtn.value="查詢";
            o_qform_tbl_rtd.appendChild(o_qform_sbtn);

            //按鈕,不分
            var o_qform_abtn=document.createElement("INPUT");
            o_qform_abtn.className="qform_abtn";
            o_qform_abtn.id   =qform_id+"_abtn";
            o_qform_abtn.type ="button";
            o_qform_abtn.value="不分";
            o_qform_tbl_rtd.appendChild(o_qform_abtn);

            //按鈕,重設
            var o_qform_rbtn=document.createElement("INPUT");
            o_qform_rbtn.className="qform_abtn";
            o_qform_rbtn.id   =qform_id+"_abtn";
            o_qform_rbtn.type ="button";
            o_qform_rbtn.value="重設";
            o_qform_tbl_rtd.appendChild(o_qform_rbtn);

            //附加表格到表單
            o_qform_form.appendChild(o_qform_tbl);

            //附加表單到容器
            o_qform.appendChild(o_qform_form);

            //初始化
            _createElement(o_qform_tbl_mtd,key=o_qform_type.options[0].value);

            //總類,下拉,onchange事件
            o_qform_type.onchange=function(){
                var opt=this.options[this.selectedIndex];
                var txt=opt.text;
                var val=opt.value;
                o_qform_tbl_mtd.innerHTML="";
                _createElement(o_qform_tbl_mtd,key=val);
            }

            //回傳
            o_qform._createElement=_createElement;
            o_qform.qform_form   =o_qform_form;
            o_qform.qform_tbl    =o_qform_tbl;
            o_qform.qform_tbl_ltd=o_qform_tbl_ltd;
            o_qform.qform_tbl_mtd=o_qform_tbl_mtd;
            o_qform.qform_tbl_rtd=o_qform_tbl_rtd;
            o_qform.qform_type   =o_qform_type;
            o_qform.qform_sbtn   =o_qform_sbtn;
            o_qform.qform_abtn   =o_qform_abtn;
            o_qform.qform_rbtn   =o_qform_rbtn;
            return o_qform;

            //-----------------------------------------------
            //子函式
            //-----------------------------------------------
            function _createElement(obj,key){

                var config=configs[key];

                switch(config['type'].toLowerCase()){
                    case 'text':
                        obj.appendChild(_text(config));
                        break;
                    case 'select':
                        obj.appendChild(_select(config));
                        break;
                    case 'radio':
                        obj.appendChild(_radio(config));
                        break;
                    case 'checkbox':
                        obj.appendChild(_checkbox(config));
                        break;
                    case 'city_region':
                        //obj.appendChild(_city_region(config));
                        _city_region(oparent=obj,config);
                        break;
                }

                //_text()
                function _text(config){
                    var type     =config['type']
                    var id       =config['id']
                    var name     =config['name']
                    var vals     =config['vals']
                    var className=config['className']

                    var otxt=document.createElement("INPUT");

                    otxt.type     ="TEXT";
                    otxt.id       =id;
                    otxt.name     =name;
                    otxt.value    =vals;
                    otxt.className=className;

                    return otxt;
                }

                //_select()
                function _select(config){
                    var type     =config['type']
                    var id       =config['id']
                    var name     =config['name']
                    var vals     =config['vals']
                    var className=config['className']

                    var osel=document.createElement('SELECT');
                    osel.id       =id;
                    osel.name     =name;
                    osel.className=className;

                    for(var val in vals){
                        var txt=vals[val];

                        var opt=document.createElement("option");
                        opt.value=val;
                        opt.text =txt;
                        osel.options.add(opt);
                    }

                    return osel;
                }

                //_radio()
                function _radio(config){
                    var type     =config['type']
                    var name     =config['name']
                    var vals     =config['vals']
                    var className=config['className']

                    var ocon=document.createElement("DIV");

                    for(var val in vals){
                        var txt=vals[val];

                        var ord =_rd(name);
                        ord.name=name;
                        ord.type=type;
                        ord.value=val;
                        ord.className=className;
                        var otxt=document.createTextNode(txt);

                        ocon.appendChild(ord);
                        ocon.appendChild(otxt);
                    }

                    return ocon;

                    function _rd(name){
                        try{
                        //FOR IE
                            return document.createElement('<input name='+name+'>');
                        }catch(e){
                            return document.createElement('input');
                        }
                    }
                }

                //_checkbox()
                function _checkbox(config){
                    var type     =config['type']
                    var name     =config['name']
                    var vals     =config['vals']
                    var className=config['className']

                    var ocon=document.createElement("DIV");

                    for(var val in vals){
                        var txt=vals[val];

                        var och =_checkbox(name);
                        och.name=name;
                        och.type=type;
                        och.value=val;
                        och.className=className;
                        var otxt=document.createTextNode(txt);

                        ocon.appendChild(och);
                        ocon.appendChild(otxt);
                    }

                    return ocon;

                    function _checkbox(name){
                        try{
                        //FOR IE
                            return document.createElement('<input name='+name+'>');
                        }catch(e){
                            return document.createElement('input');
                        }
                    }
                }

                //_city_region()
                function _city_region(oparent,config){
                    var type     =config['type']
                    var name     =config['name']
                    var vals     =config['vals']
                    var use_type =config['use_type']
                    var className=config['className']

                    var ocon=document.createElement("DIV");

                    //city
                    var ocity=document.createElement('SELECT');
                    var city_val=vals['city_val'];
                    ocity.id       =name['city_name'];
                    ocity.name     =name['city_name'];
                    ocity.className=className;

                    //region
                    var oregion=document.createElement('SELECT');
                    var region_val=vals['region_val'];
                    oregion.id       =name['region_name'];
                    oregion.name     =name['region_name'];
                    oregion.className=className;

                    //appendChild
                    ocon.appendChild(ocity);
                    ocon.appendChild(oregion);
                    ocon.ocity  =ocity;
                    ocon.oregion=oregion;
                    oparent.appendChild(ocon);

                    //binding
                    city_region_sel(ocity.id,oregion.id,city_val,region_val,use_type);
                    return ocon;
                }
            }

            function city_region_sel(city_id,region_id,city_val,region_val,use_type){
            //-----------------------------------------------
            //縣市鄉鎮下拉
            //-----------------------------------------------
            //city_id       縣市id
            //region_id     鄉鎮id
            //city_val      縣市值,預設 '請選擇'
            //region_val    鄉鎮值,預設 '請選擇'
            //use_type      用途: form|query,預設 'form'
            //              form  用在一般表單裡,即無'請選擇'選項
            //              query 用在查詢表單裡,即有'請選擇'選項
            //-----------------------------------------------

                //縣市鄉鎮陣列
                array_city={
                    '請選擇':{
                                '請選擇':'0'
                            },
                    '基隆市':{
                                '請選擇':'0',
                                '仁愛區':'200',
                                '信義區':'201',
                                '中正區':'202',
                                '中山區':'203',
                                '安樂區':'204',
                                '暖暖區':'205',
                                '七堵區':'206'
                            },
                    '台北市':{
                                '請選擇':'0',
                                '中正區':'100',
                                '大同區':'103',
                                '中山區':'104',
                                '松山區':'105',
                                '大安區':'106',
                                '萬華區':'108',
                                '信義區':'110',
                                '士林區':'111',
                                '北投區':'112',
                                '內湖區':'114',
                                '南港區':'115',
                                '文山區':'116'
                            },
                    '新北市':{
                                '請選擇':'0',
                                '萬里區':'207',
                                '金山區':'208',
                                '板橋區':'220',
                                '汐止區':'221',
                                '深坑區':'222',
                                '石碇區':'223',
                                '瑞芳區':'224',
                                '平溪區':'226',
                                '雙溪區':'227',
                                '貢寮區':'228',
                                '新店區':'231',
                                '坪林區':'232',
                                '烏來區':'233',
                                '永和區':'234',
                                '中和區':'235',
                                '土城區':'236',
                                '三峽區':'237',
                                '樹林區':'238',
                                '鶯歌區':'239',
                                '三重區':'241',
                                '新莊區':'242',
                                '泰山區':'243',
                                '林口區':'244',
                                '蘆洲區':'247',
                                '五股區':'248',
                                '八里區':'249',
                                '淡水區':'251',
                                '三芝區':'252',
                                '石門區':'253'
                            },
                    '桃園縣':{
                                '請選擇':'0',
                                '中壢市':'320',
                                '平鎮市':'324',
                                '龍潭鄉':'325',
                                '楊梅市':'326',
                                '新屋鄉':'327',
                                '觀音鄉':'328',
                                '桃園市':'330',
                                '龜山鄉':'333',
                                '八德市':'334',
                                '大溪鎮':'335',
                                '復興鄉':'336',
                                '大園鄉':'337',
                                '蘆竹鄉':'338'
                            },
                    '新竹市':{
                                '請選擇':'0',
                                '東區':'200',
                                '北區':'200',
                                '香山區':'200'
                            },
                    '新竹縣':{
                                '請選擇':'0',
                                '竹北市':'302',
                                '湖口鄉':'303',
                                '新豐鄉':'304',
                                '新埔鎮':'305',
                                '關西鎮':'306',
                                '芎林鄉':'307',
                                '寶山鄉':'308',
                                '竹東鎮':'310',
                                '五峰鄉':'311',
                                '橫山鄉':'312',
                                '尖石鄉':'313',
                                '北埔鄉':'314',
                                '峨眉鄉':'315'
                            },
                    '苗栗縣':{
                                '請選擇':'0',
                                '竹南鎮':'350',
                                '頭份鎮':'351',
                                '三灣鄉':'352',
                                '南庄鄉':'353',
                                '獅潭鄉':'354',
                                '後龍鎮':'356',
                                '通霄鎮':'357',
                                '苑裡鎮':'358',
                                '苗栗市':'360',
                                '造橋鄉':'361',
                                '頭屋鄉':'362',
                                '公館鄉':'363',
                                '大湖鄉':'364',
                                '泰安鄉':'365',
                                '銅鑼鄉':'366',
                                '三義鄉':'367',
                                '西湖鄉':'368',
                                '卓蘭鎮':'369'
                            },
                    '台中市':{
                                '請選擇':'0',
                                '中區':'400',
                                '東區':'401',
                                '南區':'402',
                                '西區':'403',
                                '北區':'404',
                                '北屯區':'406',
                                '西屯區':'407',
                                '南屯區':'408',
                                '太平區':'411',
                                '大里區':'412',
                                '霧峰區':'413',
                                '烏日區':'414',
                                '豐原區':'420',
                                '后里區':'421',
                                '石岡區':'422',
                                '東勢區':'423',
                                '和平區':'424',
                                '新社區':'426',
                                '潭子區':'427',
                                '大雅區':'428',
                                '神岡區':'429',
                                '大肚區':'432',
                                '沙鹿區':'433',
                                '龍井區':'434',
                                '梧棲區':'435',
                                '清水區':'436',
                                '大甲區':'437',
                                '外埔區':'438',
                                '大安區':'439'
                            },
                    '彰化縣':{
                                '請選擇':'0',
                                '彰化市':'500',
                                '芬園鄉':'502',
                                '花壇鄉':'503',
                                '秀水鄉':'504',
                                '鹿港鎮':'505',
                                '福興鄉':'506',
                                '線西鄉':'507',
                                '和美鎮':'508',
                                '伸港鄉':'509',
                                '員林鎮':'510',
                                '社頭鄉':'511',
                                '永靖鄉':'512',
                                '埔心鄉':'513',
                                '溪湖鎮':'514',
                                '大村鄉':'515',
                                '埔鹽鄉':'516',
                                '田中鎮':'520',
                                '北斗鎮':'521',
                                '田尾鄉':'522',
                                '埤頭鄉':'523',
                                '溪州鄉':'524',
                                '竹塘鄉':'525',
                                '二林鎮':'526',
                                '大城鄉':'527',
                                '芳苑鄉':'528',
                                '二水鄉':'530'
                            },
                    '南投縣':{
                                '請選擇':'0',
                                '南投市':'540',
                                '中寮鄉':'541',
                                '草屯鎮':'542',
                                '國姓鄉':'544',
                                '埔里鎮':'545',
                                '仁愛鄉':'546',
                                '名間鄉':'551',
                                '集集鎮':'552',
                                '水里鄉':'553',
                                '魚池鄉':'555',
                                '信義鄉':'556',
                                '竹山鎮':'557',
                                '鹿谷鄉':'558'
                            },

                    '雲林縣':{
                                '請選擇':'0',
                                '斗南鎮':'630',
                                '大埤鄉':'631',
                                '虎尾鎮':'632',
                                '土庫鎮':'633',
                                '褒忠鄉':'634',
                                '東勢鄉':'635',
                                '台西鄉':'636',
                                '崙背鄉':'637',
                                '麥寮鄉':'638',
                                '斗六市':'640',
                                '林內鄉':'643',
                                '古坑鄉':'646',
                                '莿桐鄉':'647',
                                '西螺鎮':'648',
                                '二崙鄉':'649',
                                '北港鎮':'651',
                                '水林鄉':'652',
                                '口湖鄉':'653',
                                '四湖鄉':'654',
                                '元長鄉':'655'
                            },
                    '嘉義市':{
                                '請選擇':'0',
                                '東區':'600',
                                '西區':'600'
                            },
                    '嘉義縣':{
                                '請選擇':'0',
                                '番路鄉':'602',
                                '梅山鄉':'603',
                                '竹崎鄉':'604',
                                '阿里山':'605',
                                '中埔鄉':'606',
                                '大埔鄉':'607',
                                '水上鄉':'608',
                                '鹿草鄉':'611',
                                '太保市':'612',
                                '朴子市':'613',
                                '東石鄉':'614',
                                '六腳鄉':'615',
                                '新港鄉':'616',
                                '民雄鄉':'621',
                                '大林鎮':'622',
                                '溪口鄉':'623',
                                '義竹鄉':'624',
                                '布袋鎮':'625'
                            },
                    '台南市':{
                                '請選擇':'0',
                                '中西區':'700',
                                '東區':'701',
                                '南區':'702',
                                '北區':'704',
                                '安平區':'708',
                                '安南區':'709',
                                '永康區':'710',
                                '歸仁區':'711',
                                '新化區':'712',
                                '左鎮區':'713',
                                '玉井區':'714',
                                '楠西區':'715',
                                '南化區':'716',
                                '仁德區':'717',
                                '關廟區':'718',
                                '龍崎區':'719',
                                '官田區':'720',
                                '麻豆區':'721',
                                '佳里區':'722',
                                '西港區':'723',
                                '七股區':'724',
                                '將軍區':'725',
                                '學甲區':'726',
                                '北門區':'727',
                                '新營區':'730',
                                '後壁區':'731',
                                '白河區':'732',
                                '東山區':'733',
                                '六甲區':'734',
                                '下營區':'735',
                                '柳營區':'736',
                                '鹽水區':'737',
                                '善化區':'741',
                                '大內區':'742',
                                '山上區':'743',
                                '新市區':'744',
                                '安定區':'745'
                            },
                    '高雄市':{
                                '請選擇':'0',
                                '新興區':'800',
                                '前金區':'801',
                                '苓雅區':'802',
                                '鹽埕區':'803',
                                '鼓山區':'804',
                                '旗津區':'805',
                                '前鎮區':'806',
                                '三民區':'807',
                                '楠梓區':'811',
                                '小港區':'812',
                                '左營區':'813',
                                '仁武區':'814',
                                '大社區':'815',
                                '岡山區':'820',
                                '路竹區':'821',
                                '阿蓮區':'822',
                                '田寮區':'823',
                                '燕巢區':'824',
                                '橋頭區':'825',
                                '梓官區':'826',
                                '彌陀區':'827',
                                '永安區':'828',
                                '湖內區':'829',
                                '鳳山區':'830',
                                '大寮區':'831',
                                '林園區':'832',
                                '鳥松區':'833',
                                '大樹區':'840',
                                '旗山區':'842',
                                '美濃區':'843',
                                '六龜區':'844',
                                '內門區':'845',
                                '杉林區':'846',
                                '甲仙區':'847',
                                '桃源區':'848',
                                '那瑪夏':'849',
                                '茂林區':'851',
                                '茄萣區':'852'
                            },
                    '屏東縣':{
                                '請選擇':'0',
                                '屏東市':'900',
                                '三地門':'901',
                                '霧台鄉':'902',
                                '瑪家鄉':'903',
                                '九如鄉':'904',
                                '里港鄉':'905',
                                '高樹鄉':'906',
                                '鹽埔鄉':'907',
                                '長治鄉':'908',
                                '麟洛鄉':'909',
                                '竹田鄉':'911',
                                '內埔鄉':'912',
                                '萬丹鄉':'913',
                                '潮州鎮':'920',
                                '泰武鄉':'921',
                                '來義鄉':'922',
                                '萬巒鄉':'923',
                                '崁頂鄉':'924',
                                '新埤鄉':'925',
                                '南州鄉':'926',
                                '林邊鄉':'927',
                                '東港鎮':'928',
                                '琉球鄉':'929',
                                '佳冬鄉':'931',
                                '新園鄉':'932',
                                '枋寮鄉':'940',
                                '枋山鄉':'941',
                                '春日鄉':'942',
                                '獅子鄉':'943',
                                '車城鄉':'944',
                                '牡丹鄉':'945',
                                '恆春鎮':'946',
                                '滿州鄉':'947'
                            },
                    '台東縣':{
                                '請選擇':'0',
                                '台東市':'950',
                                '綠島鄉':'951',
                                '蘭嶼鄉':'952',
                                '延平鄉':'953',
                                '卑南鄉':'954',
                                '鹿野鄉':'955',
                                '關山鎮':'956',
                                '海端鄉':'957',
                                '池上鄉':'958',
                                '東河鄉':'959',
                                '成功鎮':'961',
                                '長濱鄉':'962',
                                '太麻里':'963',
                                '金峰鄉':'964',
                                '大武鄉':'965',
                                '達仁鄉':'966'
                            },
                    '花蓮縣':{
                                '請選擇':'0',
                                '花蓮市':'970',
                                '新城鄉':'971',
                                '秀林鄉':'972',
                                '吉安鄉':'973',
                                '壽豐鄉':'974',
                                '鳳林鎮':'975',
                                '光復鄉':'976',
                                '豐濱鄉':'977',
                                '瑞穗鄉':'978',
                                '萬榮鄉':'979',
                                '玉里鎮':'981',
                                '卓溪鄉':'982',
                                '富里鄉':'983'
                            },
                    '宜蘭縣':{
                                '請選擇':'0',
                                '宜蘭市':'260',
                                '頭城鎮':'261',
                                '礁溪鄉':'262',
                                '壯圍鄉':'263',
                                '員山鄉':'264',
                                '羅東鎮':'265',
                                '三星鄉':'266',
                                '大同鄉':'267',
                                '五結鄉':'268',
                                '冬山鄉':'269',
                                '蘇澳鎮':'270',
                                '南澳鄉':'272'
                            },
                    '澎湖縣':{
                                '請選擇':'0',
                                '馬公市':'880',
                                '西嶼鄉':'881',
                                '望安鄉':'882',
                                '七美鄉':'883',
                                '白沙鄉':'884',
                                '湖西鄉':'885'
                            },
                    '金門縣':{
                                '請選擇':'0',
                                '金沙鎮':'890',
                                '金湖鎮':'891',
                                '金寧鄉':'892',
                                '金城鎮':'893',
                                '烈嶼鄉':'894',
                                '烏坵鄉':'896'
                            },
                    '連江縣':{
                                '請選擇':'0',
                                '南竿鄉':'209',
                                '北竿鄉':'210',
                                '莒光鄉':'211',
                                '東引鄉':'212'
                            }
                };

                //預設值
                city_def  ='桃園縣';
                region_def='桃園市';

                //參數檢驗
                if((city_id==undefined)||(trim(city_id)=='')){
                    return false;
                }
                if((region_id==undefined)||(trim(region_id)=='')){
                    return false;
                }
                if((use_type==undefined)||(trim(use_type)=='')){
                    use_type='form';
                }
                if((city_val==undefined)||(trim(city_val)==''||(trim(city_val)=='請選擇'))){
                    if(use_type.toLowerCase()=='form'){
                        city_val=city_def;
                    }else{
                        city_val='請選擇';
                    }
                }
                if((region_val==undefined)||(trim(region_val)==''||(trim(region_val)=='請選擇'))){
                    if(use_type.toLowerCase()=='form'){
                        region_val=region_def;
                    }else{
                        region_val='請選擇';
                    }
                }

                //縣市鄉鎮下拉
                var ocity   =document.getElementById(city_id);
                var oregion =document.getElementById(region_id);
                if((!ocity)||(!oregion)){
                    return false;
                }

                //縣市
                for(var key in array_city){
                    if(use_type.toLowerCase()=='form'){
                        if(key.toLowerCase()=='請選擇'){
                            continue;
                        }
                    }
                    var o_opt=document.createElement('OPTION');
                    o_opt.value=key;
                    o_opt.text =key;
                    ocity.options.add(o_opt);

                    if(key.toLowerCase()==city_val){
                       o_opt.selected=true;
                    }
                }

                //鄉鎮
                for(var key in array_city[city_val]){
                    if(use_type.toLowerCase()=='form'){
                        if(key.toLowerCase()=='請選擇'){
                            continue;
                        }
                    }

                    var o_opt=document.createElement('OPTION');
                    o_opt.value=key;
                    o_opt.text =key;
                    oregion.options.add(o_opt);

                    if(key.toLowerCase()==region_val){
                       o_opt.selected=true;
                    }
                }

                //連動處理
                ocity.setAttribute('region_id',region_id);
                ocity.setAttribute('use_type',use_type);
                ocity.onchange=function(){

                    //屬性
                    var region_id=this.getAttribute('region_id');
                    var use_type =this.getAttribute('use_type');

                    //取回鄉鎮下拉
                    var oregion=document.getElementById(region_id);

                    //取回縣市名稱
                    var city=this.value;

                    //取回對應鄉鎮區名稱
                    var regions=[];

                    for(var key in array_city[city]){
                        if(use_type.toLowerCase()=='form'){
                            if(key.toLowerCase()=='請選擇'){
                                continue;
                            }
                        }
                        var zip=array_city[city][key];
                        regions.push(key);
                    }

                    //清掉鄉鎮下拉既有選項
                    oregion.innerHTML='';

                    //回填新的項目
                    for(var i=0;i<regions.length;i++){
                        var o_opt=document.createElement('OPTION');
                        o_opt.value=regions[i];
                        o_opt.text =regions[i];
                        oregion.options.add(o_opt);
                    }
                }

                function trim(str){
                //去字串前後空白
                    str=str.toString();
                    str=str.replace(/^\s+/,'');
                    str=str.replace(/\s+$/,'');
                    return str;
                }
            }
        }

    //---------------------------------------------------
    //首頁logo
    //---------------------------------------------------

        function logo(rd,page,arg){
        //---------------------------------------------------
        //函式: logo()
        //用途: 首頁logo
        //---------------------------------------------------
        //rd    層級
        //page  首頁名稱,預設 index.php
        //arg   參數物件
        //---------------------------------------------------

            //參數檢驗
            var rd  =rd || 0;
            var page=page || 'index.php';
            var arg =arg || {};

            //層級處理
            var _rd="";
            for(var i=0;i<rd;i++){
                _rd="../"+_rd;
            }

            //網址處理
            var url ="";
            var _arg=[];
            for(var key in arg){
                var val=arg[key];
                _arg.push(key+"="+encodeURI(val));
            }
            if(_arg.length!==0){
                _arg=_arg.join("&");
                url =_rd+page+"?"+_arg;
            }else{
                url =_rd+page;
            }

            //設定
            var o_logo=document.getElementById("logo");

            if(o_logo){
                o_logo.onmouseover=function(){
                    this.style.cursor="pointer";
                }
                o_logo.onmouseout=function(){
                    this.style.cursor="";
                }
                o_logo.onclick=function(){
                    self.location.href=url;
                }
            }
        }