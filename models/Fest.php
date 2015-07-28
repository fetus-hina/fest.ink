<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fest".
 *
 * @property integer $id
 * @property string $name
 * @property integer $start_at
 * @property integer $end_at
 *
 * @property OfficialData[] $officialDatas
 * @property Team[] $teams
 * @property Color[] $colors
 */
class Fest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'start_at', 'end_at'], 'required'],
            [['name'], 'string'],
            [['start_at', 'end_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfficialDatas()
    {
        return $this->hasMany(OfficialData::className(), ['fest_id' => 'id'])
            ->orderBy('official_data.downloaded_at ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['fest_id' => 'id']);
    }

    public function getRedTeam()
    {
        return $this->hasOne(Team::className(), ['fest_id' => 'id'])
            ->andWhere('team.color_id = 1');
    }

    public function getGreenTeam()
    {
        return $this->hasOne(Team::className(), ['fest_id' => 'id'])
            ->andWhere('team.color_id = 2');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColors()
    {
        return $this->hasMany(Color::className(), ['id' => 'color_id'])->viaTable('team', ['fest_id' => 'id']);
    }
}