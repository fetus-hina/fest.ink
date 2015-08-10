<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "timezone".
 *
 * @property integer $id
 * @property string $zone
 */
class Timezone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timezone';
    }

    public static function find()
    {
        return parent::find()->orderBy('{{timezone}}.[[zone]] ASC');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zone'], 'required'],
            [['zone'], 'string', 'max' => 64],
            [['zone'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'zone' => 'Zone',
        ];
    }
}
