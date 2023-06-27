<?php

require_once './vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Ztobs\Gec\CaptchaSolver;

$host = 'http://selenium:4444';
 
$capabilities = DesiredCapabilities::chrome();
$webDriver = RemoteWebDriver::create($host, $capabilities);

try {
    $url = 'https://service2.diplo.de/rktermin/extern/appointment_showMonth.do?locationCode=lago&realmId=347&categoryId=564';
    $webDriver->manage()->window()->maximize();
    $webDriver->get($url);

    sleep(1);
    $currentPage = $webDriver->getTitle();
    echo 'Page Title: ' . $webDriver->getTitle() . "\n\n";

    $selector = WebDriverBy::cssSelector('#appointment_captcha_month > div > captcha > div');

    $captcha = $webDriver->findElement($selector);
    $captcha->takeElementScreenshot('captcha.png');

    $base64 = explode("'", $captcha->getAttribute('style'))[1];
    $base64 = str_replace('data:image/jpg;base64,', '', $base64);

    $captchaSolver = new CaptchaSolver();
    $result = $captchaSolver->createCaptchaTask($base64);

    echo 'Result: ' . $result . "\n\n";

    $element = $webDriver->findElement(WebDriverBy::name('captchaText'));
    $element->sendKeys($result);
    $element->submit();
    
    sleep(3);


    // page 2
    $element = $webDriver->findElement(WebDriverBy::id('content'));
    if (strpos($element->getText(), 'there are no appointments available at this time') !== false) {
        echo 'No appointments \n';
    }

    // $webDriver->takeScreenshot('img.png');


    $webDriver->quit();
} catch (\Throwable $th) {
    print $th->getMessage();
    $webDriver->quit();
}




