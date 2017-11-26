<?php
namespace app\models;

use Yii;
use yii\base\Model;

class UploadPdfForm extends Model{
    public $pdf;
    protected $controller;


    public function __construct(\app\controllers\SiteController $controller) 
    {
        $this->controller=$controller;
    }

    public function rules()
    {
        return [
            ['pdf','required'],
            ['pdf','file','extensions'=>'pdf','maxSize' => 1024 * 1024 * 50]
        ];
    }
    

    public function upload()
    {
        if ($this->validate()) {
            $this->pdf->saveAs('uploads/' . $this->pdf->baseName . '.pdf');
            return true;
        } else {
            return false;
        }
    }
    
    public function processPdf()
    {
        exec(
                'magick '.$this->controller->getUploadPath().$this->pdf->baseName . '.pdf '.
                $this->controller->getUploadPath().'a.png', $output
        );
        unlink($this->controller->getUploadPath().$this->pdf->baseName . '.pdf ');
    }
    /**
     * Количество страниц в pdf файле
     * 
     * @return int
     */
    public function getPageNum()
    {
        $i=0;
        while (true) {
            if (!file_exists($this->controller->getUploadPath().'a-'.$i.'.png')) {
                return $i;
            } else {
                $i++;
            }
        }
    }
    /**
     * Определяет, портретное ли ориентация в пдф файл
     * 
     * @return boolean
     * @throws Exception
     */
    public function isPortrait()
    {
        $path=$this->controller->getUploadPath().'a-0.png';
        if (!file_exists($path)) {
            throw new Exception("Unable to open file", 1);
        } 
        $size=  getimagesize($path);
        return ($size[1]>=$size[0]);
    }
    /**
     * Убирает изображения
     */
    public function clearUpload()
    {
        $i=0;
        while (true) {
            $path=  $this->controller->getUploadPath().'a-'.$i.'.png';
            if (!file_exists($path)) {
                break;
            }
            unlink($path);
            $i++;
        }
    }
    
    public function getImagesUri()
    {
        $path='uploads/';
        $images=array();
        $ttl=$this->getPageNum();
        if ($ttl==0) {
            throw new Exception("Images dont exists", 2);
        }
        for ($i=0;$i<$ttl;$i++) {
            $images[]=$path.'a-'.$i.'.png';
        }
        return $images;
    }
    
    public function getImagesFilePath()
    {
        $images=array();
        $ttl=$this->getPageNum();
        if ($ttl==0) {
            throw new Exception("Images dont exists", 2);
        }
        for ($i=0;$i<$ttl;$i++) {
            $images[]=$this->controller->getUploadPath().'a-'.$i.'.png';
        }
        return $images; 
    }
}


