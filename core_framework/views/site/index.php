<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use szaboolcs\recaptcha\InvisibleRecaptcha;

$this->title = 'Temari Ball';
$this->registerCss(".container{
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    text-align: center;
}
header img{
    width: 100%
}
.demo img{
    width: 100%;
}
.email-prompt{
    margin: 6% 0;
}
.email-prompt-message1{
    font-size: 16px;
    margin-bottom: 1%;
    font-family: \"proxima-nova\";
}

.email-prompt-message2{
    font-size: 16px;
    margin-bottom: 2%;
    font-family: \"proxima-nova\";
}
.email-text-box{
    display: block;
    width: 55%;
    margin: 0 auto 4%;
    padding: 1%;
    font-size: 18px;
    font-family: \"proxima-nova\";
    font-weight: 600;
}
.email-prompt-button{
    font-size: 18px;
    font-style: italic;
    border: 1px solid #ccc;
    display: inline-block;
    padding: 2% 7%;
    font-family: \"utopia-std\";
    background-color: transparent;
}
.hide{
   display : none;
}
.email-prompt-error-message{
    color: red;
    margin-bottom: 1.5%;
    min-height: 16px;
    font-size: 16px;
    font-family: \"proxima-nova\";
}");
?>
<header>
    <img src="images/header.png"/>
</header>

<?php
$form = ActiveForm::begin([
            'id' => 'register-form',
//                'fieldConfig' => [
//                    'options' => [
//                        'tag' => false,
//                    ],
//                ],
            'options' => ['class' => 'email-prompt']
        ]);
?>
<div class="email-prompt-message1">
    Enter your email to win 1 of 5 exclusive mystery offers.
</div>
<div class="email-prompt-message2">
    Your individual code will be sent to your email.
</div>
<?=
$form->field($model, 'email', [
    'template' => '{error}{input}',
    'inputOptions' => ['class' => 'email-text-box', 'placeholder' => 'EMAIL'],
    'errorOptions' => ['class' => 'email-prompt-error-message']
])
?>

<?php
echo InvisibleRecaptcha::widget([
  'name'         => 'SUBMIT',
  'formSelector' => '#register-form',
  'btnClass' => 'email-prompt-button',
  'badgePosition'=>'bottomright'
]);
?>
<?php  //echo Html::submitButton('SUBMIT', ['class' => 'email-prompt-button']) ?>

<?php ActiveForm::end(); ?>

<div class="demo">
    <img src="<?php echo yii\helpers\Url::to('@web/images/scratch.gif') ?>"/>
</div>
<script>
    $('form#register-form').on('beforeSubmit', function()
{
    var $form = $(this);
    var $submit = $form.find(':submit');
    //$submit.html('Processing...');
    $submit.prop('disabled', true);
});
</script>
