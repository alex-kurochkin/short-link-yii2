<?php
/**
 * ShortLinkController class
 * 
 * Handles short link creation and redirection.
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Link;
use app\models\Click;
use app\services\CodeGenerator;
use app\services\CodeCheckers\EloquentCodeUniquenessChecker;

class ShortLinkController extends Controller
{
    /**
     * Redirects to the original URL by code
     * 
     * @param string $code
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionRedirect(string $code): Response
    {
        $link = Link::find()->where(['code' => $code])->one();
        
        if (!$link) {
            throw new NotFoundHttpException('Ссылка не найдена');
        }

        // Record click
        $click = new Click();
        $click->link_id = $link->id;
        $click->ip_address = Yii::$app->request->userIP;
        $click->clicked_at = time();
        $click->save(false);

        return $this->redirect($link->original_url);
    }

    /**
     * Creates a new short link
     * 
     * @return Response|string
     */
    public function actionCreate()
    {
        $model = new Link(['scenario' => 'create']);
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                try {
                    $checker = new EloquentCodeUniquenessChecker();
                    $generator = new CodeGenerator($checker);
                    $code = $generator->generate();
                    
                    $model->code = $code;
                    $model->user_id = Yii::$app->user->id;
                    
                    if ($model->save(false)) {
                        Yii::$app->session->setFlash('success', 'Ссылка создана: ' . $model->short_url);
                        return $this->refresh();
                    }
                } catch (\RuntimeException $e) {
                    $model->addError('original_url', $e->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
