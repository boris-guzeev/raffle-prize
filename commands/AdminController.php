<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

class AdminController
{
    /**
     * Здесь должна быть отправка, например, через Курл запрос в стороний сервис банка
     * @return int Exit code
     */
    public function actionSend($message)
    {
        echo $message . "\n";

        return ExitCode::OK;
    }
}