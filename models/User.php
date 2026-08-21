<?php
/**
 * User model class.
 * 
 * This is the ActiveRecord class for the "users" table in the database.
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $created_at
 * @property int|null $updated_at
 * 
 * @property Link[] $links
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'email', 'password'], 'required'],
            [['name', 'email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['password'], 'string', 'min' => 8],
            [['email_verified_at', 'created_at', 'updated_at'], 'integer'],
            [['remember_token'], 'string', 'max' => 100],
            [['password_confirmation'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios['login'] = ['email', 'password'];
        $scenarios['signup'] = ['name', 'email', 'password', 'password_confirmation'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'email_verified_at' => 'Email Verified At',
            'password' => 'Password',
            'remember_token' => 'Remember Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): ?IdentityInterface
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        // Not implemented for this application
        return null;
    }

    /**
     * Finds user by email address.
     * 
     * @param string $email
     * @return static|null
     */
    public static function findByEmail(string $email): ?static
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->remember_token;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->remember_token === $authKey;
    }

    /**
     * Validates password.
     * 
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Sets password hash.
     * 
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates remember token.
     */
    public function generateRememberToken(): void
    {
        $this->remember_token = Yii::$app->security->generateRandomString(10);
    }

    /**
     * Gets links relation.
     * 
     * @return yii\db\ActiveQuery
     */
    public function getLinks(): yii\db\ActiveQuery
    {
        return $this->hasMany(Link::class, ['user_id' => 'id']);
    }

    /**
     * Checks if user can access admin panel (always true for this app).
     * 
     * @return bool
     */
    public function canAccessPanel(): bool
    {
        return true;
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
                if (!$this->password) {
                    // Password already hashed in signup() method
                }
            } else {
                $this->updated_at = time();
            }
            return true;
        }
        return false;
    }

    /**
     * Logs in the user using email and password from model attributes.
     * 
     * @return bool whether user is logged in successfully
     */
    public function login(): bool
    {
        $user = self::findByEmail($this->email);
        if ($user && $user->validatePassword($this->password)) {
            return Yii::$app->user->login($user, $this->remember_token ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Signs up a new user with provided attributes.
     * 
     * @return bool whether user is created successfully
     */
    public function signup(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        
        $this->setPassword($this->password);
        $this->generateRememberToken();
        $this->email_verified_at = time();
        
        return $this->save(false);
    }
}
