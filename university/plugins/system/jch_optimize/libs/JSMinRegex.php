<?php

namespace JchOptimize;

/**
 * This is a regular expressions based implementation of the JSMin algorithim as decsribed on 
 * Douglas Crockford's page at http://www.crockford.com/javascript/jsmin.html in PHP and also 
 * guided by the PHP port written by  Ryan Grove <ryan@wonko.com>
 * 
 * This was written to provide a PHP tool to minify javascript but with an emphasis on speed, 
 * in particular for tools that want to minify javascript on the fly such as http://www.jch-optimize.net. 
 * Based on independent comparison tests, this library consistently returns the same results as JSMin.php 
 * but on an average of 20 times faster.
 * 
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 * 
 *  -- 
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 * 
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright (c) 2002, Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright (c) 2014, Samuel Marshall <sdmarshall73@gmail.com> (JSMinRegex.php)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * 
 */
class JSMinRegex
{

        protected $options = array();

        public static function minify($js, $options = array())
        {
                $oMinifyJs = new JSMinRegex($options);
                return $oMinifyJs->process($js);
        }

        private function __construct($options)
        {
                $this->options = $options;
        }

        protected function process($js)
        {
                //replace carriage return with line feeds
                $js = str_replace(array("\r\n","\r"), "\n", $js);

                //convert all other control characters to space
                $js = preg_replace('#[^\S\n ]#', ' ', $js);


                //regex for double quoted strings
                $s1 = '"(?>(?:\\\\.)?[^\\\\"\n]*+)+?"';

                //regex for single quoted string
                $s2 = "'(?>(?:\\\\.)?[^\\\\'\n]*+)+?'";

                //regex for block comments
                $b = '/\*(?>[^/\*]++|//|\*(?!/)|(?<!\*)/)*+\*/';

                //regex for line comments
                $c = '//[^\n]*+';

                //regex for regexp literals
                $x = '(?:(?:(?<=(?<=[(,=:[!&|?+\-~*{;]|^|\n)|(?<=[(,=:[!&|?+\-~*{;]|^|\n)[ ])'
                        . '(?<x>/(?![*/])(?:(?(?=\\\\)\\\\.|\[(?>(?:\\\\.)?[^\\\\\]\n]*+)+?\])?[^\\\\/\n\[]*+)+?/))'
                        . '|(?:(?<=case |else |in |return |typeof )(?&x)))';


                //remove comments
                $js = preg_replace_callback("#(?(?=[\"'/])(?:{$s1}|{$s2}|{$x}|{$b}|{$c})|[\"'/])#", array($this, '_commentsCB'), $js);

                //replace runs of whitespace with single space or linefeed
                $js = preg_replace_callback("#(?(?=[\"'/\s])(?:\s*\n+\s*|[ ]{2,}|{$s1}|{$s2}|{$x}|{$b})|[\"'/\s])#", array($this, '_singleCB'), $js);


                //regex for regexp literals, preserving space after keywords
                $x2 = '(?:(?:(?<=(?<=[(,=:[!&|?+\-~*{;]|^|\n)|(?<=[(,=:[!&|?+\-~*{;]|^|\n)[ ])'
                        . '(?<x>/(?![*/])(?:(?(?=\\\\)\\\\.|\[(?>(?:\\\\.)?[^\\\\\]\n]*+)+?\])?[^\\\\/\n\[]*+)+?/))'
                        . '|(?:(?<=case|else|in|return|typeof)[ ](?&x)))';
                //regex for removing spaces
                //remove space except when a space is preceded and followed by a non-ASCII character or by an ASCII letter or digit, 
                //or by one of these characters \ $ _  ...ie., all ASCII characters except those listed.
                $sp = '(?:(?<=(?<s>["\'!\#%&`()*.,\/:;<=>?@\[\]\^{}|~])) | (?=(?&s))|(?<=(?<p>[+\-])) (?!\k<p>)|(?<=[^+\-]) (?!([^+\-])))';

                //regex for removing linefeeds
                //remove linefeeds except if it precedes a non-ASCII character or an ASCII letter or digit or one of these 
                //characters: \ $ _ [ ( + - and if it follows a non-ASCII character or an ASCII letter or digit or one of these 
                //characters: \ $ _ ] ) + - " ' ...ie., all ASCII characters except those listed respectively
                $ln = '(?:(?<=[!\#%&`*.,/:;<=>?@\^|~{\[(])\n|\n(?=[!\#%&`*.,/:;<=>?@\^|~}\])"\']))';


                //remove unnecessary whitespaces
                $js = preg_replace_callback("#(?(?=[\"'/\s])(?:{$s1}|{$s2}|{$x2}|{$b}|{$sp}|{$ln})|[\"'/\s])#", array($this, '_whitespaceCB'), $js);

                return trim($js);
        }

        /**
         * 
         * @param type $m
         * @return string
         */
        protected function _commentsCB($m)
        {
                //replace block quotes with single space
                if (preg_match('#^/\*.*\*/$#s', $m[0]))
                {
                        return ' ';
                }
                //replace single quotes with newlines
                elseif (preg_match('#^//#', $m[0]))
                {
                        return "\n";
                }
                //return literals unchanged
                else
                {
                        return $m[0];
                }
        }

        /**
         * 
         * @param type $m
         * @return string
         */
        protected function _singleCB($m)
        {
                //return literals unchanged
                if (preg_match('#^["\'/]|["\'/]$#', $m[0]))
                {
                        return $m[0];
                }
                //replace runs of whitespaces that include newline(s) with single newline
                elseif (preg_match('#\n#', $m[0]))
                {
                        return "\n";
                }
                //replace runs of whitespace (not including newlines) with single space
                else
                {
                        return ' ';
                }
        }

        /**
         * 
         * @param type $m
         * @return string
         */
        protected function _whitespaceCB($m)
        {
                //remove matched spaces and newlines
                if ($m[0] == ' ' || $m[0] == "\n")
                {
                        return '';
                }
                //return literals unchanged
                else
                {
                        return $m[0];
                }
        }

}
