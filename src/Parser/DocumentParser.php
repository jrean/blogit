<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit\Parser;

use RuntimeException;
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
     * @param  string  $content
     * @return string
     */
    public function render($content)
    {
        return $this->text($content);
    }

    /**
     * Parse metadata.
     *
     * @param  string  $content
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public function parseMetadata($content)
    {
        $metadata = Yaml::parse($this->parseSection($content, 0));

        if ($metadata === null) throw new RuntimeException('Document metadata not found.');

        return $metadata;
    }

    /**
     * Parse a section of the document.
     *
     * @param  string  $content
     * @param  int  $offset
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function parseSection($content, $offset)
    {
        $sections = preg_split(self::SECTION_SPLITTER, $content, 2);

        if (count($sections) != 2) throw new RuntimeException('Something went wrong with the parsing of the sections.');

        return trim($sections[$offset]);
    }
}
