<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
\Yii::$app->view->registerMetaTag([
    'name' => 'og:url',
    'content' => 'https://www.tatcha.com/get-tatcha/thanks2017',
]);
\Yii::$app->view->registerMetaTag([
    'name' => 'og:image:url',
    'content' => 'https://www.tatcha.com/images/uploads/holiday-2017-shop-dropdown.png',
]);
\Yii::$app->view->registerMetaTag([
    'name' => 'og:image:type',
    'content' => 'image/png',
]);
\Yii::$app->view->registerMetaTag([
    'name' => 'og:image:width',
    'content' => '200',
]);
\Yii::$app->view->registerMetaTag([
    'name' => 'og:image:width',
    'content' => '200',
]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <script src="//use.typekit.net/lhn4dnc.js"></script>
    <script>try{Typekit.load({ async: true });}catch(e){}</script>
</head>
<body>
<?php $this->beginBody() ?>
    <?php
$this->registerCss("
.fb-wrapper{
    border-radius: 4px;
    font-size: 13px;
    height: 28px;
    padding: 0 4px 0 6px;
    background: #4267b2;
    border: 1px solid #4267b2;
    color: #fff;
    cursor: pointer;
    font-family: Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    margin: 0;
    -webkit-user-select: none;
    white-space: nowrap;
}
.fb-wrapper a{
    color: white;
    text-decoration: none;
    font-weight:bold;
}
.fb-wrapper svg{
    height: 16px;
    width: 16px;
    vertical-align: bottom;
}
.promo-container{
    margin-top:2%;
}
.shop-now-button{
    font-size: 18px;
    font-style: italic;
    border: 1px solid #ccc;
    display: inline-block;
    padding: 2% 7%;
    font-family: \"utopia-std,Georgia,serif\";
    background-color: transparent;
    text-decoration:  none;
    color: #333;
}
.shop-now-button-container{
    margin-bottom:3%;
}
.links{
    margin-top:2%;
}
.links a{
    color: #430098;
    text-decoration: none;
    font-size: 16px;
    line-height: 1.4616;
    font-family:'utopia-std';
}
.links::after{
    display: table;
    content: '';
    clear: both;
}
.links .separator {
    padding-left: 20px;
    padding-right: 20px;
}
.links .faq-link{
    /*float: left;*/
}
.links .disclaimer-link{
    /*float: right;*/
}");
?>
<div class="container">
        <!-- <header>
            
        </header> -->
        <div id="scratch-pad" class="scratch-pad">
            <?= $content; ?>
        </div>
        <?php if(yii::$app->controller->action->id=='exists' || yii::$app->controller->action->id=='coupon-empty'){ ?>
        <div class="promo-container">
            <div class="shop-now-button-container">
                <a href="https://www.tatcha.com/" target="_blank" class="shop-now-button">SHOP NOW</a>
            </div>
            <div>
<!--                <div id="fb-root"></div>
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.11';
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
                <div class="fb-share-button" data-href="https://www.tatcha.com/get-tatcha/thanks2017" data-layout="button" data-size="large" data-mobile-iframe="true">
                    <a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.tatcha.com%2Fget-tatcha%2Fthanks2017&amp;src=sdkpreparse">Share</a>
                </div>-->
                <button class="fb-wrapper">
                    <a class="fb-share-button" href="http://www.facebook.com/sharer.php?s=100&p[url]=https://www.tatcha.com/get-tatcha/thanks2017&p[images][0]=https://www.tatcha.com/images/uploads/holiday-2017-shop-dropdown.png&display=popup">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" color="#ffffff">
                                <path fill="#ffffff" fill-rule="evenodd" d="M8 14H3.667C2.733 13.9 2 13.167 2 12.233V3.667A1.65 1.65 0 0 1
                                    3.667 2h8.666A1.65 1.65 0 0 1 14 3.667v8.566c0 .934-.733
                                    1.667-1.667
                                    1.767H10v-3.967h1.3l.7-2.066h-2V6.933c0-.466.167-.9.867-.9H12v-1.8c.033
                                    0-.933-.266-1.533-.266-1.267 0-2.434.7-2.467
                                    2.133v1.867H6v2.066h2V14z">
                                </path>
                            </svg>
                        </span>
                        <span>Share</span>
                    </a>
                </button>
            </div>
        </div>
        <?php }else{ ?>
        <div class="promo-container" style="display:none;">
            <div class="shop-now-button-container">
                <a href="https://www.tatcha.com/" target="_blank" class="shop-now-button">SHOP NOW</a>
            </div>
            <div>
<!--                <div id="fb-root"></div>
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.11';
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
                <div class="fb-share-button" data-href="https://www.tatcha.com/get-tatcha/thanks2017" data-image="https://www.tatcha.com/images/uploads/holiday-2017-shop-dropdown.png" data-layout="button" data-size="large" data-mobile-iframe="true">
                    <a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.tatcha.com%2Fget-tatcha%2Fthanks2017&amp;src=sdkpreparse&image=https://www.tatcha.com/images/uploads/holiday-2017-shop-dropdown.png">Share</a>
                </div>-->
                <button class="fb-wrapper">
                    <a class="fb-share-button" href="http://www.facebook.com/sharer.php?s=100&p[url]=https://www.tatcha.com/get-tatcha/thanks2017&p[images][0]=https://www.tatcha.com/images/uploads/holiday-2017-shop-dropdown.png&display=popup">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" color="#ffffff">
                                <path fill="#ffffff" fill-rule="evenodd" d="M8 14H3.667C2.733 13.9 2 13.167 2 12.233V3.667A1.65 1.65 0 0 1
                                    3.667 2h8.666A1.65 1.65 0 0 1 14 3.667v8.566c0 .934-.733
                                    1.667-1.667
                                    1.767H10v-3.967h1.3l.7-2.066h-2V6.933c0-.466.167-.9.867-.9H12v-1.8c.033
                                    0-.933-.266-1.533-.266-1.267 0-2.434.7-2.467
                                    2.133v1.867H6v2.066h2V14z">
                                </path>
                            </svg>
                        </span>
                        <span>Share</span>
                    </a>
                </button>
            </div>
        </div>
        <?php } ?>
        <div class="links">
            <a href="https://www.tatcha.com/get-tatcha/black-friday-faqs" target="_blank" class="faq-link">FAQs</a><span class="separator">|</span>
            <a href="https://www.tatcha.com/get-tatcha/black-friday-terms-and-conditions" target="_blank" class="disclaimer-link">Promotional Disclaimer</a>
        </div>
    </div>

<?php $this->endBody() ?>
<script type="text/javascript">
    $(document).ready(function() {
    $('.fb-share-button').click(function(e) {
        e.preventDefault();
        window.open($(this).attr('href'), 'fbShareWindow', 'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        return false;
    });
});
</script>
</body>
</html>
<?php $this->endPage() ?>
