<?php
use yii\web\View;
use yii\helpers\Url;
$this->registerCss(".container{
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    text-align: center;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
canvas{
    left: 0;
}
.fb-share-button{
    height: 1.5rem;
}
.scratch-pad{
    width: 100%;
    margin: 0 auto;
    display: table;
    font-family: 'sans-serif';
    margin-bottom: 1rem;
}");

?>
<!-- <div class="scratch-container">
  <div id="promo" class="scratchpad"></div>
</div> -->
<!-- <div class="promo-container" style="display:none;">
  <div class="promo-code"></div>
  <a href="https://www.tatcha.com/" target="_blank" class="btn btn-primary">Redeem Now</a>
</div> -->

<!-- facebook-sdk-end-->
<?php
$this->registerJs(
"selectBG = '".Url::to('@web/images/offer/'.$coupon->type.'.jpg')."';
function main(){
    var scratchPad = document.getElementById('scratch-pad');
    scratchPad.style.height = scratchPad.offsetWidth+'px';
    $('.scratch-pad').wScratchPad({
        size : 70,       
        bg : selectBG,
        fg: '".Url::to('@web/images/overlay.jpg')."',
        cursor : 'url(\'".Url::to('@web/images/tatcha_coin.png')."\') 5 5, default',
        scratchMove: function (e, percent) {
        if (percent > 35) {
          $('.promo-container').show();
          $('.scratch-pad').wScratchPad('clear');
          $('canvas').remove();
        }
      }
    });  
}
window.onload = window.onresize = main;", View::POS_READY, 'scratch-js'
);
?>

