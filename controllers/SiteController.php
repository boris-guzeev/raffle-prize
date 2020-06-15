<?php

namespace app\controllers;

use app\models\Converter;
use app\models\Game;
use app\models\ItemPrize;
use app\models\Option;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Отображает главную страницу приложения
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
            $this->redirect('login');

        $itemPrizes = ItemPrize::findAll(['winner_id' => null]);

        $total = Option::findOne(['name' => 'total'])->value;
        $limit = Yii::$app->params['rules']['maxPrize'];
        $limit = $limit < $total ? $limit : $total;


        // отобразить в лейауте общую сумму текущего пользователя
        $sum = (new Query())->from('money_prizes')
            ->where(['winner_id' => Yii::$app->user->identity->id])
            ->sum('sum');
        $this->view->params['sum'] = $sum ? $sum : 0;

        // выйгранные предметы пользователя
        $userItems = ItemPrize::findAll(['winner_id' => Yii::$app->user->identity->id]);


        return $this->render('index', [
            'itemPrizes' => $itemPrizes,
            'userItems' => $userItems,
            'total' => $total,
            'limit' => $limit
        ]);
    }


    public function actionExchange($id)
    {
        Converter::toMoney($id);
    }


    public function actionConvert()
    {
        Converter::toPoints(Yii::$app->user->identity->id);
    }
    /**
     * Запуск игры
     * @return array информация об итоге
     * @throws \yii\db\Exception
     */
    public function actionPlay()
    {
        $result = (new Game)->play();
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['login']);
    }
}
