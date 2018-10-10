<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "coupon".
 *
 * @property string $id
 * @property string $coupon_code
 * @property integer $status
 */
class Coupon extends \yii\db\ActiveRecord
{
    const TYPE_1 = 'TYPE1';
    const TYPE_2 = 'TYPE2';
    const TYPE_3 = 'TYPE3';
    const TYPE_4 = 'TYPE4';
    const TYPE_GOLDEN = 'GOLDEN';
    
    const TYPE_DATA = [
            self::TYPE_1 => '20% off $100+ order',
            self::TYPE_2 => '$20 off $100+ order',
            self::TYPE_3 => '$15 off $75+ order',
            self::TYPE_4 => 'FS Hand Cream with $100 purchase',
            self::TYPE_GOLDEN => 'Golden Ticket'
            ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coupon_code'], 'required'],
            [['status'], 'integer'],
            [['coupon_code'], 'string', 'max' => 255],
        ];
    }
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'coupon_code' => 'Coupon Code',
            'status' => 'Status',
        ];
    }
    
    public static function getRandomCoupon()
    {
        $type = [];
//        $ini_array = Coupon::readINI();
//        $typeSum = array_sum($ini_array);
//        if($typeSum<=0){
//            self::resetINI();
//            $ini_array = Coupon::readINI();
//        }
        
        $now = date('H:i:s');
        $glodenHours = ['10:30:00','11:32:00','12:43:00','15:12:00','16:22:00','16:48:00'];
        $statusKey = 'golden_status_'.date('Ymd');
        $goldenStatus = Setting::getValue($statusKey);
        $conditionKey = 'golden_condition_'.date('Ymd');
        $goldenCondition = Setting::getValue($conditionKey);
        if(!$goldenCondition){
            $conditionValue = $glodenHours[array_rand($glodenHours, 1)];
            Setting::setValue($conditionKey, $conditionValue);
            $goldenCondition = Setting::getValue($conditionKey);
        }
        if(!$goldenStatus && $goldenCondition && (strtotime($now) >= strtotime($goldenCondition))){
            $type = self::TYPE_GOLDEN;
        }else{
            $type = [Coupon::TYPE_1, Coupon::TYPE_2, Coupon::TYPE_3, Coupon::TYPE_4];
//            foreach ($ini_array as $key=>$value){
//                if((int)$value >0){
//                    $type[] = $key;
//                }
//            }
        }

        $availableCoupons = Coupon::availableCoupons();
        $alertCounts = [50000,20000,15000,10000,7500,5000];
        if(in_array($availableCoupons, $alertCounts)){
            Coupon::sendAlert($availableCoupons);
        }
        
        if($type){         
            $model = Coupon::find()
                    ->where(['status'=>0])
                    ->andWhere(['in','type',$type])
                    ->orderBy('rand()')
                    ->limit(1)
                    ->one();
//            if(isset($model->type) && isset($ini_array[$model->type])){
//                $ini_array[$model->type] = (int) $ini_array[$model->type]-1; 
//                self::writeINI($ini_array);
//            }
            return $model;
        }else{
            return null;
        }
    }
    
    public static function readINI()
    {
        $dataINI = Yii::getAlias('@webroot/core_framework/config/data.ini');
        $ini_array = parse_ini_file($dataINI);
        return $ini_array;
    }
    
    public static function writeINI($data)
    {
        $dataINI = Yii::getAlias('@webroot/core_framework/config/data.ini');
        $writeBuffer = ';data.ini'.PHP_EOL;
                foreach ($data as $name=>$value)
                        $writeBuffer .= "$name = $value".PHP_EOL;
            file_put_contents($dataINI, $writeBuffer);
    }
    
    public static function resetINI()
    {
        $data = Yii::$app->params['type_limit'];
        self::writeINI($data);
    }

    public static function todayUsage()
    {
        $today = \Date('Y-m-d',time());

        $query = new \yii\db\Query();
        $query->from('customer')
            ->where(['date_created'=>$today]);
        $count = $query->count('id');
        return $count;
    }
    
    public static function availableCoupons()
    {
        $query = new \yii\db\Query();
        $query->from('coupon')
            ->where(['status'=>0]);
        $count = $query->count('id');
        return $count;
    }
    
    public static function availableCouponCount()
    {
        $query = new \yii\db\Query();
        //$query->select(['ANY_VALUE(type) as type', 'COUNT(type) as used'])
        $query->select(['type as type', 'COUNT(type) as available'])
                ->from('coupon')
                ->where(['status'=>0])
                ->groupBy('type');
        $data = $query->all();
        return $data;
    }
    
    public static function usedCouponCount()
    {
        $query = new \yii\db\Query();
        //$query->select(['ANY_VALUE(type) as type', 'COUNT(type) as used'])
        $query->select(['type as type', 'COUNT(type) as used'])
                ->from('coupon')
                ->where(['status'=>1])
                ->groupBy('type');
        $data = $query->all();
        return $data;
    }
    
    public static function generateAttachment($filename,$date)
    {
        if(!$date){
            $date = date('Y-m-d');
        }
        \moonland\phpexcel\Excel::export([
            'models' => Customer::find()->where(['date_created'=>$date])->all(),
            'columns' => [
                'email:text',
                'date_created:date:Date',
                [
                        'attribute' => 'delivery_status',
                        'header' => 'Delivery Status',
                        'format' => 'text',
                        'value' => function($model) {
                            return $model->deliveryStatus;
                        },
                ],
                [
                        'attribute' => 'coupon.type',
                        'header' => 'Coupon Type',
                        'format' => 'text',
                        'value' => function($model) {
                            return Coupon::TYPE_DATA[$model->coupon->type];
                        },
                ],
                //'coupon.type:text',
                'coupon.coupon_code:text',
            ],
            'headers' => [
                'email' => 'Email',
            ],
            'fileName'=>$filename,
            'savePath'=>'export',
            'asAttachment'=>false,
            'format'=>'Excel5',
        ]);
    }
    
    public static function sendAlert($count)
    {
        $availableCoupons = Coupon::availableCouponCount();
        //$email = ['joji.thomas@litmus7.com','michelle@litmus7.com', 'justin@litmus7.com', 'avinash@litmus7.com'];
        $email = ['andrea@tatcha.com','berto@tatcha.com', 'simon@tatcha.com', 'josephine@tatcha.com','collette@tatcha.com','sophia@tatcha.com','justin.maliakal@tatcha.com','avinash.tharayil@tatcha.com','joji.thomas@litmus7.com','ryan.scott@tatcha.com'];
        $subject = "Temari Low Coupon Alert";
        $body = '<b>Coupons are running low. Available coupons : '.$count.'</b>' ;
        $body .= '<br/><br/><table style="border:1px solid black; border-collapse: collapse;">';
        $body.='<tr><th style="border:1px solid black; padding: 15px; text-align: left;">Type</th><th style="border:1px solid black; padding: 15px; text-align: left;">Available</th></tr>';
        foreach($availableCoupons as $available){
            $body.="<tr><td style=\"border:1px solid black; padding: 15px; text-align: left;\">".Coupon::TYPE_DATA[$available['type']]."</td><td style=\"border:1px solid black; padding: 15px; text-align: left;\">".$available['available']."</td></tr>";
        }
        $body.='</table>';
        
        try {
            return Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom(['maildelivery.tatcha@gmail.com' => 'Temari'])
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();
        }catch (\Exception $e) {
            //echo 'Caught exception: ', $e->getMessage(), "\n";die;
        }
    }
}
