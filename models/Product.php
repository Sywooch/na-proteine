<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price', 'type_id', 'brand_id', 'count', 'info'], 'required'],
            [['price'], 'number'],
            [['type_id', 'brand_id', 'count', 'popularity'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['info'], 'string', 'max' => 500],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['type_id' => 'id']],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'id']],
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
            'price' => 'Price',
            'type_id' => 'Type',    //change label and error message
            'brand_id' => 'Brand',  //change label and error message
            'count' => 'Count',
            'popularity' => 'Popularity',
            'info' => 'Info',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }


    public static function getProducts($pagination)
    {
        $command = Yii::$app->db->createCommand("SELECT p.id ,p.name, p.price, b.brand AS brand, t.type
                                                 FROM product p
                                                 LEFT JOIN brand b ON b.id = p.brand_id
                                                 LEFT JOIN type t ON t.id = p.type_id
                                                 LIMIT $pagination->limit OFFSET $pagination->offset
                                                 ");  // TODO request by AR
        $products = $command->queryAll();

        return $products;
    }

    public static function findTypeBrand()
    {
        $brand = Yii::$app->request->get('brand');
        $type = Yii::$app->request->get('type');
        $brand = str_replace('_', ' ', $brand);
        $type = str_replace('_', ' ', $type);
        $command = Yii::$app->db->createCommand("SELECT p.id, p.price, p.name, b.brand AS brand, t.type
                                                FROM `product` p
                                                LEFT JOIN brand b ON b.id = p.brand_id
                                                LEFT JOIN type t ON t.id = p.type_id
                                                WHERE b.brand = '$brand'
                                                AND t.type = '$type'
                                               ");
        $products = $command->queryAll();
        return $products;
//                $sql = "SELECT p.name, p.price, b.name AS brand     TODO
//                 FROM product p
//                 LEFT JOIN brand b ON b.id = p.brand_id";
//        $products = Product::findBySql($sql)->all();

//        $products = $query
//            ->joinWith(['brand'])
//            ->select(['brand.brand AS brand'])
//            ->offset($pagination->offset)
//            ->limit($pagination->limit)
//            ->all();

//        $brand = LeftSide::find()
//            ->select('*')
//            ->all();
//
//        LeftSide::$name = 'type';
//
//        $type = LeftSide::find()
//            ->select('*')
//            ->all();
    }

    public static function findType()
    {
        $type = Yii::$app->request->get('type');
        $type = str_replace('_', ' ', $type);
        $command = Yii::$app->db->createCommand("SELECT p.id, p.price, p.name, b.brand AS brand, t.type
                                                FROM `product` p
                                                LEFT JOIN brand b ON b.id = p.brand_id
                                                LEFT JOIN type t ON t.id = p.type_id
                                                WHERE  t.type = '$type'
                                               ");
        $products = $command->queryAll();
        return $products;
    }

    public static function findBrand()
    {
        $brand = Yii::$app->request->get('brand');
        $brand = str_replace('_', ' ', $brand);
        $command = Yii::$app->db->createCommand("SELECT p.id, p.price, p.name, b.brand AS brand, t.type
                                                FROM `product` p
                                                LEFT JOIN brand b ON b.id = p.brand_id
                                                LEFT JOIN type t ON t.id = p.type_id
                                                WHERE b.brand = '$brand'
                                               ");
        $products = $command->queryAll();
        return $products;
    }

    public static function findByCookie(){
        if (isset($_COOKIE['product_id']) && $_COOKIE['product_id'] != '') {
            $ids = explode(", ", $_COOKIE['product_id']);
            return $ids;
        } else {
            return null;
        }
    }

    public static function findByID(array $ids = null)
    {
        if ($ids){
            $products = [];
            foreach ($ids as $id) {
                $command = Yii::$app->db->createCommand("SELECT p.id, p.price, p.name, b.brand AS brand, t.type
                                                    FROM `product` p
                                                    LEFT JOIN brand b ON b.id = p.brand_id
                                                    LEFT JOIN type t ON t.id = p.type_id
                                                    WHERE p.id = '$id'
                                               ");
                $product = $command->queryAll();
                $products[] = $product[0];
            }
            return $products;
        } else {
            return null;
        }
    }
}
