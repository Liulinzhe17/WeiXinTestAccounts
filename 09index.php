<?php
//define your token
define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    public function valid()//验证接口的方法
    {
        $echoStr = $_GET["echostr"];//从微信用户端获取一个随机字符赋予变量echostr

        //valid signature , option访问地61行的checkSignature签名验证方法，如果签名一致，输出变量echostr，完整验证配置接口的操作
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    //签名验证程序，checkSignature被18行调用。官方加密、校验流程：将token，timestamp，nonce这三个参数进行字典序排序，然后将这三个参数字符串拼接成一个字符串惊喜shal加密，开发者获得加密后的字符串可以与signature对比，表示该请求来源于微信。
    private function checkSignature()
    {
        $signature = $_GET["signature"];//从用户端获取签名赋予变量signature
        $timestamp = $_GET["timestamp"];//从用户端获取时间戳赋予变量timestamp
        $nonce = $_GET["nonce"];    //从用户端获取随机数赋予变量nonce
        $token = TOKEN;//将常量token赋予变量token
        $tmpArr = array($token, $timestamp, $nonce);//简历数组变量tmpArr
        sort($tmpArr, SORT_STRING);//新建排序
        $tmpStr = implode( $tmpArr );//字典排序
        $tmpStr = sha1( $tmpStr );//shal加密
        //tmpStr与signature值相同，返回真，否则返回假
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    //公有的responseMsg的方法，是我们回复微信的关键。以后的章节修改代码就是修改这个。
    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];//将用户端放松的数据保存到变量postStr中，由于微信端发送的都是xml，使用postStr无法解析，故使用$GLOBALS["HTTP_RAW_POST_DATA"]获取

        //extract post data如果用户端数据不为空，执行30-55否则56-58
        if (!empty($postStr)){
                $this->logger("R ".$postStr);
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);//将postStr变量进行解析并赋予变量postObj。simplexml_load_string（）函数是php中一个解析XML的函数，SimpleXMLElement为新对象的类，LIBXML_NOCDATA表示将CDATA设置为文本节点，CDATA标签中的文本XML不进行解析
                $LLZ_msgtype = trim($postObj->MsgType);//将用户的发来的消息类型赋予变量LLZ_msgtype

            //用户发送的消息类型判断
                $result = "";
                switch ($LLZ_msgtype)
                {
                    case "event":
                        $result = $this->receiveEvent($postObj);
                        break;
                    case "text":
                        $result = $this->receiveText($postObj);
                        break;
                    case "image":
                        $result = $this->receiveImage($postObj);
                        break;
                    case "voice":
                        $result = $this->receiveVoice($postObj);
                        break;
                    case "video":
                        $result = $this->receiveVideo($postObj);
                        break;
                    case "location":
                        $result = $this->receiveLocation($postObj);
                        break;
                    default:
                        $result = "unknow msg type: ".$LLZ_msgtype;
                        break;
                }
                $this->logger("T ".$result);
                echo $result;
            }else {
                echo "";
                exit;
            }
    }
    //收到事件
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                $content = "谢谢你这么好看还关注我！09组";
                break;
            case "unsubscribe":
                $content = "取消关注";
                break;
            case "CLICK":
                $key = $object->EventKey;
                include("09mysql.php");
                if (strpos($key,"music")!==false) {
                    $musicString="";
                    if (strpos($key,"5")!==false) {
                        $musicString = getMusicInfo("09hot_music",5);
                    }
                    if (strpos($key,"4")!==false) {
                        $musicString = getMusicInfo("09hot_music",4);
                    }
                    if (strpos($key,"3")!==false) {
                        $musicString = getMusicInfo("09hot_music",3);
                    }
                    if (strpos($key,"2")!==false) {
                        $musicString = getMusicInfo("09hot_music",2);
                    }
                    if (strpos($key,"1")!==false) {
                        $musicString = getMusicInfo("09hot_music",1);
                        $musicArr = explode(",",$musicString);
                        $tpl = array("Title"=>"今日推荐",
                        "Description"=>$musicArr[0],
                        "MusicUrl"=>$musicArr[1],
                        "HQMusicUrl"=>$musicArr[1]);
                        return $result = $this->transmitMusic($object, $tpl);
                    }
                    $musicArr = explode(",",$musicString);
                    $content = array();
                    $j=0;//存放音乐url的下标
                    //回复多图文消息
                    for ($i=0; $i < count($musicArr)-1; $i++) { 
                    	$j=$i+1;
                    	$url=$musicArr[$j];
						$content[] = array("Title"=>$musicArr[$i], "Description"=>"", "PicUrl"=>getPicUrl(rand(1,9)), "Url" =>$url);
						$i=$j;
                    }
		            return $result = $this->transmitNews($object, $content);
                }elseif ($key == "comment") {
                    $content = getComment("09hot_comment");
                }
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        $content = "很抱歉，我还在成长中，暂时不能处理：".$keyword;
        $result = $this->transmitText($object, $content);

        return $result;
    }

    private function receiveImage($object)
    {
        $content = "请随意使用菜单栏上的功能😄";
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function receiveVoice($object)
    {
        $content = "请随意使用菜单栏上的功能😄";
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function receiveVideo($object)
    {
        $content = "请随意使用菜单栏上的功能😄";
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function receiveLocation($object)
    {
        $content = "请随意使用菜单栏上的功能😄";
        $result = $this->transmitText($object, $content);
        return $result;
    }

    /*
     * 向用户回复文本消息
     */
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                    </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    
    /*
     * 向用户回复图片消息
     */
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 向用户回复语音消息
     */
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[voice]]></MsgType>
                        $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 向用户回复视频消息
     */
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['Title'], $videoArray['Description']);

        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[video]]></MsgType>
                        $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 向用户回复图文消息
     */
    private function transmitNews($object, $arr_item)
    {
        if(!is_array($arr_item))
            return;

        $itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>
                ";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <Content><![CDATA[]]></Content>
                        <ArticleCount>%s</ArticleCount>
                        <Articles>
                        $item_str</Articles>
                    </xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item));
        return $result;
    }
    
    /*
     * 向用户回复音乐消息
     */
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                    </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[music]]></MsgType>
                        $item_str
                    </xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    /*
     * 向用户回复地理位置消息
     */
    private function transmitLocation($object, $locationArray){
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                    </xml>";

        $content = "地理位置："."\n经度：".$locationArray['Location_Y']."\n纬度：".$locationArray['Location_X']."\n地点：".$locationArray['Label']."\n缩放大小：".$locationArray['Scale'];
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    //日志
    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 10000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }
 }

?>



