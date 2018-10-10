<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Customer;
use app\models\Coupon;
use yii\helpers\StringHelper;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Gd\Font;
use arogachev\excel\import\basic\Importer;
use yii\helpers\Html;
use szaboolcs\recaptcha\InvisibleRecaptchaValidator;


class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
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
    
//    public function beforeAction($action) {
//        $this->enableCsrfValidation = false;
//        return parent::beforeAction($action);
//    }

    /**
     * @inheritdoc
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
        $model = new Customer();
        if ($model->load(Yii::$app->request->post())) {
            //echo gmdate('Y-m-d H:i:s A', time()-28800); //PST datetime
            $model->date_created = date('Y-m-d',time());
            if($model->validate() && InvisibleRecaptchaValidator::validate(Yii::$app->request->post(InvisibleRecaptchaValidator::POST_ELEMENT))){
                $exists = Customer::find()
                        ->where(['date_created'=>date('Y-m-d',time())])
                        ->andWhere(['email'=>$model->email])
                        ->exists();
                if($exists){
                    return $this->redirect(['exists']);
                }
                $coupon = Coupon::getRandomCoupon();
                if(!empty($coupon)){
                    $deliveryId = Customer::sendMail($model->email,$coupon->coupon_code, $coupon->type);
                    $coupon->updateAttributes(['status'=>1]);
                    $model->coupon_id = $coupon->id;
                    if($deliveryId){
                        $model->delivery_status = Customer::MAIL_SEND;
                    }else{
                        $model->delivery_status = Customer::MAIL_NOT_SEND;
                    }
                    $model->attempts = 1;
                    $model->delivery_id = $deliveryId;
                    $model->save(false);
                    if($coupon->type == 'GOLDEN'){
                        $statusKey = 'golden_status_'.date('Ymd');
                        \app\models\Setting::setValue($statusKey, '1');
                    }
                }else{
                    return $this->redirect(['coupon-empty']);
                }
                return $this->redirect(['coupon', 'id' => StringHelper::base64UrlEncode($model->id)]);
            }else{
                return $this->render('index',[
                    'model'=>$model,
                ]);
            }
        }
        return $this->render('index',[
            'model'=>$model,
        ]);
    }
    
    public function actionCoupon($id)
    {
        $id = StringHelper::base64UrlDecode($id);
        $model = Customer::findOne($id);
        $coupon = Coupon::findOne($model->coupon_id);
        
        return $this->render('coupon',[
            'model'=>$model,
            'coupon' => $coupon
        ]);
    }
    
    public function actionCouponEmpty()
    {
        return $this->render('coupon_empty');
    }
    
    public function actionExists()
    {
        return $this->render('exists');
    }


    public function actionTest()
    {
        $coupon = Coupon::usedCouponCount();
        var_dump($coupon);
        //$deliveryStatus = Customer::readDelivery('f2422b82-294d-4ea7-957c-db74817e4455');
        //echo $deliveryStatus;
//        $delivery = Customer::sendMail('joji.thomas@litmus7.com');
//        echo $delivery;
        //exit;
        $token = '44388821-CD9B-479C-ABF2-EEBC95D3985B';
        $bronto = new \Bronto_Api();
        $bronto->setToken($token); // Or pass $token to the constructor of Bronto_Api
        $bronto->login(); // Only needs to be called once
        $listId = '0bc903ec000000000000000000000016e509';
        $messageId = '0bc903eb000000000000000000000017f8cd';
        
        //Creating contact and adding to the list
        $contactObject = $bronto->getContactObject();
        $contact = $contactObject->createRow();
        $contact->email  = 'tavinash@yahoo.com';
        $contact->status = \Bronto_Api_Contact::STATUS_ONBOARDING;

        // Add Contact to List
        $contact->addToList($listId); // $list can be the (string) ID or a Bronto_Api_List instance
        try {
            $result = $contact->save();
            $tContactId = $result->id;
        } catch (Exception $e) {
            // Handle error
        }
        //var_dump($result);
        
        //Reading contacts from particular list.
//        $contactObject = $bronto->getContactObject();
//        $contactsFilter['listId'] = array($listId);
//
//        $contactsCounter = 0;
//        $contactsPage    = 1;
//        while ($contacts = $contactObject->readAll($contactsFilter, array(), false, $contactsPage)) {
//            if (!$contacts->count()) {
//                break;
//            }
//
//            foreach ($contacts as $contact /* @var $contact \Bronto_Api_Contact_Row */) {
//                echo "{$contactsCounter}. {$contact->email}. {$contact->id}<br/>";
//                $contactsCounter++;
//            }
//
//            $contactsPage++;
//        }
        
       // $tContactId = '47f8038d-a8f2-40ec-acd2-7439ee8860ce';
        //Creating delivery
        $deliveryObject = $bronto->getDeliveryObject();

        /* @var $delivery \Bronto_Api_Delivery_Row */
        $delivery = $deliveryObject->createRow();
        $delivery->start      = date('c'); // Today
        $delivery->type       = \Bronto_Api_Delivery::TYPE_TEST;
        $delivery->messageId  = $messageId;
        $delivery->fromEmail  = 'info@litmus7.com';
        $delivery->fromName   = 'Tatcha Test';
        $delivery->replyTracking = true;
        $delivery->replyEmail   = 'info@e.tatcha.com';
        $delivery->recipients = [
            [
                'type' => 'contact',
                'id'   => $tContactId,
            ],
        ];
        $delivery->fields = [
            ['name'=>'couponCode','content'=>'COUPON125','type'=>'html']
        ];
        
        try {
            $result = $delivery->save();
            var_dump($result);
        } catch (Exception $e) {
            var_dump($e);
        }
    }
    
    public function actionImport($filename)
    {
        if(!file_exists(Yii::getAlias('@webroot/data/'.$filename))){
            echo "The file $filename does not exist";
            \Yii::$app->end();
        }
        $importer = new Importer([
            'filePath' => Yii::getAlias('@webroot/data/'.$filename),
            'standardModelsConfig' => [
                [
                    'className' => Coupon::className(),
                    'standardAttributesConfig' => [
                        [
                            'name' => 'type',
                            'label' => 'Type',
                            'valueReplacement' => function ($value) {
                                return $value;
                            },
                        ],
                        [
                            'label' => 'Coupon Code',
                            'name' => 'coupon_code',
                            'valueReplacement' => function ($value) {
                                return $value;
                            },
                        ],
                    ],
                ],
            ],
        ]);
        
        if (!$importer->run()) {
            echo $importer->error;

            if ($importer->wrongModel) {
                echo Html::errorSummary($importer->wrongModel);
            }
        }
        
        unlink(Yii::getAlias('@webroot/data/'.$filename));
    }
    
    public function actionOffline()
    {
        return $this->render('offline');
    }
   
}
