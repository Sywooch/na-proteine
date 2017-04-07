<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "comments".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $product_id
 * @property string $comment
 *
 * @property Product $product
 * @property User $user
 */
class Comments extends \yii\db\ActiveRecord
{
    public $verifyCode;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comments';
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
    public function rules()
    {
        return [
            ['comment', 'required'],
            ['verifyCode', 'captcha', 'captchaAction' => 'site/captcha'],
//            [['user_id', 'product_id'], 'required'],
//            [['user_id', 'product_id'], 'integer'],
//            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
//            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'verifyCode' => 'Verification Code',
            'created_at' => 'Created At',
            'product_id' => 'Product ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function addComment($user_id, $product_id)
    {
        if ($this->validate()) {

            $comment = new Comments();
            $comment->user_id = $user_id;
            $comment->product_id = $product_id;
            $comment->comment = $this->comment;

            if ($comment->save(false)) {    //false because of captcha is regenerate https://github.com/yiisoft/yii2/issues/6115
                return true;
            }

        }
        return false;

    }
}