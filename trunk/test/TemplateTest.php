<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright © 2010 Maikel Linke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'PHPUnit/Framework.php';
require_once 'text/Template.php';


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

    function testAddOnelineSubtemplate() {
        $tText = '<a href="./">'
                . '<!-- begin author -->author: <!-- end author -->'
                . 'title</a>';
        $expText = '<a href="./">author: title</a>';
        $t = new Template($tText);
        $t->addSubtemplate('author');
        $this->assertEquals($expText, $t->result());
    }

    function testAddUglySubtemplate() {
        $subText = ' Define your own style!';
        $tText = "<!--\tBEGIN    subtemplate-->"
                . $subText
                . "<!--END \tsubtemplate -->";
        $t = new Template($tText);
        $t->addSubtemplate('subtemplate');
        $this->assertEquals($subText, $t->result());
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
