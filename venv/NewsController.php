<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 19.05.2020
 * Time: 11:59
 */

namespace app\controllers;

use yii\web\Controller;
use app\models\NewsPromo;
use app\models\News;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
class NewsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            'statistics' => [
                'class' => \Klisl\Statistics\AddStatistics::class,
                'actions' => ['index', 'item'],
            ],
        ];
    }

    public function actionIndex()
    {

        $news = News::find()->orderBy(['created' => SORT_DESC])->asArray()->all();

        $news_promo = NewsPromo::find()->select('promo.*, promo_id')->joinWith('promo')->asArray()->all()[0];
        unset($news_promo['promo']);

        return $this->render('index', [
                'title' => 'НОВОСТНАЯ ЛЕНТА',
                'news' => $news,
                'news_promo' => $news_promo,
            ]);
    }

    public function actionItem($id) {

        $new_item = News::findOne(['id' => $id]);

        if( ! $new_item->custom_page_path) {
            $new_item = News::find()->joinWith('newsBlocks')->where(['news_id' => $id])->asArray()->all()[0];
        }


        $this->addViewing($id);
        return $this->render('item', ['item' => $new_item,]);
    }

    private function addViewing($id){
        $model = News::findOne(['id' => $id]);
        $model->count_view++;
        if(! $model->save()){
            return false;
        }
        return true;
    }

}