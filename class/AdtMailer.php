<?php
/**
* @author tome
*/
require_once Yii::getPathOfAlias('system.vendors.PHPMailer') . '/PHPMailerAutoload.php';
/* //设定为false，使得扩展的autoload可以进行
Yii::$enableIncludePath = false;
//引入phpexcel，具体的文件夹在  yii/framework/vendors/PHPMailer/
Yii::import('system.vendors.PHPMailer.*'); */

class AdtMailer
{
	public $objPHPMailer;

	public function __construct() {
		$config = Yii::app()->params['mailConfig'];
		$this->objPHPMailer = new PHPMailer();
		$this->objPHPMailer->isSMTP(); 
		$this->objPHPMailer->Host = $config['Host'];
		$this->objPHPMailer->Port = $config['Port'];
		$this->objPHPMailer->SMTPAuth = $config['SMTPAuth'];
		$this->objPHPMailer->Username = $config['Username'];
		$this->objPHPMailer->Password = $config['Password'];
	    $this->objPHPMailer->SMTPSecure = $config['SMTPSecure'];
		$this->objPHPMailer->From = $config['From'];
		$this->objPHPMailer->CharSet = $config['CharSet'];
	}

	public function send($subject,$to,$body,$attachment='') {
		$this->objPHPMailer->Subject = $subject;
		if (is_array($to)) {
			foreach ($to as $t) {
				$this->objPHPMailer->addAddress($t);
			}
			$logs_to = implode(';',$to);
		} else {
			$this->objPHPMailer->addAddress($to);
			$logs_to = $to . ';';
		}
		$this->objPHPMailer->Body = $body;
		if (is_array($attachment)) {
			foreach ($attachment as $a) {
				$this->objPHPMailer->AddAttachment($a,basename($a));
			}
			$logs_attachment =  implode("\n",$attachment);
		} else {
			$this->objPHPMailer->AddAttachment($attachment,basename($attachment));
			$logs_attachment = $attachment . "\n";
		}
		if ($this->objPHPMailer->send()) {
			//Log::logs('@' . date("Y-m-d H:i:s" ,time()) . ' 发送成功 [' . $subject . "]\nto : " . $logs_to . "\n" . $logs_attachment . "\n\n",'sendMail');
			return true;
		} else {
			//Log::logs('@' . date("Y-m-d H:i:s" ,time()) . ' 发送失败 [' . $subject . "]\nto : " . $logs_to . "\n" . $logs_attachment . "\n" . $this->objPHPMailer->ErrorInfo . "\n\n",'error');
			return false;
		}
	}
}
