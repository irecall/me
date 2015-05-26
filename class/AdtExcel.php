<?php
/**
* @author tome
*/
//require_once Yii::getPathOfAlias('system.vendors.PHPExcel.Classes') . '/PHPExcel.php';
//设定为false，使得扩展的autoload可以进行
Yii::$enableIncludePath = false;
//引入phpexcel，具体的文件夹在  yii/framework/vendors/phpexcel/
Yii::import('system.vendors.PHPExcel.Classes.PHPExcel',TRUE);

class AdtExcel
{
	public $objPHPExcel;

	public function __construct() {
		$this->objPHPExcel = new PHPExcel();
		$this->objPHPExcel->getProperties()->setCreator("云平台");
	}

	public function setCellValue($datas) {
		$key = 1;
		foreach ($datas as $data) {
			if (is_array($data)) {
				$col = 'a';
				foreach ($data as $value) {
					$this->objPHPExcel->getActiveSheet()->setCellValue($col . $key ,$value);
					$this->objPHPExcel->getActiveSheet()->getStyle($col . $key)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->objPHPExcel->getActiveSheet()->setCellValueExplicit($col . $key ,$value, PHPExcel_Cell_DataType::TYPE_STRING);
					if ($key == 1) {
						$this->objPHPExcel->getActiveSheet()->getStyle($col . $key)->getFont()->setBold(true);
						//$this->objPHPExcel->getActiveSheet()->getStyle($col . $key)->getFont()->setSize(20);
					}
					$col++;
				}
			}
			$key++;
		}
	}

	public function mergeCells($datas) {
		$key = 1;
		foreach ($datas as $data) {
			if (is_array($data)) {
				$col = 'a';
				foreach ($data as $value) {
					if (is_int($value)) {
						$pre_key =  $key - $value;
						if ($value > 1) {
							//解绑
							$before_key =  $key -1;
							$this->objPHPExcel->getActiveSheet()->unmergeCells($col . $pre_key . ':' . $col . $before_key);
						}
						$this->objPHPExcel->getActiveSheet()->mergeCells($col . $pre_key . ':' . $col . $key);
					}
					$col++;
				}
			}
			$key++;
		}
	}
	
	public function getRealPath($path) {
		return Yii::app()->params['xlsPath'] . $path;
	}
	
	public function save($path) {
		$excel_path = $this->getRealPath($path);
		if (!file_exists(dirname($excel_path))) {
			mkdir(dirname($excel_path),0777,true);
		}
		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		$objWriter->save($excel_path);
		if (file_exists($excel_path)) {
			//Log::logs('@' . date("Y-m-d H:i:s" ,time()) . ' [' . $excel_path . "] 创建成功\n\n",'createXls');	
			return $excel_path;
		} else {
			//Log::logs('@' . date("Y-m-d H:i:s" ,time()) . ' [' . $excel_path . "] 创建失败\n\n",'error');	
			return false;
		}
	}
}
