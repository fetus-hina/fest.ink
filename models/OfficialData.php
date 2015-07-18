<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "official_data".
 *
 * @property integer $id
 * @property integer $fest_id
 * @property string $sha256sum
 * @property integer $downloaded_at
 *
 * @property Fest $fest
 * @property OfficialWinData[] $officialWinDatas
 * @property Color[] $colors
 */
class OfficialData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'official_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fest_id', 'sha256sum', 'downloaded_at'], 'required'],
            [['fest_id', 'downloaded_at'], 'integer'],
            [['sha256sum'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fest_id' => 'Fest ID',
            'sha256sum' => 'Sha256sum',
            'downloaded_at' => 'Downloaded At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFest()
    {
        return $this->hasOne(Fest::className(), ['id' => 'fest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfficialWinDatas()
    {
        return $this->hasMany(OfficialWinData::className(), ['data_id' => 'id']);
    }

    public function getRed()
    {
        return $this->hasOne(OfficialWinData::className(), ['data_id' => 'id'])
            ->andWhere('official_win_data.color_id = 1');
    }

    public function getGreen()
    {
        return $this->hasOne(OfficialWinData::className(), ['data_id' => 'id'])
            ->andWhere('official_win_data.color_id = 2');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColors()
    {
        return $this->hasMany(Color::className(), ['id' => 'color_id'])->viaTable('official_win_data', ['data_id' => 'id']);
    }
}
