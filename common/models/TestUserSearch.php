<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 12.02.16
 * Time: 12:11
 */

namespace common\models;

use yii\data\ActiveDataProvider;

class TestUserSearch extends Test {

    public $fullName;

    /* setup rules */
    public function rules() {
        return [
            /* your other rules */
            [['name'], 'safe']
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

        $query = TestUser::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['attributes' => ['name']],
        ]);





        //$this->addCondition($query, 'id');
        //$this->addCondition($query, 'name', true);
      //  $this->addCondition($query, 'surename', true);
     //   // $this->addCondition($query, 'country_id');

        /* Add your filtering criteria */

        // filter by person full name
      /*  $query->andWhere('name LIKE "%' . $this->fullUserName . '%" ' .
            'OR surename LIKE "%' . $this->fullUserName . '%"'
        );*/

        // filter by country name
        /*  $query->joinWith(['country' => function ($q) {
              $q->where('tbl_country.country_name LIKE "%' . $this->countryName . '%"');
          }]);*/

        return $dataProvider;
    }

}