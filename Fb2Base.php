<?php

namespace stihi;

use DOMDocument;

/**
 * Строит и сохраняет в fb2
 */
class Fb2Base
{
    /**
     * @var string
     */
    private $fb2Filename = '';

    /**
     * @var \DOMDocument
     */
    private $doc;

    /**
     * @var \DOMElement
     */
    private $root;

    /**
     * @var \DOMElement
     */
    private $description;

    /**
     * @var \DOMElement
     */
    private $body;

    /**
     * Fb2Base constructor.
     * @param string $fb2Filename
     */
    public function __construct($fb2Filename)
    {
        $this->fb2Filename = $fb2Filename;
        $this->init();
    }

    private function init()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->root = $this->doc->createElementNS('http://www.gribuser.ru/xml/fictionbook/2.0', 'FictionBook');
        $this->root->setAttribute('xmlns:l', 'http://www.w3.org/1999/xlink');
        $this->doc->appendChild($this->root);
        $this->description = $this->doc->createElement('description');
        $this->root->appendChild($this->description);
        $this->body = $this->doc->createElement('body');
        $this->root->appendChild($this->body);
    }

    public function save()
    {
        return $this->doc->save($this->fb2Filename);
    }

    public function appendTitle(string $value)
    {
        $el = $this->doc->createElement('title', $value);
        $this->body->appendChild($el);
    }

    public function appendSubTitle(string $value)
    {
        $el = $this->doc->createElement('subtitle', $value);
        $this->body->appendChild($el);
    }

    public function appendParagraph(string $value)
    {
        $el = $this->doc->createElement('p', $value);
        $this->body->appendChild($el);
    }

    public function appendEmptyLine()
    {
        $el = $this->doc->createElement('empty-line');
        $this->body->appendChild($el);
    }
}

$d = new Fb2Base(__DIR__ . '/fb2test.fb2');
$d->appendTitle('Заголовок');
$d->appendSubTitle('Подзаголовок первый');
$d->appendParagraph('Стальные птицеподобные лапы спускающегося по склону карьера боевого робота бесшумно вонзались в слежавшийся грунт, выбивая из опустошённой породы облака пыли. В безвоздушной атмосфере исполинского астероида не было слышно лязга стальных подвижных частей и зловещего гула силовой установки, и со стороны могло показаться, что появляющиеся из-за гребня смертельно опасные боевые механизмы бесплотны, подобно объектам голографической рекламы. Ещё секунда, и идеально воссозданные изображения поплывут по воздуху, отыгрывая рекламный ролик, после чего бесследно растворятся, и в застывшем без движения горно-обогатительном комбинате вновь воцарится пустота… Но боевые роботы исчезать не собирались. В считаные секунды их количество возросло до десяти, и взявшие карьер в кольцо зловещие механизмы ....');
$d->appendSubTitle('Подзаголовок второй');
$d->appendParagraph('Стальные птицеподобные лапы ');
$d->appendParagraph('спускающегося по склону карьера');
$d->appendParagraph('Стальные птицеподобные лапы спускающегося по склону карьера боевого робота бесшумно вонзались в слежавшийся грунт, выбивая из опустошённой породы облака пыли. В безвоздушной атмосфере исполинского астероида не было слышно лязга стальных подвижных частей и зловещего гула силовой установки, и со стороны могло показаться, что появляющиеся из-за гребня смертельно опасные боевые механизмы бесплотны, подобно объектам голографической рекламы. Ещё секунда, и идеально воссозданные изображения поплывут по воздуху, отыгрывая рекламный ролик, после чего бесследно растворятся, и в застывшем без движения горно-обогатительном комбинате вновь воцарится пустота… Но боевые роботы исчезать не собирались. В считаные секунды их количество возросло до десяти, и взявшие карьер в кольцо зловещие механизмы ....');
$d->save();