<?php
/**
 * Click model class.
 * 
 * This is the ActiveRecord class for the "clicks" table in the database.
 * 
 * @property int $id
 * @property int $link_id
 * @property string $ip_address
 * @property int|null $clicked_at
 * 
 * @property Link $link
 */

namespace app\models;

use yii\db\ActiveRecord;

class Click extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'clicks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['link_id', 'ip_address', 'clicked_at'], 'required'],
            [['link_id', 'clicked_at'], 'integer'],
            [['ip_address'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'link_id' => 'Link ID',
            'ip_address' => 'IP Address',
            'clicked_at' => 'Clicked At',
        ];
    }

    /**
     * Gets link relation.
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getLink(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Link::class, ['id' => 'link_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            // clicked_at is set explicitly, no auto-timestamps
            return true;
        }
        return false;
    }
}
