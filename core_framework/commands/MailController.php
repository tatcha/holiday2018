<?php

namespace app\commands;

use yii\console\Controller;
use Yii;
use arogachev\excel\import\basic\Importer;
use yii\helpers\Html;
use app\models\Customer;
use app\models\Coupon;
use yii\helpers\StringHelper;

class MailController extends Controller
{
    
    public function actionSend()
    {
        
    }
    
    public function actionRead()
    {
        $customers = Customer::find()->where(['delivery_status'=>1])->all();
        foreach ($customers as $customer){
             $deliveryStatus = Customer::readDelivery($customer->delivery_id);
             $customer->delivery_status = $deliveryStatus;
             $customer->save();
         }
    }
    
    public function actionReport($date=null)
    {
        ini_set('max_execution_time', 600);
        if(!$date){
            $date = date('Y-m-d');
        }
        
        //Getting total usage 
        $usages = Coupon::usedCouponCount();
        
        //Prepare report content
        //$email = 'joji.thomas@litmus7.com';
        //$email = ['joji.thomas@litmus7.com','michelle@litmus7.com', 'justin@litmus7.com', 'avinash@litmus7.com'];
        $email = ['andrea@tatcha.com','berto@tatcha.com', 'simon@tatcha.com', 'josephine@tatcha.com','collette@tatcha.com','sophia@tatcha.com','justin.maliakal@tatcha.com','avinash.tharayil@tatcha.com','joji.thomas@litmus7.com','ryan.scott@tatcha.com'];
        $subject = "Temari - Report - ".Yii::$app->formatter->asDate(strtotime($date));
        $body = '';
        $body .= '<table style="border:1px solid black; border-collapse: collapse;">';
        $body.='<tr><th style="border:1px solid black; padding: 15px; text-align: left;">Coupon Type</th><th style="border:1px solid black; padding: 15px; text-align: left;">Total Usage</th></tr>';
        foreach($usages as $usage){
            $body.="<tr><td style=\"border:1px solid black; padding: 15px; text-align: left;\">". Coupon::TYPE_DATA[$usage['type']]."</td><td style=\"border:1px solid black; padding: 15px; text-align: left;\">".$usage['used']."</td></tr>";
        }
        $body.='</table>';
        $filename = 'report_'.time().'.xls';
        Coupon::generateAttachment($filename,$date);
        //echo $filename;
        
        //Sending report email with attachment
        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom(['maildelivery.tatcha@gmail.com' => 'Temari'])
            ->setSubject($subject)
            ->setHtmlBody($body)
            ->attach('export/'.$filename)
            ->send();
    }
    
    public function actionReadDelivery($date=null)
    {
        if(!$date){
            $date = date('Y-m-d');
        }
        
        $totalUsers = Customer::find()
                ->where(['delivery_status'=>1,'date_created'=>$date])
                ->andWhere(['<=','attempts',2])
                ->count();
        $limit = 50;
        $batch = ceil($totalUsers / $limit);
        for ($i = 0; $i < $batch; ++$i) {
           
            $limitStart = $i * $limit;
            $customers = Customer::find()
                    ->where(['delivery_status'=>1,'date_created'=>$date])
                    ->andWhere(['<=','attempts',2])
                    ->limit($limit)
                    ->offset($limitStart)
                    ->all();
            //$counter = 0;
            foreach ($customers as $customer) {
                //echo $customer->id."Count ".$counter++." <br>";
                if(!empty($customer->delivery_id)){
                    $deliveryStatus = Customer::readDelivery($customer->delivery_id);
                    $customer->delivery_status = $deliveryStatus;
                    $customer->attempts = (int)$customer->attempts+1;
                    $customer->save(false);
                }
            }
            
            // sleep for 10 seconds
            sleep(10);
            
        }
        
    }
    
    public function actionResendMail()
    {
        $customers = Customer::find()
                ->where(['delivery_id'=>''])
                ->orWhere(['delivery_id'=>'0'])
                ->orWhere(['delivery_id'=>null])
                ->andWhere(['date_created'=>date('Y-m-d')])
                ->all();
        foreach($customers as $customer){
            if(!empty($customer->coupon->coupon_code) && !empty($customer->coupon->type)){
                $deliveryId = Customer::sendMail($customer->email,$customer->coupon->coupon_code, $customer->coupon->type);
                if($deliveryId){
                    $customer->delivery_id = $deliveryId;
                    $customer->delivery_status = Customer::MAIL_SEND;
                }else{
                    $customer->delivery_id = 2;
                    $customer->delivery_status = Customer::MAIL_NOT_SEND;
                }
                $customer->save(false);
            }
        }
    }
}
