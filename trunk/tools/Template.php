<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

/*
 * Substitutes special template tags with given content.
 *
 * This is just another template class for HTML/XML templates. BUT it is the
 * result of studying several other template engines and tries to merge all
 * advantages. This text explains you two things:
 *
 * 1. how to use, some examples
 * 2. why this way, good reasons for the syntax and the API
 *
 * == How To Use ==
 *
 * Begin to create pure HTML. It could be like this:
 *
 * <pre>
 *     <p>Hello, this is normal text. But it is also possible to insert
 *        variable text here. For example the time: 'time'.</p>
 *     <p>Okay, this is easy. It's just a string replacement. But now follows
 *        a subtemplate. It allows you to repeat some code over and over again,
 *        everytime with different content. For example a list of names:</p>
 *     <ul>
 *         <!-- BEGIN item -->
 *         <li>'name'</li>
 *         <!-- END item -->
 *     </ul>
 * </pre>
 *
 * Now we have the template. Let's write code to fill it with data.
 *
 * <?php
 *     $template = new Template($theHtmlCodeAsString);
 *     // or
 *     $template = Template::fromFile($filenameOfTemplate);
 *
 *     // replace a variable with a value
 *     $template->assign('time', time());
 *
 *     // use the subtemplate to generate three list items
 *     $sub = $template->addSubtemplate('item');
 *     $sub->assign('name', 'Andrea');
 *     $sub = $template->addSubtemplate('item');
 *     $sub->assign('name', 'Andy');
 *     $sub = $template->addSubtemplate('item');
 *     $sub->assign('name', 'Anna');
 *
 *     // ready...
 *     echo $template->result();
 * ?>
 *
 * == Why This Way ==
 *
 * This class was written for a simple project. It's just one class, not a whole
 * framework with caching and so on. Providing only three methods it supports
 * everything, that a simple template engine should support. But not more.
 * Keep it simple.
 *
 * Why do you use single quotes for variables?
 * - There are three reasons:
 * 1. It's easy to write on most keyboards.
 * 2. Valid HTML will stay valid, when you insert single quotes in the text.
 * It's also no problem to insert single quotes into a double quoted parameter
 * value.
 * 3. You can prevent unintended replacements by encoding the user data via
 * htmlentities() before inserting into the template (see example below).
 *
 * Besides, if you like another syntax, just change the constants in the script.
 * It's free software.
 *
 * Why don't you provide an assign-method for arrays?
 * - It's no good idea to store everything in an array. Assigning one variable
 * to the template is not more complicated than a new entry in an array.
 * Perhaps you have a full array of data from mysql_fetch_array(). But you
 * should not insert this into a template. Normally you have more data in there
 * than you want to present the user. And you have to encode the user data to
 * prevent code injection. So depending on your application, perhaps you can do
 * something like this:
 * <?php
 *     $arr = mysql_fetch_array($result);
 *     foreach ($arr as $key => $value) {
 *         $encoded = htmlentities($value, ENT_QUOTES, 'UTF-8');
 *         $template->assign($key, $encoded);
 *     }
 * ?>
 * But this depends on your application. Better to write your own code based on
 * minimalistic work of others than including huge, complex frameworks and using
 * only one percent of it.
*/
class Template {

    /**
     * The beginning of a tag to substitute. Tag example: 'foo'
     */
    const TAG_START = "'";
    /**
     * The beginning of a tag to substitute. Tag example: 'bar'
     */
    const TAG_END = "'";
    /**
     * Regular expression for subtemplates.
     *
     * It uses the following pattern modifiers:
     * i - caseless
     * m - multiline
     * s - dotall
     */
    const SUB_PATTERN = '/<!--\s*BEGIN\s+([a-z0-9_\-]+)\s*-->(.*?)<!--\s*END\s+\\1\s*-->/ims';

    private $content;
    private $subTemplates = array();
    private $subTemplateKeys = array();
    private $subTemplateStrings = array();

    /**
     * Loads the content of a template.
     * @param string $filename path to the template
     */
    public static function fromFile($filename) {
        $content = file_get_contents($filename);
        if ($content === false) {
            throw new Exception("Could not read template file: " + $filename);
        }
        return new Template($content);
    }

    /**
     * Creates a new instance.
     * @param string $content
     */
    public function __construct($content) {
        $this->content = $content;
        $this->initSubtemplates();
    }

    /**
     * Replaces a template tag with the given value.
     * @param string $name tag identifier in the template
     * @param string $value new value in the document
     */
    public function assign($name, $value) {
        $search = self::TAG_START . $name . self::TAG_END;
        $this->content = str_replace($search, $value, $this->content);
    }

    /**
     * Subtemplates are defined once and will appear in the result zero or more
     * times. Varying content and also sub-subtemplates are possible.
     * @param string $name identifyer of the subtemplate
     * @return Template a full template instance to customize this subtemplate
     */
    public function addSubtemplate($name) {
        if (!isset($this->subTemplateKeys[$name])) {
            throw new Exception('This subtemplate is not available: ' . $name);
        }
        $sub = new Template($this->subTemplateStrings[$name]);
        $this->subTemplates[$name][] = $sub;
        return $sub;
    }

    /**
     * Returns the parsed result.
     * @return string actual result with all given replacements
     */
    public function result() {
        $c = $this->content;
        foreach ($this->subTemplateKeys as $name => $key) {
            $replace = '';
            foreach ($this->subTemplates[$name] as $n => $sub) {
                $replace .= $sub->result();
            }
            $c = str_replace($key, $replace, $c);
        }
        return $c;
    }

    private function initSubtemplates() {
        preg_match_all(self::SUB_PATTERN, $this->content, $matches);
        for ($i = 0; $i < sizeof($matches[0]); $i++) {
            $key = $matches[0][$i];
            $name = $matches[1][$i];
            $substring = $matches[2][$i];
            $this->subTemplateKeys[$name] = $key;
            $this->subTemplateStrings[$name] = $substring;
            $this->subTemplates[$name] = array();
        }
    }

}
?>