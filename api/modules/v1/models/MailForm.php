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
        $result = self::sendMail();
        return $result;
    }
    
    public static function sendMail()
    {
        $url = 'https://tatcha.slgnt.us/Portal/Api/organizations/TATCHA/journeys/transactional/Order_Confirmation/send';
        $data = array (
          'items' => 
          array (
            0 => 
            array (
              'recipient' => 'joji.thomas@litmus7.com',
              'language' => 'EN',
              'data' => 
              array (
                'ORDER_NUMBER' => '12311',
                'ORDER_DATE' => '2015-07-29',
                'CUSTOMER_NUMBER' => 'Optional text example',
                'CUSTOMER_NAME' => 'Optional text example',
                'BILLING_ADDRESS' => 
                array (
                  0 => 
                  array (
                    'FIRST_NAME' => 'Optional text example',
                    'LAST_NAME' => 'Optional text example',
                    'ADDRESS1' => 'Optional text example',
                    'ADDRESS2' => 'Optional text example',
                    'CITY' => 'Optional text example',
                    'POSTAL_CODE' => 'Optional text example',
                    'STATE_CODE' => 'Optional text example',
                    'COUNTRY_CODE' => 'Optional text example',
                    'PHONE' => 'Optional text example',
                  ),
                ),
                'SHIPPING_ADDRESS' => 
                array (
                  0 => 
                  array (
                    'FIRST_NAME' => 'Optional text example',
                    'LAST_NAME' => 'Optional text example',
                    'ADDRESS1' => 'Optional text example',
                    'ADDRESS2' => 'Optional text example',
                    'CITY' => 'Optional text example',
                    'POSTAL_CODE' => 'Optional text example',
                    'STATE_CODE' => 'Optional text example',
                    'COUNTRY_CODE' => 'Optional text example',
                    'PHONE' => 'Optional text example',
                  ),
                ),
                'PRODUCT' => 
                array (
                  0 => 
                  array (
                    'ID' => 'Optional text example',
                    'QUANTITY' => 3,
                    'PRICE' => 'Optional text example',
                    'DISCOUNT' => 3.140000000000000124344978758017532527446746826171875,
                    'PRODUCT_NAME' => 'Optional text example',
                    'PRODUCT_URL' => 'Optional text example',
                    'REPLENISHMENT' => 'Optional text example',
                    'PRODUCT_SECONDARY_NAME' => 'Optional text example',
                    'PRODUCT_VARIANT' => 'Optional text example',
                  ),
                ),
                'SHIPPING_METHOD' => 'Optional text example',
                'CARD_LAST_FOUR_DIGITS' => 'Optional text example',
                'CARD_TYPE' => 'Optional text example',
                'EXP_DATE' => '2015-07-29',
                'GIFT_CARD_LAST_FOUR' => 'Optional text example',
                'SUBTOTAL' => 'Optional text example',
                'ORDER_TOTAL' => 'Optional text example',
                'TAX' => 'Optional text example',
                'SHIPPING_COST' => 'Optional text example',
                'DISCOUNT' => 'Optional text example',
                'PROMO_CODE' => 'Optional text example',
                'REPLENISHMENT_ORDER' => true,
                'GIFTITEMS' => 
                array (
                  0 => 
                  array (
                    'RECIPIENT_NAME' => 'Optional text example',
                    'RECIPIENT_EMAIL' => 'Optional text example',
                    'SENDER_NAME' => 'Optional text example',
                    'SENDER_EMAIL' => 'Optional text example',
                    'PRICE' => 'Optional text example',
                  ),
                ),
                'GIFT_MESSAGE' => 'Optional text example',
                'GIFT_ITEM_PRESENT' => true,
                'MANAGE_ORDER_URL' => 'Optional text example',
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
