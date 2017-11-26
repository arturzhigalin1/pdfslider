<?php
/**
 * @version GIT <arturzhigalin1/pdfslider>
 * @category Controller
 * @author artur zhigalin <arturzhigalin1@gmail.com>
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 * @tag pdf
 */
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
/**
 * @version GIT <arturzhigalin1/pdfslider>
 * @category Controller
 * @author artur zhigalin <arturzhigalin1@gmail.com>
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 * @tag pdf
 */
class SiteController extends Controller
{
    /**
     * Путь к директории для сохранения файлов
     */
    protected $uploadPath;


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * ХЗ
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model=new \app\models\UploadPdfForm($this);
        $images=array();
        $slider=new \app\models\Slider($this);
        if (Yii::$app->request->isPost) {
            $model->pdf = \yii\web\UploadedFile::getInstance($model, 'pdf');
            if ($model->upload()) {
                // file is uploaded successfully
                // converting pdf to png files
                $model->processPdf();
                // check total page number
                $err=false;
                if ($model->getPageNum()>=20) {
                    $model->addError("pdf", "Количество страниц не должно превышать 20");
                    $err=true;
                }
                // checking portrait dims
                if (!$model->isPortrait()) {
                    $model->addError("pdf", "Ориентация должна быть портретной");
                    $err=true;
                }
                $images=$model->getImagesUri();
                $slider->images=$model->getImagesFilePath();
                $slider->generateZip();
                $slider->saveImages();
                $slider->defineSessionInfo();
                //return;
            }
        } else {
            $model->clearUpload();
        }
        $zipLink=$slider->linkZip();
        return $this->render(
            'index', ['model'=>$model, 'images'=>$images,
            'link'=>$zipLink]
        );
    }
    
    public function actionGetimages()
    {
        $slider=new \app\models\Slider($this);
        $slider->setId(Yii::$app->getRequest()->getQueryParam('id'));
        $images=array();
        try
        {
            $images=$slider->getImages();
        } catch (\Exception $e){
            print json_encode(array('status'=>2));exit;
        }
        print json_encode(array('images'=>$images, 'status'=>1));exit;
    }

    public function getUploadPath()
    {
        if (!$this->uploadPath) {
            $path=explode('/', $_SERVER['SCRIPT_FILENAME']);
            unset($path[sizeof($path)-1]);
            $this->uploadPath=  implode('/', $path).'/uploads/';
        }
        return $this->uploadPath;
    }

    public function getAssetsPath()
    {
            $path=explode('/', $_SERVER['SCRIPT_FILENAME']);
            unset($path[sizeof($path)-1]);
            return implode('/', $path).'/assets/';
    }
}
