<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit\Document;

use Jrean\Blogit\Parser\ParserInterface;

class ArticleFactory
{
    /**
     * Document parser instance.
     *
     * @var \Jrean\Blogit\Parser\ParserInterface
     */
    protected $parser;

    /**
     * Create a new article factory instance.
     *
     * @param  \Jrean\Blogit\Parser\ParserInterface  $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Make a new article instance.
     *
     * @return \Jrean\Blogit\Document\Article
     */
    public function make(array $metadata, array $commits)
    {
        return new Article($this->parser, $metadata, $commits);
    }
}
