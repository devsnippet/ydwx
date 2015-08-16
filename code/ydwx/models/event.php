<?php
/**
 * 微信事件通知消息，XML结构
 */
class YDWXEvent extends YDWXResponse{
    /**
     * 消息类型,见MSG_TYPE_*
     * @var unknown
     */
    public $MsgType;
    /**
     * 事件类型，对于接受到的普通消息，为空
     * @var unknown
     */
    public $Event;
    public $ToUserName;
    public $CreateTime;
    public $FromUserName;
    protected $xml;
    public function build($msg){
        $this->xml = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        if( ! $this->xml){
            $this->errcode = -1;
            $this->errmsg = "无法解析xml";
            return;
        }
        foreach((array)$this->xml as $name=>$value){
            $this->$name = $value;
        }
    }
    /**
     * 
     * @param YDWXEvent $msg
     */
    public static function CreateEventMsg($msg){
        $obj =  new YDWXEvent($msg);
        if($obj->Event){
            $clsname  = "YDWX".ucfirst((strtolower($obj->MsgType))).ucfirst(strtolower($obj->Event));
        }else{
            $clsname = "YDWXEventMsg".ucfirst(strtolower($obj->MsgType));
        }
        return new $clsname($msg);
    }
    /**
     * 返回处理自己的hook 名称，默认以MsgType_Event作为hook name
     * 没有Event就以EVENT_MSG_MsgType作为hook name
     * @return string
     */
    public function HookName(){
        if($this->Event){
            $hookname = strtoupper($this->MsgType."_".$this->Event);
        }else{
            $hookname = "EVENT_MSG_".strtoupper($this->MsgType);
        }
        return constant("YDWXHook::{$hookname}");
    }
}

/**
 * 普通消息事件推送基类
 * @author leeboo
 * @see http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html
 */
class YDWXEventMsg extends YDWXEvent{
    public $MsgId;
}
/**
 * 文字消息
 * @author leeboo
 *
 */
class YDWXEventMsgText extends YDWXEventMsg{
    public $Content;
}
/**
 * 图片消息
 * @author leeboo
 *
 */
class YDWXEventMsgImage extends YDWXEventMsg{
    /**
     * 图片链接
     * @var unknown
     */
    public $PicUrl;
    /**
     * 图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
     * @var unknown
     */
    public $MediaId;
}
/**
 * 音频消息
 * @author leeboo
 *
 */
class YDWXEventMsgVoice extends YDWXEventMsg{
    /**
     * 格式
     * @var unknown
     */
    public $Format;
    public $MediaId;
    /**
     * 识别出的文字内容，如果公众号没有开启，它为空
     * @var unknown
     */
    public $Recognition;
}
/**
 * 连接消息
 * @author leeboo
 *
 */
class YDWXEventMsgLink extends YDWXEventMsg{
    public $Title;
    public $Description;
    public $Url;
}
/**
 * 地址消息
 * @author leeboo
 *
 */
class YDWXEventMsgLocation extends YDWXEventMsg{
    public $Location_X;
    public $Location_Y;
    public $Scale;
    public $Label;
}
/**
 * 视频消息
 * @author leeboo
 *
 */
class YDWXEventMsgVideo extends YDWXEventMsg{
    public $MediaId;
    public $ThumbMediaId;
}
/**
 * 小视频消息
 * @author leeboo
 *
 */
class YDWXEventMsgShortVideo extends YDWXEventMsgVideo{}
class YDWXEventUnsubscribe extends YDWXEvent{}
/**
 * 用户关注事件，有两张情况
 * 1.扫描二维码关注推送的消息，这是EventKey与Ticket有值
 * 2.正常关注
 * @author leeboo
 *
 */
class YDWXEventSubscribe extends YDWXEvent{
    /**
     * 事件KEY值，qrscene_为前缀，后面为二维码的参数值
     * @var unknown
     */
    public $EventKey;
    /**
     * 二维码的ticket，可用来换取二维码图片
     * @var unknown
     */
    public $Ticket;
    public function HookName(){
        if($this->EventKey) return YDWXHook::EVENT_SCAN_SUBSCRIBE;
        return parent::HookName();
    }
}

/**
 * 扫描带参数二维码事件
 * @author leeboo
 * @see http://mp.weixin.qq.com/wiki/2/5baf56ce4947d35003b86a9805634b1e.html#.E6.89.AB.E6.8F.8F.E5.B8.A6.E5.8F.82.E6.95.B0.E4.BA.8C.E7.BB.B4.E7.A0.81.E4.BA.8B.E4.BB.B6
 */
class YDWXEventScan extends YDWXEvent{
    /**
     * 事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id
     * @var unknown
     */
    public $EventKey;
    /**
     * 二维码的ticket，可用来换取二维码图片
     * @var unknown
     */
    public $Ticket;
}
/**
 * 上报地理位置事件
 * 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，或在进入会话后每5秒上报一次地理位置
 * 
 * @author leeboo
 * @see http://mp.weixin.qq.com/wiki/2/5baf56ce4947d35003b86a9805634b1e.html#.E4.B8.8A.E6.8A.A5.E5.9C.B0.E7.90.86.E4.BD.8D.E7.BD.AE.E4.BA.8B.E4.BB.B6
 *
 */
class YDWXEventLocation extends YDWXEvent{
    /**
     * 纬度
     * @var unknown
     */
    public $Latitude;
    /**
     * 经度
     * @var unknown
     */
    public $Longitude;
    /**
     * 精度
     */
    public $Precision;
}

/**
 * 点击菜单拉取消息时的事件推送
 * @author leeboo
 * @see http://mp.weixin.qq.com/wiki/2/5baf56ce4947d35003b86a9805634b1e.html#.E8.87.AA.E5.AE.9A.E4.B9.89.E8.8F.9C.E5.8D.95.E4.BA.8B.E4.BB.B6
 */
class YDWXEventClick extends YDWXEvent{
    /**
     * 事件KEY值，与自定义菜单接口中KEY值对应
     * @var unknown
     */
    public $EventKey;
}
/**
 * 点击菜单跳转链接时的事件推送
 * @author leeboo
 * @see http://mp.weixin.qq.com/wiki/2/5baf56ce4947d35003b86a9805634b1e.html#.E8.87.AA.E5.AE.9A.E4.B9.89.E8.8F.9C.E5.8D.95.E4.BA.8B.E4.BB.B6
 */
class YDWXEventView extends YDWXEvent{
    /**
     * 事件KEY值，设置的跳转URL
     * @var unknown
     */
    public $EventKey;
}
/**
 * 扫码推事件的事件推送
 * @author leeboo
 * @see http://mp.weixin.qq.com/wiki/9/981d772286d10d153a3dc4286c1ee5b5.html#scancode_push.EF.BC.9A.E6.89.AB.E7.A0.81.E6.8E.A8.E4.BA.8B.E4.BB.B6.E7.9A.84.E4.BA.8B.E4.BB.B6.E6.8E.A8.E9.80.81
 */
class YDWXEventScancode_push extends YDWXEvent{
    /**
     * 事件KEY值，由开发者在创建菜单时设定
     * @var unknown
     */
    public $EventKey;
    /**
     * 扫描类型，一般是qrcode
     * @var unknown
     */
    public $ScanType;
    /**
     * 扫描结果，即二维码对应的字符串信息
     * @var unknown
     */
    public $ScanResult;
    private $ScanCodeInfo;
    public function build($msg){
        parent::build($msg);
        if( $this->xml ){
            $this->ScanType   = $this->ScanCodeInfo->ScanType;
            $this->ScanResult = $this->ScanCodeInfo->ScanResult;
        }
    }
}

/**
 * 扫码推事件且弹出“消息接收中”提示框的事件推送
 * @author leeboo
 *
 */
class YDWXEventScancode_waitmsg extends YDWXEventScancode_push{}

/**
 * 弹出系统拍照发图的事件推送
 * @author leeboo
 *
 */
class YDWXEventPic_sysphoto extends YDWXEventScancode_push{
    /**
     * 事件KEY值，由开发者在创建菜单时设定
     * @var unknown
     */
    public $EventKey;
    private $SendPicsInfo;
    /**
     * 发送的图片数量
     * @var unknown
     */
    public $Count;
    /**
     * 图片组数，格式array(array(PicMd5Sum=>""),array(PicMd5Sum=>""))
     * PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
     * @var unknown
     */
    public $PicList;
    public function build($msg){
        parent::build($msg);
        if( $this->xml ){
            $this->Count      = $this->SendPicsInfo->Count;
            $array = array();
            foreach ($this->SendPicsInfo->PicList->item as $value){
                $array[] = (array)$value;
            }
            $this->PicList    = $array;
        }
    }
}
/**
 * 弹出拍照或者相册发图的事件推送
 * @author leeboo
 *
 */
class YDWXEventPic_photo_or_album extends YDWXEventPic_sysphoto{}
/**
 * 弹出微信相册发图器的事件推送
 * @author leeboo
 *
 */
class YDWXEventPic_weixin extends YDWXEventPic_sysphoto{}
class YDWXEventLocation_select extends YDWXEvent{
    private $SendLocationInfo;
    /**
     * 事件KEY值，由开发者在创建菜单时设定
     * @var unknown
     */
    public $EventKey;
    public $Location_X;
    public $Location_Y;
    /**
     * 精度，可理解为精度或者比例尺、越精细的话 scale越高
     * @var unknown
     */
    public $Scale;
    /**
     * 地理位置的字符串信息
     * @var unknown
     */
    public $Label;
    /**
     * 朋友圈POI的名字，可能为空
     * @var unknown
     */
    public $Poiname;
}