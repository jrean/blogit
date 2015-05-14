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
use Jrean\Blogit\BlogitCollection;

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
     * Tag(s) Related Articles.
     *
     * @var \Jrean\Blogit\BlogitCollection
     */
    protected $tagsRelated;

    /**
     * Content Metadata.
     * Every `keys / values` above the `---`.
     *
     * @var array
     */
    protected $contentMetadata;

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

        $this->contentMetadata = $this->parser->parseMetadata($this->getContent());

        $this
            ->setTitle()
            ->setSlug()
            ->setTags()
            ->setHistoryUrl();
    }

    /**
     * Set the Article title.
     *
     * @return \Jrean\Blogit\Document\Article
     *
     * @throws \Exception
     */
    protected function setTitle()
    {
        if ( ! array_key_exists('title', $this->contentMetadata) || empty($this->contentMetadata['title'])) {
            throw new Exception('Title metadata is missing or empty.');
        }
        $this->title = $this->contentMetadata['title'];
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
     * @return \Jrean\Blogit\Document\Article
     */
    protected function setSlug()
    {
        if (array_key_exists('slug', $this->contentMetadata) && !empty($this->contentMetadata['slug'])) {
            $this->slug = str_slug($this->contentMetadata['slug']);
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
     * @return \Jrean\Blogit\Document\Article
     */
    protected function setTags()
    {
        if (array_key_exists('tags', $this->contentMetadata) && !empty($this->contentMetadata['tags'])) {
            $this->tags = $this->contentMetadata['tags'];
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
     * Set the Article History Url.
     *
     * @return \Jrean\Blogit\Document\Article
     */
    protected function setHistoryUrl()
    {
        $base             = 'https://github.com/';
        $user             = env('GITHUB_USER');
        $repository       = env('GITHUB_REPOSITORY');
        $path             = '/commits/master/';
        $directoryPath    = env('GITHUB_ARTICLES_DIRECTORY_PATH');
        $this->historyUrl = $base . $user . '/' . $repository . $path . $directoryPath . '/' . $this->filename;
        return $this;
    }

    /**
     * Get the Article History Url.
     *
     * @return string
     */
    public function getHistoryUrl()
    {
        return $this->historyUrl;
    }

    /**
     * Set tags related articles.
     *
     * @param  \Jrean\Blogit\BlogitCollection $articles
     * @return \Jrean\Blogit\Document\Article
     */
    public function setTagsRelated(BlogitCollection $articles)
    {
        $this->tagsRelated = $articles;
        return $this;
    }

    /**
     * Get tags related articles.
     *
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getTagsRelated()
    {
        return $this->tagsRelated;
    }
}
