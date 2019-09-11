<?php
/**
 * 后台管理信息也的HTML代码输出文件
 * 
 * @author rwb
 * @version 1.1
 * 
 */
class messageHtml {
    /**
     * ip显示HTML代码输出页面
     */
    public static function ipDisplay($ipmessageArray){
        foreach($ipmessageArray as $value){
        ?>
        <tr>
            <td class="t1">
                <img class="sPic2" src="../img/k.png">
            </td>
            <td class="t2"><?php echo $value['ip'];?></td>
            <td class="t1"><?php echo $value['browser'];?></td>
            <td class="t2"><?php echo $value['language'];?></td>
            <td class="t1"><?php echo $value['nationality'].$value['province'].$value['city'];?></td>
            <td class="t2"><?php echo $value['opreating']?></td>
            <td class="t1"><?php echo $value['weekNum']?></td>
            <td class="t1"><?php echo $value['num']?></td>
        </tr>
        <?php
        }
    } 

    /**
     * 输出用户查找信息的html代码
     */
    public static function selectMDisplay($message){
        foreach($message as $value){
            ?>
            <tr>
                <td class="t1">
                    <img class="sPic2" src="../img/k.png">
                </td>
                <td class="t1"><?php echo $value['startCity']?></td>
                <td class="t1"><?php echo $value['toCity']?></td>
                <td class="t1"><?php echo $value['weekNum']?></td>
                <td class="t1"><?php echo $value['num']?></td>
            </tr>
            <?php
        }
    }

    /**
     * 输出留言信息的HTML代码
     */
    public static function leaveDisplay_promt($message){
        ?>
        <div class="bug">
            <a class="liejian" href="javascript:;">
                <img class="icon-0" src="../img/a.png">

                <?php if($message[0]['num']==0){
                }else{?>
                    <span class="num"><?php echo $message[0]['num']?></span>

                    <?php
                }?>


            </a>
            <div class="angle"></div>

            <div class="hidden">
            <div class="summit">
                <p>您有<?php echo $message[0]['num']?>条新消息</p>
            </div>
            <div class="boo">

        <?php
        //var_dump(count($message));die;
            foreach($message as $value){
                
                ?>
                <div class="news">
                    <img class="head" src="../img/name.png">
                    <span class="first"><?php echo $value['y_name']?></span>
                    <i class="time"><?php echo $value['createtime']?></i>
                    <a href="javascript:;">
                        <p class="nice"><?php echo $value['opinion']?></p>
                    </a>
                </div>

                <?php
            }
            ?>
            </div>
            <div class="footer">
                <p>查看所有信息</p>
            </div>
        </div>
        </div>

        
                <?php
            
    }

    /**
     * 输出留言信息提示显示HTML代码
     */
    public static function leaveDisplay($message){
        foreach($message as $value){
            ?>

            <div class="tuo">
                <img class="head1" src="../img/name.png">
                <span class="first1"><?php echo $value['y_name']?></span>
                <i class="time1"><?php echo $value['createtime']?></i>
                 <p class="nice1"><?php echo $value['opinion']?></p>
                <input class="hui" type="button" value="查看" />
                <input id="leave_id" type="hidden" value=<?php echo $value['id']?>>
            </div>

            <?php
        }
        

    }
}