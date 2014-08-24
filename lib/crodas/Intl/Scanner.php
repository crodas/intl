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
namespace crodas\intl;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

class Scanner
{
    protected $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    public function findPatterns($file, Array &$texts)
    {
        $content = file_get_contents($file);
        $length  = strlen($content);
        preg_match_all("/((?:_|__|_e)\s*\(\s*[\"\'])/", $content, $matches);
        foreach (array_unique($matches[1]) as $pattern) {
            $offset = 0;
            $len    = strlen($pattern);
            while ( ($pos=strpos($content, $pattern, $offset)) !== FALSE ) {
                $end = $content[$pos+$len-1];
                for ($i = $pos + $len; $i < $length && $content[$i] != $end; ++$i) {
                    if ($content[$i] == "\\") ++$i;
                }
                $texts[] = stripslashes(substr($content, $pos + $len, $i - $pos - $len));
                $offset = $i;
            }
        }
        $texts = array_unique($texts);
    }

    protected function dumpFile($file, Array $data)
    {
        if (is_file($file)) {
            $yaml = new Parser();
            $data = array_merge($data, $yaml->parse(file_get_contents($file)));
        }
        $dumper = new Dumper();
        $yaml = $dumper->dump($data, 2);
        file_put_contents($file, $yaml);
    }

    public function dump($dir, Array $data)
    {
        foreach (glob("$dir/*.yml") as $yml) {
            $this->dumpFile($yml, $data);
        }
        $this->dumpFile("$dir/template.yml", $data);
    }

    public function scan()
    {
        $texts = [];
        foreach ($this->finder as $file) {
            $ttl  = filemtime($file);
            $this->findPatterns($file, $texts);
        }
        sort($texts);
        return $texts;
    }
}

