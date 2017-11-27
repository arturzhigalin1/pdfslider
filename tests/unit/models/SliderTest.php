<?php
namespace app\tests;

class SliderTest extends \Codeception\Test\Unit
{
    
    protected $slider;
    protected $id;
    protected $pathFrom;
    protected $pathTo;
    
    protected function _before()
    {
        $_SERVER['SCRIPT_FILENAME']='C:/xampp/htdocs/wowworks/tests/unit/models/SliderTest.php';
        $_SERVER['SERVER_NAME']='localhost';
        $_SERVER['SCRIPT_NAME']='/wowworks/web/index.php';
        $this->pathFrom='C:/xampp/htdocs/wowworks/tests/unit/models/uploads1/';
        $this->pathTo='C:/xampp/htdocs/wowworks/tests/unit/models/uploads/';
        $h= opendir($this->pathFrom);
        while (false !== ($entry = readdir($h))) {
            if ($entry=='.' || $entry=='..') {
                continue;
            }
            copy($this->pathFrom.$entry, $this->pathTo.$entry);
        }
        closedir($h);
        $this->slider=new \app\models\Slider(new \app\controllers\SiteController(1,2));
        $this->id='adf';
        $this->slider->setId($this->id);
        for($i=0;$i<6;$i++){
            $this->slider->images[]=$this->pathTo.'a-'.$i.'.png';
        }
    }
    
    public function testSlider(){
        //saveImages
        $this->assertFalse(is_dir($this->pathTo.$this->id));
        $this->slider->saveImages();
        $this->assertTrue(is_dir($this->pathTo.$this->id));
        $this->assertTrue(file_exists($this->pathTo.$this->id.'/a-0.png'));
        //getImages
        $images=$this->slider->getImages();
        $this->assertEquals(count($images),6);
        $this->assertEquals($images[0],'localhost/wowworks/web/uploads/'.$this->id.'/a-0.png');
        //linkZip
        $_COOKIE['zipTime']=time();
        $_COOKIE['zipId']=  $this->id;
        $this->assertEquals($this->slider->linkZip(),'uploads/'.$this->id.'.zip');
        //generateZip
        $this->slider->generateZip();
        $this->assertTrue(file_exists($this->pathTo.$this->id.'.zip'));
    }
    

    protected function _after() {
        rrmdir($this->pathTo);
        mkdir($this->pathTo);
    }
}

function rrmdir($src) {
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                rrmdir($full);
            }
            else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
}