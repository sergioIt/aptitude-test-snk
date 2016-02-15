<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 12.02.16
 * Time: 12:11
 */

namespace common\models;

use yii\data\ActiveDataProvider;
use yii\db\Query;

class TestSearch extends Test {

    public $userName;

    /* setup rules */
    public function rules() {
        return [
            /* your other rules */
            [['userName'], 'safe']
        ];
    }


    /**
     * setup search function for filtering and sorting
     * based on `fullUserName` field
     * @param $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {

        $query = Test::find();
        $query->joinWith('user');
             $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['fullUserName'] = [
            'asc' => ['user.name' => SORT_ASC],
            'desc' => ['user.name' => SORT_DESC],
        ];




        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

      /*  $this->addCondition($query, 'id', true);
        $this->addCondition($query, 'created', true);
        $this->addCondition($query, 'score', true);*/

       // $this->addCondition($query, 'name', true);

/*        $query->andFilterWhere([
            //... other searched attributes here
        ])
            // Here we search the attributes of our relations using our previously configured
            // ones in "TourSearch"
            ->andFilterWhere(['like', 'user.name', $this->user]);

        $query->andWhere('first_name LIKE "%' . $this->fullName . '%" ' .
            'OR last_name LIKE "%' . $this->fullName . '%"'
        );*/

        return $dataProvider;
    }

}