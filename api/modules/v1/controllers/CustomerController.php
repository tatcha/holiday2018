<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use api\modules\v1\models\Customer;
use api\modules\v1\models\Coupon;
use api\modules\v1\models\MailForm;

class CustomerController extends ActiveController
{
    public $modelClass = '';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
                'class' => HttpBearerAuth::className(),
                'only'=>['register', 'sendcoupon'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ]
        ];

        return $behaviors;
    }
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs[ "register" ] = ['POST','PUT'];
        $verbs[ "sendcoupon" ] = ['POST'];
        
        return $verbs;
    }
    
    
    public function actionRegister()
    {
        $model = new Customer();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '')) {
            //echo gmdate('Y-m-d H:i:s A', time()-28800); //PST datetime
            $model->date_created = date('Y-m-d',time());
            if($model->validate()){
                $exists = Customer::find()
                        ->where(['date_created'=>date('Y-m-d',time())])
                        ->andWhere(['email'=>$model->email])
                        ->exists();
                if($exists){
                    return ['response'=>['status'=>'error', 'error'=>['msg'=>'Your email already exist.']]];
                }
                $coupon = Coupon::getRandomCoupon();
                if(!empty($coupon)){
                    $deliveryId = '';
                    //$deliveryId = Customer::sendMail($model->email,$coupon->coupon_code, $coupon->type);
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
                        \api\modules\v1\models\Setting::setValue($statusKey, '1');
                    }
                    return ['response'=>['status'=>'success','coupon_code'=>$coupon->coupon_code,'email'=>$model->email,'coupon_type'=>$coupon->type]];
                }else{
                    return ['response'=>['status'=>'error', 'error'=>['msg'=>'Coupon Empty']]];
                }
                return $model;
            }else{
                $msgs = [];
                $errors = $model->getErrors();
                foreach($errors as $key=>$error){
                    $msgs['msg'] = $error[0];
                }
                return ['response'=>['status'=>'error', 'error'=>$msgs]];
            }
        }
    }
    
    public function actionSendcoupon()
    {
        $model = new MailForm();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->validate()) {
            $customer = Customer::find()
                    ->where(['email'=>$model->email])
                    ->orderBy('date_created DESC')
                    ->one();
            $type = $model->coupon_type;
            
            if(!empty($customer) && !empty($type)){
                $result = $model->sendCoupon($customer,$type);
                if($result) {
                    $customer->delivery_id = 1;
                    $customer->delivery_status = 1;
                    $customer->save(false);
                    return ['response'=>['status'=>'success', 'email'=>$customer->email, 'message_id'=> $customer->delivery_id]];
                }else{
                    return ['response'=>['status'=>'error', 'error'=>['msg'=>'Something went wrong.']]];
                }
            }else{
                return ['response'=>['status'=>'error', 'error'=>['msg'=>'Customer not exist.']]];
            }
      
        }else{
            $msgs = [];
            $errors = $model->getErrors();
            foreach($errors as $key=>$error){
                $msgs['msg'] = $error;
            }
            return ['response'=>['status'=>'error', 'error'=>$msgs]];
        }
    }
    
}
