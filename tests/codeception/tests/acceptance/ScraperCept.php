<?php

namespace app\tests\codeception\acceptance;

use \app\components\Scraper;
use \Yii;

/* @var $scenario Codeception\Scenario */

$I = new \AcceptanceTester($scenario);
$I->wantTo('ensure that product scraping works');
$I->amOnPage('http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html');
$I->see('Ripe & ready | Sainsbury\'s');

$I->seeNumberOfElements('ul.productLister > li', [1, 20]);
$itemTitles = $I->grabMultiple('ul.productLister > li div.productInfo > h3 > a');
$itemPrices = $I->grabMultiple('ul.productLister > li p.pricePerUnit');

$processedItemPrices = array_map(function ($rawPrice) {
	if (preg_match('#\d+(?:\.\d{1,2})?#', $rawPrice, $match)) {
		return $match[0];
	}

	return 0;
}, $itemPrices);

$scrapedItems = \Yii::$app->scraper->scrape();
$cummSum = 0;
\PHPUnit_Framework_Assert::assertEquals(count($itemTitles), count($scrapedItems) - 1);

foreach ($scrapedItems as $idx => $scrapedItem) {
	if (!is_array($scrapedItem)) {
		continue;
	}

	$cummSum += $processedItemPrices[$idx];
	\PHPUnit_Framework_Assert::assertEquals(trim($scrapedItem['title']), trim($itemTitles[$idx]));
	\PHPUnit_Framework_Assert::assertEquals($scrapedItem['unit_price'], $processedItemPrices[$idx]);
}

\PHPUnit_Framework_Assert::assertEquals($cummSum, $scrapedItems['total']);