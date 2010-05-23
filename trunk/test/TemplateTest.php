<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

require_once 'PHPUnit/Framework.php';
require_once 'tools/Template.php';


class TemplateTest extends PHPUnit_Framework_TestCase {

    function testFromFile() {
        try {
            Template::fromFile('view/page.html');
        } catch (Exception $ex) {
            $this->fail($ex);
        }
    }

    function testFromFileFailing() {
        try {
            Template::fromFile('/');
        } catch (Exception $expected) {
            // This should happen
            return;
        }
        $this->fail('Exception expected.');
    }

    function testTemplate() {
        $text = 'This is just text.';
        $t = new Template($text);
        $this->assertEquals($text, $t->result());
    }

    function testAssign() {
        $text = 'This is just text.';
        $tText = "This is 'a template'.";
        $t = new Template($tText);
        $t->assign('a template', 'just text');
        $this->assertEquals($text, $t->result());
    }

    function testAssignArray() {
        $expected = 'This is new text, from hell.';
        $tText = "This is 'a template', 'not more'.";
        $t = new Template($tText);
        $t->assign('a template', 'just text');
        $a = array('a template' => 'new text', 'not more' => 'from hell');
        $t->assignArray($a);
        $this->assertEquals($expected, $t->result());
    }

    function testAddSubtemplate() {
        $tText = 'Hello World.'
                . '<!-- BEGIN subtemplate -->'
                . " I love 'you'!"
                . '<!-- END subtemplate -->'
                . '<!-- BEGIN subNotToUse -->'
                . ' Ayayayayyyy!'
                . '<!-- END subNotToUse -->';
        $expText = 'Hello World.';
        $t = new Template($tText);
        $s = $t->addSubtemplate('subtemplate');
        $expSubText = " I love 'you'!";
        $this->assertEquals($expSubText, $s->result());
        $expText .= $expSubText;
        $this->assertEquals($expText, $t->result());
        $s2 = $t->addSubtemplate('subtemplate');
        $s2->assign('you', 'peace');
        $expText .= ' I love peace!';
        $this->assertEquals($expText, $t->result());
    }

    function testAddSubtemplateFailing() {
        $tText = 'Hello World.';
        $t = new Template($tText);
        try {
            $t->addSubtemplate('kokoloris');
        } catch (Exception $ex) {
            // this should happen
            return;
        }
        $this->fail('Exception expected.');
    }

    function testSubstitutionInSubtemplate() {
        $tText = 'Hello World.'
                . '<!-- BEGIN subtemplate -->'
                . " I love 'you'!"
                . '<!-- END subtemplate -->';
        $expText = 'Hello World.';
        $t = new Template($tText);
        $t->assign('you', 'bugs');
        $this->assertEquals($expText, $t->result());
    }

    function testAmbiguousSubtemplates() {
        $tText = 'Hello World.'
                . '<!-- BEGIN subtemplate -->'
                . 'a text'
                . '<!-- END subtemplate -->'
                . '<!-- BEGIN subtemplate -->'
                . 'a different text'
                . '<!-- END subtemplate -->';
        try {
            new Template($tText);
        } catch (Exception $expected) {
            $this->assertNotNull($expected->getMessage());
            return;
        }
        $this->fail('An invalid template was accepted.');
    }

}
?>