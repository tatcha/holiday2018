<?php

namespace api\modules\v1\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class MailForm extends Model
{
    public $email;
    public $coupon_type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email','coupon_type'], 'required'],
            
            [['coupon_type'], 'string', 'max' => 255],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
    public function sendCoupon($customer,$type)
    {
        $coupon = Coupon::findOne($customer->coupon_id);
        $from = 'noreply@tatcha.com';
        $subject = 'test';
        $body = 'test';
        
        if($coupon->type != $type){
            return false;
        }
        
        $result = self::sendMail($customer,$coupon,$type);
        return $result;
    }
    

    public static function sendMail($customer,$coupon,$type)
    {

        if($type == 'TYPE1'){
            $type = 'Black_Friday_Offer1';
        } else if($type == 'TYPE2'){
            $type = 'Black_Friday_Offer2';
        } else if($type == 'TYPE3'){
            $type = 'Black_Friday_Offer3';
        } else if($type == 'TYPE4'){
            $type = 'Black_Friday_Offer4';
        }
        
        
        $url = 'https://a.klaviyo.com/api/track';
        $data = array (
                'token' => 'pk_012ff1252c05c17490b409a3a807ed6f98',
                'event' => 'Black Friday Promo',
                'customer_properties' =>
                    array (
                        '$email' => $customer->email,
                     ),
                'properties' =>
                    array (
                        '$event_id' => uniqid(md5($customer->email)),
                        'promoCode' => $coupon->coupon_code,
                        'promoType' => $type,
                    ),
                'time' => time(),
                );

        $data_string = base64_encode(json_encode($data));
        $url = $url.'?data='.$data_string;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $status = curl_exec($ch);
        return $status;
    }
}
