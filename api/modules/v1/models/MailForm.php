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
    public function sendCoupon($customer)
    {
        $coupon = Coupon::findOne($customer->coupon_id);
        $from = 'noreply@tatcha.com';
        $subject = 'test';
        $body = 'test';
        $type = 'Black_Friday_Offer1';
        $result = self::sendMail($customer,$coupon,$type);
        return $result;
    }
    

    public static function sendMail($customer,$coupon,$type)
    {

        $template['TYPE_1'] = 'Black_Friday_Offer1';
        $template['TYPE_2'] = 'Black_Friday_Offer1';
        $template['TYPE_3'] = 'Black_Friday_Offer1';
        $template['TYPE_4'] = 'Black_Friday_Offer1';
        
        $url = 'https://tatcha.slgnt.us/Portal/Api/organizations/TATCHA/journeys/transactional/'.$template[$type].'/send';
        $data = array (
                'items' =>
                        array (
                                0 =>
                                array (
                                'recipient' => $customer->email,
                                'language' => 'EN',
                                'data' =>
                                        array (
                                        'PROMO_CODE' =>$coupon->coupon_code,
                                        ),
                                ),
                ),
        );
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'X-ApiKey: 3HxZVl6JTnlVVvlaI2FErOXLkQrer0WA8LSaRjCI7g4=:mvpmewfrmx0gbJChk2Nh+orexM36M3tks+Kg6cqQ67c=',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        return json_decode($result, true);
    }
}
