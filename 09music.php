<?php
require_once "09jssdk.php";
$jssdk = new JSSDK("wxad13bc38b45249dd", "aaf3c55d55895e30e0612cab597b06c5");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="{CHARSET}">
    <title>十月音乐盒</title>
    <link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css">
    <!-- 引入远程的jquery -->
    <script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>
    <style type="text/css">
      img {
       width:500px;
       height:500px;
       border-radius: 250px;
       margin-left:240px;
       margin-top:100px;
       }
      #Music
      {
          margin-top:10px;
         border-collapse:collapse;
      }
      #Music tr {
          border: 1px solid;
      }
      #Music td {
          border: 1px solid;
          border-color: green;
          text-align:center;
          font-size: 50px;
      }
      .weui-navbar {
        height: 130px;
      }
      .weui-navbar__item{
        font-size: 60px;
        color: green;
      }
    .weui-navbar__item.weui-bar__item_on {
      background-color: #7cd3f9;
    }
    </style>
    <script>
      function play(url){
        window.location.href = url;
      }
      $(document).ready(function(){
        $("tr:odd").css("backgroundColor","#cfeaf6");
        $("#hotBtn").click(function(){
          $("#hotBtn").addClass("weui-bar__item_on");
          $("#hotDiv").css("display","block");
          $("#newBtn").removeClass("weui-bar__item_on");
          $("#newDiv").css("display","none");
        })
        $("#newBtn").click(function(){
          $("#newBtn").addClass("weui-bar__item_on");
          $("#newDiv").css("display","block");
          $("#hotBtn").removeClass("weui-bar__item_on");
          $("#hotDiv").css("display","none");
        })
      });
    </script>
  </head>
  <body>
    <!-- 导航栏 -->
    <div class="weui-tab">
      <div class="weui-navbar">
          <div class="weui-navbar__item weui-bar__item_on" id="hotBtn">
              热歌榜
          </div>
          <div class="weui-navbar__item" id="newBtn">
              新歌榜
          </div>
      </div>
      <div class="weui-tab__panel">
          <div id="hotDiv">
            <!-- 图片层 -->
            <img src="http://p1.music.126.net/GhhuF6Ep5Tq9IEvLsyCN7w==/18708190348409091.jpg?param=150y150">
            <!-- 内容层 -->
            <table id="Music">
              <tr>
                <td style="width: 20%">排名</td>
                <td style="width: 80%">歌曲名</td>
              </tr>
                <?php
                include("09mysql.php");
                $musicString = getAllMusic("09hot_music");
                $musicArr = explode(",",$musicString);
                $urlIndex=0;//存放音乐url的下标
                $rankIndex=0;//存放排名的下标
                for ($i=0; $i < count($musicArr)-1; $i++) { 
                    $urlIndex=$i+2;
                    $rankIndex=$i+1;
                    $url=$musicArr[$urlIndex];
                    $rank=$musicArr[$rankIndex];
                    $name=$musicArr[$i];
                    echo '<tr onclick="play('."'$url'".')"><td>'.$rank.'</td><td>'.$name.'</td></tr>';
                    $i=$urlIndex;
                }
              ?>
            </table>
          </div>
          <div style="display:none" id="newDiv">
            <!-- 图片层 -->
            <img src="http://p1.music.126.net/N2HO5xfYEqyQ8q6oxCw8IQ==/18713687906568048.jpg?param=150y150">
            <!-- 内容层 -->
            <table id="Music">
              <tr>
                <td style="width: 20%">排名</td>
                <td style="width: 80%">歌曲名</td>
              </tr>
                <?php
                $musicString1 = getAllMusic("09new_music");
                $musicArr1 = explode(",",$musicString1);
                $urlIndex1=0;//存放音乐url的下标
                $rankIndex1=0;//存放排名的下标
                for ($i1=0; $i1 < count($musicArr1)-1; $i1++) { 
                    $urlIndex1=$i1+2;
                    $rankIndex1=$i1+1;
                    $url1=$musicArr1[$urlIndex1];
                    $rank1=$musicArr1[$rankIndex1];
                    $name1=$musicArr1[$i1];
                    echo '<tr onclick="play('."'$url1'".')"><td>'.$rank1.'</td><td>'.$name1.'</td></tr>';
                    $i1=$urlIndex1;
                }
              ?>
            </table>
          </div>
      </div>
    </div>
  </body>
</html>
