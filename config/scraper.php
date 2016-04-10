<?php

return [
    'class' => 'app\components\Scraper',
    'config' => [
        'endPoint' => 'http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html',
        'domNodes' => [
            'itemNodes' => 'ul.productLister > li',
            'jumpLink' => 'div.productInfo > h3 > a',
            'itemAttributes' => [
                'title' => ['path' => 'div.productTitleDescriptionContainer > h1'],
                'size' => ['path' => '', 'processor' => 'getContentSize'],
                'unit_price' => ['path' => 'p.pricePerUnit'],
                'description' => ['path' => 'div.productText > p'],
            ],
            'processor' => 'sumUp',
        ],
    ],
];
