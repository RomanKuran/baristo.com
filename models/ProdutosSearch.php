<?php

class ProdutosSearch extends Product
{
public $procuraglobal;
/**
 * @inheritdoc
 */
public function rules()
{
    return [
        [['id', 'promo', 'maisvendido'], 'integer'], 
        [['foto', 'categoria', 'nome', 'valor', 'descricao', 'dummy1', 'dummy2', 'dummy3', 'dummy4', 'dummy5', 'procuraglobal'], 'safe'],
        [['foto', 'categoria', 'nome', 'valor', 'dummy1', 'dummy2', 'dummy3', 'dummy4', 'dummy5'], 'string', 'max' => 255]     
    ];
}

/**
 * @inheritdoc
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
    $query = Product::find();

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    $this->load($params);

    if (!$this->validate()) {
        // uncomment the following line if you do not want to return any records when validation fails
        // $query->where('0=1');
        return $dataProvider;
    }

    $query->andFilterWhere([
        'id' => $this->id,
        'promo' => $this->promo,
        'maisvendido' => $this->maisvendido,
    ]);

    $query->andFilterWhere(['like', 'foto', $this->foto])
        ->andFilterWhere(['like', 'categoria', $this->categoria])
        ->andFilterWhere(['like', 'nome', $this->nome])
        ->andFilterWhere(['like', 'valor', $this->valor])
        ->andFilterWhere(['like', 'descricao', $this->descricao])
        ->andFilterWhere(['like', 'dummy1', $this->dummy1])
        ->andFilterWhere(['like', 'dummy2', $this->dummy2])
        ->andFilterWhere(['like', 'dummy3', $this->dummy3])
        ->andFilterWhere(['like', 'dummy4', $this->dummy4])
        ->andFilterWhere(['like', 'dummy5', $this->dummy5]);

    return $dataProvider;
}?>