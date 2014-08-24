<?php
/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2014  César Rodas                                                 |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
*/
namespace crodas;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

require __DIR__ . "/Intl/alias.php";


class Intl
{
    static $datas = [];
    static $lang;
    static $data;
    static $file;

    public static function build($path, $outdir)
    {
        if (!($path instanceof Finder)) {
            $finder = new Finder;
            $finder->files()->name("/\.(php|phtml|tpl|html|js)$/")->in($path);
            $path = $finder;
        }
        $scanner = new Intl\Scanner($finder);
        $texts   = $scanner->scan();
        if (!is_dir($outdir)) {
            mkdir($outdir);
        }
        $scanner->dump($outdir, array_combine($texts, $texts));
    }

    public static function compile($dir)
    {
        $parser = new Parser;
        $data   = [];
        foreach (glob("$dir/*.yml") as $file) {
            $data[ substr(basename($file), 0, -4) ] = $parser->parse(file_get_contents($file));
        }
        file_put_contents(self::$file, "<?php return " . var_export($data, true) . ";");
        self::init(self::$file, self::$lang);
    }

    public static function setLanguage($lang)
    {
        self::$lang = $lang;
        self::$data = !empty(self::$datas[$lang]) ? self::$datas[$lang] : [];
    }

    public static function init($path, $lang)
    {
        if (is_file($path)) {
            self::$datas = require $path;
        }
        self::$file = $path;
        self::$lang = $lang;
        self::$data = !empty(self::$datas[$lang]) ? self::$datas[$lang] : [];
    }

    public static function _($args)
    {
        if (!empty(self::$data[$args[0]])) {
            $args[0] =  self::$data[$args[0]];
        }
        if (count($args) == 1) {
            return $args[0];
        }
        return call_user_func_array('sprintf', $args);
    }
}
