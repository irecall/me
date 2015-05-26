<?php
/**
 * 文件上传控制器 依类于Yii核心库 CUploadedFile
 * @author xiaoke
 * *@param string $filepath 文件的路径，例如：‘/aa/bb’=>protected/data/aa/bb
 * @param string $filename 文件名称，可以自定义，也可默认，默认为md5时间戳
 * @param string $upload_file 前端文件提交的名称
 * @param string $is_success 上传成功true 默认为false
 * @param string $baseUpload  Yii 基础上传类对象
 *
 * 多文件上传
 * file 组件名称 upload[]
 * $up = new UploadFile('protected/data/testUpload'，'upload')
 * if($up->uploads()->volid()){
 *      //返回文件名 是一个二维数组
 *      $fileNames = $up->getFileNames;
 * }
 *
 */

class UploadFile{

    private $filepath='';//默认在protection/data目录下创建
    private $filename='';
    private $filenames='';
    private $upload_file='';
    private $is_success = false;
    private $baseUpload = '';  //Yii 基础上传类

    /**
     * @param $filepath  上传的目录
     * @param $upload_file HTML上传组件的表单名
     * @param string $filename 上传后的文件名
     * @param string $type 上传的类型 用 ， 隔开，例如 png,jpg,jpeg,bmp,gif
     * @param int $size 上传文件的最大尺寸 单位M
     */
    public function __construct($filepath,$upload_file,$filename='',$type = 'png,jpg,jpeg,bmp,gif,octet-stream',$size = 2){
        //初始化文件名，默认md5命名
        if (empty($filename))
            $this->filename=md5(time().rand(10000,100000));
        //根据路径自动创建目录 uploadBasePath 基础目录定义在 config\main.php
        $this->filepath=$this->createDir(rtrim(Yii::app()->params['uploadBasePath'].$filepath,'/'));
        $this->upload_file = $upload_file;
        $this->type = $type;
        $this->size = $size * 1024 *1024;
    }

    /**
     * 自动创建目录
     * @param $dir string 创建目录的路径
     * @return mixed 成功返回目录
     */

    private function createDir($dir){
        if(!is_dir($dir))  {
            if(!$this->createDir(dirname($dir))){
                throw new Exception("目录创建失败，请检查路径:{$dir}");
            }
            if(!mkdir($dir,0775)){
                throw new Exception("目录创建失败，请检查路径:{$dir}");
            }
        }
        return $dir;
    }

    /**
     * @return string
     * @throws Exception
     */

    public function Upload() {
        if (empty($this->upload_file))
            throw new Exception('缺少必须参数！');
        //表单名获取上传类 Object
        $this->baseUpload = CUploadedFile::getInstanceByName($this->upload_file);

        $this->checkFile(); //检查文件是否合法

        //复制文件到目录
        if (!($this->is_success = $this->baseUpload->saveAs($this->getFileAddress()))){
            throw new Exception('上传文件失败！Error:'.$this->baseUpload->getError());
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function Uploads() {
        if (empty($this->upload_file))
            throw new Exception('缺少必须参数！');
        //表单名获取上传类 Object
        $uploadObjs = CUploadedFile::getInstancesByName($this->upload_file);
        foreach($uploadObjs as $key => $val){
            $this->baseUpload = $val;
            //检查文件是否合法
            $this->checkFile();
            //复制文件到目录
            if (!($this->is_success = $this->baseUpload->saveAs($this->getFileAddress()))){
                $this->filenames[] =  $this->getFileAddress();
            }else{
                throw new Exception('上传文件失败！Error:'.$this->baseUpload->getError());
            }
        }
        return $this;
    }

    /**
     * 返回上传结果
     * @return mixed
     */
    public function volid(){
        return $this->is_success;
    }

    /**
     * @return string 返回上传路径和文件名
     */
    public function getFileAddress(){
        return $this->getPath().'/'.$this->getFileName();
    }

    /**
     * @return string 返回上传路径和文件名
     */
    public function getFileNames(){
        return $this->filenames;
    }

    /**
     * @return string 返回文件名称
     */
    public function getFileName(){
        return $this->filename.'.'.$this->getType();
    }

    /**
     * @return string 返回上传的路径
     */
    public function getPath(){
        return $this->filepath;
    }

    /**
     * @return string 返回上传的大小
     */
    public function getSize(){
        return $this->baseUpload->getSize();
    }

    /**
     * @return string 返回上传的类型
     */
    public function getType(){
        $tm = $this->baseUpload->getName();
        $tm = explode('.',$tm);
        return $tm[count($tm)-1];
    }

    /**
     * 检查文件大小 类型 是否允许上传 不允许抛出异常
     * @throws Exception
     */
    private function checkFile(){
        if(!preg_match("/{$this->getType()}/",$this->type))
            throw new Exception("上传文件不支持该{$this->getType()}类型");
        if($this->getSize()>=$this->size)
            throw new Exception("上传文件超过默认大小");
    }
}
?>