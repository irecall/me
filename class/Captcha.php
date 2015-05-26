<?php

/**
 * 图片验证码
 *
 * @author dailei
 *
 */
class Captcha {

    /**
     * 图片宽度
     * @var integer
     */
    protected $_width   = 150;

    /**
     * 图片高度
     * @var integer
     */
    protected $_height  = 50;

    /**
     * 图片文本
     * @var string
     */
    protected $_text    = '';

    /**
     * 图片文本长度
     * @var string
     */
    protected $_length   = 4;
    
    /**
     * 文本字体
     * @var string
     */
    protected $_font    = 'airbus-special.ttf';
    
    /**
     * 可用的字母数字
     * @var string
     */
    protected $_units    = 'BCEFGHJKMPQRTVWXY2346789';

    /**
     * 生成图片
     */
    protected function _image() {
        //字符长度
        $length = strlen($this->_text);

        //文本总宽度
        $tWidth = 0;

        //字体大小
        $size = (int) min($this->_height * 0.7, $this->_width / $length * 0.9);

        //文字间距
        $margin = - (int) ($size / 10);

        //最大旋转角度
        $angleMax = $size > 20 ? 10 : 5;

        //字体文件
        $font = __DIR__ . '/fonts/' . $this->_font;

        //文本数组
        $text = array();
        for ($i = 0; $i < $length; $i++) {
            //字符
            $char   = $this->_text[$i];

            //旋转角度
            $angle  = mt_rand(-10, 10);

            //大小
            $box    = imagettfbbox($size, 0, $font, $char);
            $width  = max($box[0], $box[2], $box[4], $box[6]) - min($box[0], $box[2], $box[4], $box[6]);
            $height = max($box[1], $box[3], $box[5], $box[7]) - min($box[1], $box[3], $box[5], $box[7]);

            $text[] = array(
            	'c' => $char,
            	'a' => $angle,
            	'w' => $width,
            	'h' => $height,
            );

            $tWidth += $width;
        }

        //原始图片
        $src    = imagecreatetruecolor($this->_width, $this->_height);

        //背景使用浅色
        $bColor = imagecolorallocate($src, 0xff, 0xff, 0xff);

        //字体使用深色
        $fColor = imagecolorallocate($src, mt_rand(0, 0x7f), mt_rand(0, 0x7f), mt_rand(0, 0x7f));

        //填充背景
        imagefill($src, 0, 0, $bColor);

        //写文字
        $x = (int) (($this->_width - ($tWidth + (count($text) - 1) * $margin)) / 2);
        $y = (int) (($this->_height + $size) / 2);

        foreach ($text as $c) {
            imagettftext($src, $size, $c['a'], $x, $y, $fColor, $font, $c['c']);
            $x += $c['w'] + $margin;
        }

        //目标图片
        $dst    = imagecreatetruecolor($this->_width, $this->_height);

        //填充目标图片背景
        imagefill($dst, 0, 0, $bColor);

        //背景透明
        imagecolortransparent($dst, $bColor);



        //横向偏移量
        $offsetX = $size / 10;

        //随机数
        $rY = mt_rand(0, $this->_height);

        //不显示像素的概率
        $rH = max(20, min(350, 20 + ($size - 15) * 5));

        //拷贝原始图片到目标图片
        for ($i = 0; $i < $this->_width; $i++) {
            for ($j = 0; $j < $this->_height; $j++) {
                //原始图片像素
                $rgb = imagecolorat($src, $i, $j);

                //在目标图片上描点
                if ($rgb != $bColor) {
                    /* $t = mt_rand(0, 100);
                    if ($t < 5) {
                        $y = $j - 1;
                    } else if ($t > 95) {
                        $y = $j + 1;
                    } else {
                        $y = $j;
                    } */

                    if (mt_rand(1, 1000) > $rH) {
                        $x = $i + (sin(($j - $rY) / $this->_height * 2 * M_PI) * $offsetX);

                        imagesetpixel($dst, $x, $j, $rgb);
                    }
                }
            }
        }

        imagedestroy($src);

        //imagefilledellipse($dst, $this->_width * mt_rand(0, 100) / 100, $this->_height * mt_rand(0, 100) / 100, $tWidth, $this->_height, imagecolorallocatealpha($dst, ($fColor >> 16) & 0xff, ($fColor >> 8) & 0xff, $fColor & 0xff, 45));
        //imagefilledellipse($dst, $this->_width * mt_rand(0, 100) / 100, $this->_height * mt_rand(0, 100) / 100, $tWidth, $this->_height, imagecolorallocatealpha($dst, ($fColor >> 16) & 0xff, ($fColor >> 8) & 0xff, $fColor & 0xff, 60));
        //imagefilledellipse($dst, mt_rand() % $this->_width, mt_rand() % $this->_height, min($tWidth, $this->_width * 0.9), $this->_height, imagecolorallocatealpha($dst, ($fColor >> 16) & 0xff, ($fColor >> 8) & 0xff, $fColor & 0xff, mt_rand(80, 96)));
        imagefilledellipse($dst, $this->_width / 2, $this->_height / 2, min($tWidth, $this->_width) * 0.7, $this->_height, imagecolorallocatealpha($dst, ($fColor >> 16) & 0xff, ($fColor >> 8) & 0xff, $fColor & 0xff, mt_rand(80, 96)));

        //加入干扰象素;
        $count = (int) ($this->_width * $this->_height / 8);
        for($i = 0; $i < $count; $i++){
            $x = mt_rand() % $this->_width;
            $y = mt_rand() % $this->_height;

            if (imagecolorat($dst, $x, $y) != $bColor) {
                imagesetpixel($dst, $x, $y, $fColor);
            }
        }

        //输出
        imagepng($dst);

        imagedestroy($dst);

    }

    /**
     * __construct
     */
    public function __construct() {

    }

    /**
     * 设置宽高
     *
     * @param integer $width
     * @return Captcha
     */
    public function setSize($width, $height) {
        $this->_width = max(40, (int) $width);
        $this->_height = max(15, min($this->_width, (int) $height));

        return $this;
    }

    /**
     * 设置字体
     *
     * @param string $font
     * @return Captcha
     */
    public function setFont($font) {
        $this->_font = $font;

        return $this;
    }

    /**
     * 设置文本
     *
     * @param string $width
     * @return Captcha
     */
    public function setText($text) {
        $this->_text = $text;

        return $this;
    }
    
    /**
     * 设置文本长度
     *
     * @param string $width
     * @return Captcha
     */
    public function setLength($length) {
    	$this->_length = $length;
    
    	return $this;
    }
    
    /**
     * 生成验证码
     *
     * @return string
     */
    public function genCode() {
        $return = '';
        for ($i = 0; $i < $this->_length; $i++) {
            $return .= $this->_units[mt_rand(0, 23)];
        }
        return $return;
    }

    /**
     * 输出到浏览器
     */
    public function display() {
        header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Content-Type: image/png');
        $this->_image();
    }

    /**
     * 获取图片
     */
    public function fetch() {
        ob_start();
        $this->_image();
        return ob_get_clean();
    }
    
}
