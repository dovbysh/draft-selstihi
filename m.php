<?php


namespace stihi;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

require_once(__DIR__ . '/vendor/autoload.php');
require __DIR__ . '/Fb2Base.php';

class Main
{
    /**
     * @var RemoteWebDriver
     */
    private $driver;

    /**
     * @var string
     */
    private $fn = '';

    /**
     * @var Fb2Base
     */
    private $fb2;

    /**
     * @var string[]
     */
    private $poemTitles = [];

    public function __construct()
    {
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create($host, $capabilities, 5000);
        $this->fn = __DIR__ . '/stihi.fb2';
        $this->fb2 = new Fb2Base($this->fn);
    }

    public function start()
    {
        for ($page = 0; $page <= 350; $page += 50) {
            $this->driver->get('http://stihi.ru/avtor/youzhakoff&s=' . $page);
            $this->fb2->appendTitle('Влад Южаков стихи.');
            $cookies = $this->driver->manage()->getCookies();
            print_r($cookies);


            if ($page === 0) {
                $first = 'Произведения, не вошедшие в сборники';

                $this->fb2->appendEmptyLine();
                $this->fb2->appendTitle($first);
                $this->fb2->appendEmptyLine();

                $read[] = $first;
            }

            $this->readSection();
        }

        $sections = $this->driver->findElements(
            WebDriverBy::xpath('//div[@id="bookheader"]/a')
        );

        foreach ($sections as $i => $s) {
            $section = $this->driver->findElements(
                WebDriverBy::xpath('//div[@id="bookheader"]/a')
            )[$i];
            $title = $section->getText();
            if (in_array($title, $read)) {
                continue;
            }
            print "=========\n$title\n==\n";
            $this->fb2->appendTitle($title);
            $section->click();
            $this->driver->wait();
            $this->readSection();
            $this->driver->navigate()->back();
            $this->driver->wait();
            $read[] = $title;

        }

        $this->fb2->save();
    }

    public function __destruct()
    {
        if (!is_null($this->driver)) {
            $this->driver->quit();
        }
    }


    private function readSection()
    {
        $links = $this->driver->findElements(
            WebDriverBy::xpath('//a[@class="poemlink"]')
        );
        foreach ($links as $i => $link) {
            if ($i > 0) {
                $link = $this->driver->findElements(
                    WebDriverBy::xpath('//a[@class="poemlink"]')
                )[$i];
            }

            $this->readStih($link, $i);
        }
    }

    private function readStih(RemoteWebElement $link, int $i)
    {
        $title = $link->getText();
        if (in_array($title, $this->poemTitles)) {
            return;
        }

        $this->fb2->appendSubTitle($title);
        print $i . " -- " . $title . "\n";
        $link->click();
        $this->driver->wait();
        $text = $this->driver->findElement(WebDriverBy::xpath('//div[@class="text"]'))->getText();
        print "text:\n" . $text . "\n";
        $lines = preg_split('~\n~', $text);
        foreach ($lines as $p) {
            $this->fb2->appendParagraph($p);
        }
        $this->fb2->appendEmptyLine();
        $this->fb2->appendEmptyLine();
        $this->poemTitles[] = $title;
        $this->driver->navigate()->back();
        $this->driver->wait();
    }
}

(new Main())->start();