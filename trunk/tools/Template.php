<?php
/*
 * This file is part of uBook - a website to buy and sell books.
 * Copyright (C) 2010 Maikel Linke
*/

/*
 * Substitutes special template tags with given content.
*/
class Template {

    /**
     * The beginning of a tag to substitute. Tag example: {foo}
     */
    const TAG_START = '{';
    /**
     * The beginning of a tag to substitute. Tag example: {bar}
     */
    const TAG_END = '}';
    /**
     * Regular expression for subtemplates. Example:
     * <pre>
     *     <p>Normal Text</p>
     *     <ul>
     *         <!-- BEGIN item -->
     *         <li>{name}</li>
     *         <!-- END item -->
     *     </ul>
     * </pre>
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
        $search = '{' . $name . '}';
        $this->content = str_replace($search, $value, $this->content);
    }

    /**
     * Subtemplates are defined once and will appear in the result zero or more
     * times. Varying content and also sub-subpatterns are possible. In the
     * following example 'item' is the name of the subtemplate.
     * <pre>
     *     <p>Normal Text</p>
     *     <ul>
     *         <!-- BEGIN item -->
     *         <li>{name}</li>
     *         <!-- END item -->
     *     </ul>
     * </pre>
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
     * Returns the result.
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
        if (!isset($this->subTemplates[$name])) {
            $this->subTemplates[$name] = array();
        }

        $pattern = '/<!--\s*BEGIN\s+([a-z0-9_\-]+)\s*-->'
                . '(.*?)'
                . '<!--\s*END\s+\\1\s*-->/ims';
        /*
         * Using the following pattern modifiers:
         * i - caseless
         * m - multiline
         * s - dotall
        */
        preg_match_all($pattern, $this->content, $matches);
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