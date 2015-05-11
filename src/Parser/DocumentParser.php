<?php

/*
 * This file is part of Blogit.
 *
 * (c) Jong <go@askjong.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jrean\Blogit\Parser;

use Exception;
use Parsedown;
use Symfony\Component\Yaml\Yaml;

class DocumentParser extends Parsedown implements ParserInterface
{
    /**
     * Pattern to separate the metadata and the body.
     */
    const SECTION_SPLITTER = '/\s+-{3,}\s+/';

    /**
     * Render HTML.
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        return $this->text($content);
    }

    /**
     * Parse metadata.
     *
     * @param  string $content
     * @return array
     *
     * @throws \Exception
     */
    public function parseMetadata($content)
    {
        $metadata = Yaml::parse($this->parseSection($content, 0));
        if ($metadata === null) throw new Exception('Document metadata not found.');
        return $metadata;
    }

    /**
     * Parse a section of the document.
     *
     * @param  string $content
     * @param  int $offset
     * @return string
     *
     * @throws \Exception
     */
    public function parseSection($content, $offset)
    {
        $sections = preg_split(self::SECTION_SPLITTER, $content, 2);
        if (count($sections) != 2) throw new Exception('Something went wrong with the parsing of the sections.');
        return trim($sections[$offset]);
    }
}
