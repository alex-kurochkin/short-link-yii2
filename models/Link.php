<?php
/**
 * Link model class.
 * 
 * This is the ActiveRecord class for the "links" table in the database.
 * 
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property string $original_url
 * @property int|null $created_at
 * @property int|null $updated_at
 * 
 * @property User $user
 * @property Click[] $clicks
 * @property string $short_url
 * @property int $clicks_count
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Link extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'links';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'code', 'original_url'], 'required'],
            [['user_id'], 'integer'],
            [['code'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['original_url'], 'url'],
            [['original_url'], 'string', 'max' => 2048],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'code' => 'Code',
            'original_url' => 'Original URL',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets user relation.
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets clicks relation.
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getClicks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Click::class, ['link_id' => 'id']);
    }

    /**
     * Getter for short_url attribute (accessor).
     * 
     * @return string
     */
    public function getShortUrl(): string
    {
        return \Yii::$app->request->hostInfo . '/' . $this->code;
    }

    /**
     * Getter for clicks_count attribute (accessor).
     * 
     * @return int
     */
    public function getClicksCount(): int
    {
        return $this->getClicks()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = time();
                $this->updated_at = time();
            } else {
                $this->updated_at = time();
            }
            return true;
        }
        return false;
    }
}
