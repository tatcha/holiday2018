<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "setting".
 *
 * @property integer $id
 * @property string $setting_key
 * @property string $setting_value
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['setting_key', 'setting_value'], 'required'],
            [['setting_key', 'setting_value'], 'string', 'max' => 255],
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
            'setting_key' => 'Setting Key',
            'setting_value' => 'Setting Value',
        ];
    }
    
    public static function getValue($key)
    {
        $setting = Setting::findOne(['setting_key'=>$key]);
        if($setting !== null){
            return $setting->setting_value;
        }
        return false;
    }
    
    public static function setValue($key,$value)
    {
        $setting = Setting::findOne(['setting_key'=>$key]);
        if($setting === null){
            $setting = new Setting();
        }
        $setting->setting_key = $key;
        $setting->setting_value = $value;
        if($setting->save()){
            return true;
        }
        return false;
    }
}
