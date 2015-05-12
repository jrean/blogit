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
     * Article tags.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Article History Url.
     *
     * @var string
     */
    protected $historyUrl;

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

        $this
            ->setTitle($contentMetadata)
            ->setSlug($contentMetadata)
            ->setTags($contentMetadata)
            ->setHistoryUrl();
    }

    /**
     * Set the Article title.
     *
     * @param  array $metadata
     * @return \Jrean\Blogit\Document\Article
     *
     * @throws \Exception
     */
    protected function setTitle(array $metadata)
    {
        if ( ! array_key_exists('title', $metadata) || empty($metadata['title'])) {
            throw new Exception('Title metadata is missing or empty.');
        }
        $this->title = $metadata['title'];
        return $this;
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

    /**
     * Set the Article slug.
     *
     * @param  array $metadata
     * @return \Jrean\Blogit\Document\Article
     */
    protected function setSlug(array $metadata)
    {
        if (array_key_exists('slug', $metadata) && !empty($metadata['slug'])) {
            $this->slug = str_slug($metadata['slug']);
        } else {
            $this->slug = str_slug($this->title);
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
     * Set the Article tag(s).
     *
     * @param  array $metadata
     * @return \Jrean\Blogit\Document\Article
     */
    protected function setTags(array $metadata)
    {
        if (array_key_exists('tags', $metadata) && !empty($metadata['tags'])) {
            $this->tags = $metadata['tags'];
        }

        return $this;
    }

    /**
     * Get the Article tag(s).
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the Document History Url.
     *
     * @return \Jrean\Blogit\Document\Document
     */
    protected function setHistoryUrl()
    {
        $base          = 'https://github.com/';
        $user          = env('GITHUB_USER');
        $repository    = env('GITHUB_REPOSITORY');
        $path          = '/commits/master/';
        $directoryPath = env('GITHUB_ARTICLES_DIRECTORY_PATH');

        $this->historyUrl = $base . $user . '/' . $repository . $path . $directoryPath . '/' . $this->filename;
        return $this;
    }

    /**
     * Get the Document History Url.
     *
     * @return string
     */
    public function getHistoryUrl()
    {
        return $this->historyUrl;
    }
}
