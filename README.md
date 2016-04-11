#Installation
Run the following commands to get the app installed and running.

- ``` git clone git@github.com:tochix/fruit-scraper.git  ```
- From the cloned app directory ``` composer update ``` 

# Usage
To run the command line scraper, issue the command
``` ./yii scraper ```

# Acceptance Test
There's an acceptance test case included. You can run it by:
- ``` cd tests/codeception ```
- ``` ../../vendor/bin/codecept bootstrap ```
- ``` ../../vendor/bin/codecept run acceptance tests/acceptance/ScraperCept.php ```

# Implementation
fruit-scraper was implemented using the [Yii2 PHP framework](http://www.yiiframework.com/). The web page scraper was built as a [component](https://github.com/tochix/fruit-scraper/blob/master/components/Scraper.php) which uses pre-defined [config](https://github.com/tochix/fruit-scraper/blob/master/config/scraper.php) to state items of interest for scraping. The component is loaded in the console's config [file](https://github.com/tochix/fruit-scraper/blob/master/config/console.php#L26), making it available at application start and usable from command line. A [test case](https://github.com/tochix/fruit-scraper/blob/master/tests/codeception/tests/acceptance/ScraperCept.php) was written to test the end to end functionality of the scraper.


This implementation has a dependency on [Goutte](https://github.com/FriendsOfPHP/Goutte) (used for web scraping) and [Codeception](http://codeception.com/) (used for the test cases).
