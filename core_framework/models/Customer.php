<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer".
 *
 * @property string $id
 * @property string $email
 * @property string $date_created
 * @property string $coupon_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const MAIL_NOT_SEND = 0;
    const MAIL_SEND = 1;
    const MAIL_DELIVERED = 2;
    
    
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'filter', 'filter'=>'strtolower'],
            [['email'], 'email','message'=>'Please enter a valid email address.'],
            [['date_created'], 'safe'],
            [['coupon_id', 'delivery_status', 'created_at', 'updated_at'], 'integer'],
            [['email'], 'string', 'max' => 128],
            //[['email', 'date_created'], 'unique', 'targetAttribute' => ['email', 'date_created'], 'message' => 'exist','on'=>'emailunique'],
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
            'email' => 'Email',
            'date_created' => 'Date Created',
            'coupon_id' => 'Coupon ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deliveryStatus' =>'Mail Status'
        ];
    }
    
    public function getCoupon()
    {
        return $this->hasOne(Coupon::className(), ['id' => 'coupon_id']);
    }
    
    public function getDeliveryStatus()
    {
        $deliveryStatus = '';
        if($this->delivery_status == '2'){
            $deliveryStatus = 'delivered';
        }elseif ($this->delivery_status == '1') {
            $deliveryStatus = 'sent';
        }else{
            $deliveryStatus = 'fail';
        }
        return $deliveryStatus;
    }
    
    public static function sendMail($email, $couponCode, $type)
    {
        $token = Yii::$app->params['b_token'];
        $bronto = new \Bronto_Api();
        $bronto->setToken($token); // Or pass $token to the constructor of Bronto_Api
        $bronto->login(); // Only needs to be called once
        $listId = Yii::$app->params['b_listId'];
        $messageId = Yii::$app->params['message_'.$type];
        $contactId = '';
        $deliveryId = 0;
        
        //Creating contact and adding to the list
        $contactObject = $bronto->getContactObject();
        $contact = $contactObject->createRow();
        $contact->email  = $email;
        $contact->status = \Bronto_Api_Contact::STATUS_ONBOARDING;

        // Add Contact to List
        $contact->addToList($listId); // $list can be the (string) ID or a Bronto_Api_List instance
        try {
            $result = $contact->save();
            $contactId = $result->id;
        } catch (\Exception $e) {
            return false;
        }
        
        if($contactId){
            $deliveryObject = $bronto->getDeliveryObject();

            /* @var $delivery \Bronto_Api_Delivery_Row */
            $delivery = $deliveryObject->createRow();
            $delivery->start      = date('c'); // Today
            $delivery->type       = \Bronto_Api_Delivery::TYPE_TRANSACTIONAL;
            $delivery->messageId  = $messageId;
            $delivery->fromEmail  = 'info@tatcha.com';
            $delivery->replyEmail = 'info@tatcha.com';
            $delivery->fromName   = 'Tatcha';
            $delivery->recipients = [
                [
                    'type' => 'contact',
                    'id'   => $contactId,
                ],
            ];
            $delivery->fields = [
                ['name'=>'couponCode','content'=>$couponCode,'type'=>'html']
            ];

            try {
                $result = $delivery->save();
                $deliveryId = $result->id;
                
            } catch (\Exception $e) {
                return false;
            }
        }
        
        return $deliveryId;
    }
    
    public static function readDelivery($deliveryId)
    {
        $deliveryStatus = Customer::MAIL_SEND;
        $token = Yii::$app->params['b_token'];
        $bronto = new \Bronto_Api();
        $bronto->setToken($token); // Or pass $token to the constructor of Bronto_Api
        $bronto->login(); // Only needs to be called once
        
        $deliveryObject = $bronto->getDeliveryObject();

        /* @var $delivery \Bronto_Api_Delivery_Row */
        //$delivery = $deliveryObject->createRow();
        $delivery = $deliveryObject->createRow(array(
            'id' => $deliveryId
        ));
        $result = $delivery->read();
        if(((int)$result->numDeliveries) > 0){
            $deliveryStatus = Customer::MAIL_DELIVERED;
        }
        return $deliveryStatus;
        //var_dump($result);
    }
}
