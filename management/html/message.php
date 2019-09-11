<?php
session_start();
require_once('../ini.php');
if(empty($_SESSION['login'])){
    die;
}

?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/pic.css">
    <link rel="stylesheet" type="text/css" href="../css/message.css">
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/icono.min.css">
    <script src="../js/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/houtai.css">
</head>

<body>
    <div id="header">
        <p class="title">随行后台管理系统</p>

        <?php
        $leave_obj = new leaveDisplay;
        $leave_message = $leave_obj->getLeavMessage();

        messageHtml::leaveDisplay_promt($leave_message);
        ?>

        <a href="manage.html">
            <img class="exit" src="../img/tui.png">
        </a>
    </div>
    <div class="content1">

        <div class="left">

            <div class="profile">
                <div class="top">
                    <img class="pic" src="../img/name.png">
                    <p class="name">Alice</p>
                </div>
            </div>
            <div class="main1">
                <ul>
                    <li class="nav">
                        <a class="nav-0" href="javascript:;">
                            <img class="icon" src="../img/e-1.png">
                            <p class="write">控制台</p>
                        </a>
                    </li>
                    <li class="nav">
                        <a class="nav-0" href="javascript:;">
                            <img class="icon" src="../img/g-1.png">
                            <p class="write">数据表</p>
                        </a>
                    </li>
                    <li class="nav">
                        <a class="nav-0" href="javascript:;">
                            <img class="icon" src="../img/f-1.png">
                            <p class="write">
                                网站日志</p>
                        </a>
                    </li>

                    <li class="nav">
                        <a class="nav-0" href="javascript:;">
                            <img class="icon" src="../img/h-1.png">
                            <p class="write">图片</p>
                        </a>
                    </li>


                    <li class="nav">
                        <a class="nav-0" href="javascript:;">
                            <img class="icon" src="../img/l-1.png">
                            <p class="write">留言</p>
                        </a>
                    </li>
                    <li class="nav">
                        <a class="nav-0" href="javascript:;">
                            <img class="icon" src="../img/t-1.png">
                            <p class="write">其他</p>
                        </a>
                    </li>


                </ul>
            </div>
        </div>

        <div class="right">
            <div class="control">
                <div class="shang">
                    控制台
                </div>
                <div class="small">
                    <div class="sTitle">
                        <img class="sPic" src="../img/k.png">
                        <span class="sTit">最近</span>
                    </div>
                </div>
                <from name="from">
                    <table class="table" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="fir">
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t2">ip</td>
                                <td class="t1">浏览器类型</td>
                                <td class="t2">语言</td>
                                <td class="t2">地址</td>
                                <td class="t2">操作系统</td>
                                <td class="t1">周访问次数</td>
                                <td class="t1">总访问次数</td>
                            </tr>

                            <?php
                            $ipMessage_obj = new ipDisplay();
                            messageHtml::ipDisplay($ipMessage_obj->getipMessage());
                            ?>
                            <!-- <tr>
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>

                            </tr>
                            -->
                        </tbody>
                    </table>
                </from>
            </div>
            <div class="data">
                <div class="shang">
                    数据表
                </div>
                <div class="small">
                    <div class="sTitle">
                        <img class="sPic" src="../img/k.png">
                        <span class="sTit">最近</span>
                    </div>
                </div>
                <from name="from">
                    <table class="table" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="fir">
                                <td class="t0">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t1">起点城市</td>
                                <td class="t1">终点城市</td>
                                <td class="t1">最近一周查询次数</td>
                                <td class="t1">总查询次数</td>
                            </tr>

                            <?php
                            $selectDisplay_obj = new selectDisplay;
                            messageHtml::selectMDisplay($selectDisplay_obj->getSelectMessage());
                            ?>
                            <!-- <tr>
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t1"></td>
                                <td class="t1"></td>
                                <td class="t1"></td>
                                <td class="t1"></td>
                            </tr>
                             -->
                        </tbody>
                    </table>
                </from>
            </div>
            <div class="daily">
                <div class="shang">
                    网站日志
                </div>
                <div class="small">
                    <div class="sTitle">
                        <img class="sPic" src="../img/k.png">
                        <span class="sTit">最近</span>
                    </div>
                </div>
                <from name="from">
                    <table class="table" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr class="fir">
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t2">域</td>
                                <td class="t1">点击次数</td>
                                <td class="t2">更新</td>
                                <td class="t1">点击次数</td>
                                <td class="t2">更新</td>
                                <td class="t2">状态</td>
                                <td class="t1">其他</td>
                            </tr>
                            <tr>
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                            </tr>
                            <tr>
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                            </tr>
                            <tr>
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                            </tr>
                            <tr>
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                            </tr>
                            <tr>
                                <td class="t1">
                                    <img class="sPic2" src="../img/k.png">
                                </td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                                <td class="t2"></td>
                                <td class="t2"></td>
                                <td class="t1"></td>
                            </tr>
                        </tbody>
                    </table>
                </from>
            </div>
            <div class="liu">

                <div class="shang">
                    留言
                </div>
                <div class="small">
                    <div class="sTitle">
                        <img class="sPic" src="../img/k.png">
                        <span class="sTit">最近</span>
                    </div>
                </div>
                <div class="contain">

                <?php
                messageHtml::leaveDisplay($leave_message);
                ?>
                </div>
            </div>

             <div class="look">
                    <div class="news2">
                        <img class="head2" src="../img/name.png">
                        <div class="poxes">
                        <span class="first2">Json</span>
                        <p class="mail">123456789@qq.com</p>
                        </div>
                        <i class="time2">right   sdasf   a</i>
                        <img class="tui2" src="../img/tui1.png">                        
                    </div>
                    <div class="pp">
                        <p class="nice2"> Diana Kennedy purchased a year subscription Diana Kennedy purchased a year subscription Diana Kennedy purchased a year subscription Diana Kennedy purchased a year subscription.</p>                        
                    </div>
                </div>

            <div class="picture">

                <div class="page-wrap">
                    <!-- Main -->
                    <div class="shang">
                        图片
                    </div>
                    <div class="small">
                        <div class="sTitle">
                            <img class="sPic" src="../img/k.png">
                            <span class="sTit">最近</span>
                        </div>
                    </div>
                    <section id="main">
                        <section id="galleries">
                            <div class="gallery">
                                <div class="content">
                                    <div class="media all people">
                                        <a href="../img/images/fulls/01.jpg" target="_blank">
                                            <img src="../img/images/thumbs/01.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all place">
                                        <a href="../img/images/fulls/05.jpg" target="_blank">
                                            <img src="../img/images/thumbs/05.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all thing">
                                        <a href="../img/images/fulls/09.jpg" target="_blank">
                                            <img src="../img/images/thumbs/09.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all people">
                                        <a href="../img/images/fulls/02.jpg" target="_blank">
                                            <img src="../img/images/thumbs/02.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all place">
                                        <a href="../img/images/fulls/06.jpg" target="_blank">
                                            <img src="../img/images/thumbs/06.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all thing">
                                        <a href="../img/images/fulls/10.jpg" target="_blank">
                                            <img src="../img/images/thumbs/10.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all people">
                                        <a href="../img/images/fulls/03.jpg" target="_blank">
                                            <img src="../img/images/thumbs/03.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all place">
                                        <a href="../img/images/fulls/07.jpg" target="_blank">
                                            <img src="../img/images/thumbs/07.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all thing">
                                        <a href="../img/images/fulls/11.jpg" target="_blank">
                                            <img src="../img/images/thumbs/11.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all people">
                                        <a href="../img/images/fulls/04.jpg" target="_blank">
                                            <img src="../img/images/thumbs/04.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all place">
                                        <a href="../img/images/fulls/08.jpg" target="_blank">
                                            <img src="../img/images/thumbs/08.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                    <div class="media all thing">
                                        <a href="../img/images/fulls/12.jpg" target="_blank">
                                            <img src="../img/images/thumbs/12.jpg" alt="" title="This right here is a caption." />
                                        </a>
                                    </div>
                                </div>
                                <div class="more">
                                    <button class="btn11">加载更多</button>
                                </div>
                            </div>

                        </section>


                </div>

            </div>

        </div>
    </div>
    <div id="elevator_item">
        <a id="elevator" onclick="return false;" title="000"></a>
        <a class="qr"></a>
        <div class="qr-popup">
            <a class="code-link">
                <img class="code" src="../img/wx.jpg" />
            </a>
            <span>扫描二维码，进入网页</span>
            <div class="arr"></div>
        </div>
    </div>
    <div id="foot">
        <p>@CSECL实验室 随行后台管理</p>
    </div>
    <script src="../js/main.js"></script>
    <script>
        $(function () {
            $(window).scroll(function () {
                var scrolltop = $(this).scrollTop();
                if (scrolltop >= 200) {
                    $("#elevator_item").show();
                } else {
                    $("#elevator_item").hide();
                }
            });
            $("#elevator").click(function () {
                $("html,body").animate({ scrollTop: 0 }, 500);
            });
            $(".qr").hover(function () {
                $(".qr-popup").show();
            }, function () {
                $(".qr-popup").hide();
            });
        });


        $(".wrap5").mousedown(function (event) {
            if ($(".wrap5").attr("a") == "off") {
                $(".wrap5 div").stop().animate({
                    "left": "48px"
                }, 400),
                    $(".wrap5").attr("a", "on")
            } else {
                $(".wrap5 div").stop().animate({
                    "left": "2px"
                }, 400),
                    $(".wrap5").attr("a", "off")
            }
        });
    </script>

</body>

</html>