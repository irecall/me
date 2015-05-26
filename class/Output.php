<?php
/**
 * Description of OutputJson
 *
 * 
 */
class Output {
    public static function json($code = 0, $data = '', $msg = '') {
        header('Content-Type: application/json; charset=utf-8', true, 200);
        echo json_encode(array(
            'code' => $code,
            'data' => $data,
            'msg'  => $msg
        ),JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 标准化输出
     * @param int $code  0:操作成功 ｜ 1:操作失败
     * @param array $data  数据
     * @param array errInfo  操作成功为空，操作失败 array("errCode"=>"errMsg")
     * @param boolean $if_exit  输出后是否exit，默认为true
     * @return json 消息结构{"code":0|1,"data":{...},"errInfo":{'errode':'errMsg'}}
     * @author HANLei
     * @email 
     **/
    public static function json_new($code = 0, $data = '', $errInfo = array(), $if_exit = true)
    {
    	header('Content-Type: application/json; charset=utf-8', true, 200);
        echo json_encode(array(
            'code' => $code,
            'data' => $data,
            'errInfo'  => $errInfo
        ),JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($if_exit) exit;
    }

	public static function html_encode($array) {
		if (!is_array($array)) {
			return CHtml::encode($array);
		}
		$return = array();
		foreach ($array as $key => $value) {
			if (!is_array($value)) {
				$return[$key] = CHtml::encode($value);
			} else {
				$return[$key] = self::html_encode($value);
			}
		}
		return $return;
	}
}
