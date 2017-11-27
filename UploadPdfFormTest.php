<?php
namespace app\tests;

class UploadPdfFormTest extends \Codeception\Test\Unit{
    protected $uploadPdfForm;
    protected $pathFrom;
    protected $pathTo;

    protected function _before()
    {
        $_SERVER['SCRIPT_FILENAME']='C:/xampp/htdocs/wowworks/tests/unit/models/UploadPdfFormTest.php';
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
        $this->uploadPdfForm=new \app\models\UploadPdfForm(new \app\controllers\SiteController(1,2));
    }
    
    public function testGetPageNum(){
        $this->assertEquals($this->uploadPdfForm->getPageNum(),6);
    }
    
    public function testIsPortrait(){
        $this->assertTrue($this->uploadPdfForm->isPortrait());
    }
    
    public function testGetImagesUri(){
        $uris=$this->uploadPdfForm->getImagesUri();
        $this->assertEquals(count($uris),6);
        $this->assertEquals($uris[0],'uploads/a-0.png');
    }
    
    public function testGetImagesFilePath(){
        $paths=$this->uploadPdfForm->getImagesFilePath();
        $this->assertEquals(count($paths),6);
        $this->assertEquals($paths[0],'C:/xampp/htdocs/wowworks/tests/unit/models/uploads/a-0.png');
    }
    
    public function testClearUpload(){
        $this->assertEquals((count(scandir($this->pathTo))),8);
        $this->uploadPdfForm->clearUpload();
        $this->assertEquals((count(scandir($this->pathTo))),2);
    }

    protected function _after()
    {
        
    }
}
