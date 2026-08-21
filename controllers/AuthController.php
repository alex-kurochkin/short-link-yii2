<?php
/**
 * AuthController class
 * 
 * Handles user authentication (login, register, logout).
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\User;

class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays the login page
     * 
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new User(['scenario' => 'login']);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                return $this->goBack(['/admin']);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Displays the registration page
     * 
     * @return string|Response
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Yii::$app->user->login($model);
                return $this->goBack(['/admin']);
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user
     * 
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
