<?php

namespace app\commands;

use Yii;
use yii\helpers\Console;
use yii\console\Controller;

/**
 * This command runs a scraper object and returns the result.
 */
class ScraperController extends Controller
{
    /**
     * This command runs a scraper object and returns the result.
     */
    public function actionIndex()
    {
        $this->stdout("Starting to scrape ...\n", Console::FG_YELLOW);
        $data = Yii::$app->scraper->scrape();
        $this->stdout(json_encode($data));
        $this->stdout("\n");
    }
}
