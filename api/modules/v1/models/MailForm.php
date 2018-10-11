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


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['email'], 'required'],
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
    public function sendMail()
    {
        $from = 'noreply@tatcha.com';
        $subject = 'test';
        $body = 'test';
        $email = $this->email;
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([$from])
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();

            return true;
        }
        return false;
    }
}
