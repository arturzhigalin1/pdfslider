<?php
namespace app\models;

class Slider 
{
    public $images=array();
    protected $id;
    protected $controller;
    
    public function __construct(\app\controllers\SiteController $controller)
    {
        $this->controller=$controller;
    }

    public function getId()
    {
        if (!$this->id) {
            $this->id=  md5(time());
        }
        return $this->id;    
    }
    
    public function setId($id)
    {
        $this->id=$id;
    }
    /**
     * Генерирует zip архив
     * 
     * @throws \Exception
     */
    public function generateZip()
    {
        if (sizeof($this->images) ==0) {
            throw new \Exception("Empty images",3);
        }
        $zip=new \ZipArchive();
        $zip->open(
                $this->controller->getUploadPath().$this->getId().".zip", 
                \ZipArchive::CREATE
        );
        $zip->addEmptyDir('images');
        $zip->addEmptyDir('assets');
        $html=  file_get_contents(
                $this->controller->getUploadPath().
                "template.html"
        );
        $imagesHtml='';
        foreach ($this->images as $img) {
            $imgName=explode("/", $img);
            $imgName=$imgName[sizeof($imgName)-1];
            $imagesHtml.='<div><img src="images/'.$imgName.'"/></div>';
            $zip->addFile($img, 'images/'.$imgName);
        }
        $html=  str_replace("%IMAGES%", $imagesHtml, $html);
        $zip->addFromString('index.html', $html);
        $zip->addFile(
                $this->controller->getAssetsPath().
                'jquery.js', 'assets/jquery.js'
        );
        $zip->addFile(
                $this->controller->getAssetsPath().
                'lightslider.js', 'assets/lightslider.js'
        );
        $zip->addFile(
                $this->controller->getAssetsPath().
                'lightslider.css', 'assets/lightslider.css'
        );
        $zip->close();
    }
    /**
     *  Сохраняет файлы изображений
     * 
     * @throws Exception
     */
    public function saveImages()
    {
        if (sizeof($this->images)==0) {
            throw new Exception("Empty images",3);
        }
        $dir=$this->controller->getUploadPath().$this->getId();
        mkdir($dir);
        foreach ($this->images as $img) {
            $name=  explode("/", $img);
            $name=$name[sizeof($name)-1];
            copy($img, $dir.'/'.$name);
        }
    }
    
    public function getImages()
    {
        $uri=explode('/', $_SERVER['SCRIPT_NAME']);
        unset($uri[sizeof($uri)-1]);
        $uri=$_SERVER['SERVER_NAME'].implode('/',$uri).'/uploads/'.$this->getId().'/';
        $images=array();
        $h=  opendir($this->controller->getUploadPath().$this->getId());
        if (!$h) {
            throw new Exception("Images directory didnt exists",4);
        }
        while (false !== ($entry = readdir($h))) {
            if ($entry=='.' || $entry=='..') {
                continue;
            }
            $images[]=$uri.$entry;
        }
        closedir($h);
        return $images;
    }

    public function defineSessionInfo()
    {
        setcookie('zipTime', time());
        setcookie('zipId', $this->getId());
        /**
         * Сохраняем в $_COOKIE для правильной работы
         * функции linkZip
         */
        $_COOKIE['zipTime']=time();
        $_COOKIE['zipId']=  $this->getId();
    }
    
    /**
     * Линк на zip архив
     * 
     * @return string|boolean
     */
    public function linkZip()
    {
        if (isset($_COOKIE['zipTime']) && isset($_COOKIE['zipId'])) {
            if ((time()-$_COOKIE['zipTime'])<=30*60) {
                return 'uploads/'.$_COOKIE['zipId'].'.zip';
            } else {
                return false;
            }
        }
        return false;
    }
}

