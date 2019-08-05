<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;
use app\models\Category;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{

    public static function tableName(){
        return "Product";
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id'], 'integer'],
            [['name', 'photo_name', 'price', 'category_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // var_dump($params['id']);
        // exit;
        // $query = Product::find();

            // var_dump($params['id']);
            // exit;

            $user_id = Yii::$app->user->identity->user_id;
            $model_user = User::find()->where(['user_id'=>$user_id])->one();

            $model_shop = $model_user->shop;
            $categoryId = $model_shop->getCategorys()->where(['name' => $params['id']])->one()["category_id"];


            
            // $categoryId = Category::find()->where(['name' => $params['id']])->one()["category_id"];
            $query = ProductSearch::find()->where(['category_id' => $categoryId, 'active'=>true]);
            
        
            
        

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'product_id' => $this->product_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'photo_name', $this->photo_name]);

        return $dataProvider;
    }

    

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function allSearch($params)
    {
        $user_id = Yii::$app->user->identity->user_id;
        $model_user = User::find()->where(['user_id'=>$user_id])->one();
        $model_shop = $model_user->shop;

        if($params)
        {
            $categoryId = $model_shop->getCategorys()->where(['name' => $params['id']])->one()["category_id"];
            $query = $model_shop->getProducts()->where(["active"=>true]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);

            $this->load($params);

            if (!$this->validate()) {
                return $dataProvider;
            }

            $query->andFilterWhere([
                'product_id' => $this->product_id,
            ]);

            $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere([ 'price' => $this->price]);

           

            return $dataProvider;
        }
        else{
            $dataProvider = new ActiveDataProvider([
                'query' => $model_shop->getProducts()->where(["active"=>true]),
            ]);
            return $dataProvider;
        }
    }



}
