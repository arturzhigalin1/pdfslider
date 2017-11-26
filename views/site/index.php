<?php

/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'My Yii Application';
$this->registerCssFile("assets/lightslider.css");
$this->registerJsFile("assets/lightslider.js", ['position'=>yii\web\View::POS_BEGIN]);
?>
<div class="site-index">


    <div class="body-content">
        <?php if (count($images)):?>
        <script type="text/javascript">
        jQuery().ready(function(){
           jQuery('#slider').lightSlider({autoWidth:true,item:1}); 
        });
        </script>
        <div class="row">
            <div id="slider">
                <?php foreach ($images as $img):?>
                <div><img style="" src="<?php print $img ?>"/></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
                   <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'upload-pdf-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?php echo $form->field($model, 'pdf')->fileInput(['autofocus' => true]) ?>

                    <div class="form-group">
                        <?php echo Html::submitButton('Конвертировать', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
        <?php if ($link):?>
        <a href="<?php echo $link?>">Скачать</a>
        <?php endif;?>
    </div>
</div>
