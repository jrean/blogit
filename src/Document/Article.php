<?php

/*
 * This file is part of Blogit.
 *
 * (c) Jong <go@askjong.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jrean\Blogit\Document;

use Jrean\Blogit\Parser\ParserInterface;

class Article extends AbstractDocument
{
    /**
     * Article title.
     *
     * @var string
     */
    protected $title;

    /**
     * Article slug.
     *
     * @var string
     */
    protected $slug;

    /**
     * Create a new Article instance.
     *
     * @param \Jrean\Blogit\Parser\ParserInterface $parser
     * @param array $metadata
     * @param array $commits
     */
    public function __construct(ParserInterface $parser, array $metadata, array $commits)
    {
        parent::__construct($parser, $metadata, $commits);

        $contentMetadata = $this->parser->parseMetadata($this->getContent());
        $this->title     = $contentMetadata['title'];
        $this->setSlug($contentMetadata);
    }

    /**
     * Set the Article slug.
     *
     * @param array $metadata
     * @return \Jrean\Blogit\Document\Article
     */
    protected function setSlug(array $metadata)
    {
        if (array_key_exists('slug', $metadata) && !empty($metadata['slug'])) {
            $this->slug = str_slug($metadata['slug']);
        } else {
            $this->slug = str_slug($metadata['title']);
        }
        return $this;
    }

    /**
     * Get the Article slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get the Article title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
