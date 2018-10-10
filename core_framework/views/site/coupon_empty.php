<?php
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
.error-message-container{
    height: 150px;
    display: table;
    width: 100%;
}
.error-message{
    display: table-cell;
    vertical-align: middle;
    font-size: 16px;
    font-weight: bold;
    font-family: proxima-nova;
}");
?>

<header>
    <img src="<?php echo yii\helpers\Url::to('@web/images/header.png') ?>"/>
</header>
<div class="error-message-container">
    <div class="error-message">
        <div class="message1">
            We're sorry! The Tatcha family is working on getting more gifts for you. Please come back in a few hours to try again
        </div>
    </div>
</div>
<div class="demo">
    <img src="<?php echo yii\helpers\Url::to('@web/images/scratch.gif') ?>"/>
</div>