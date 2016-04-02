<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit\Document;

use Jrean\Blogit\Parser\ParserInterface;
use Jrean\Blogit\BlogitCollection;
use RuntimeException;

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
     * Create a new Article instance.
     *
     * @param  \Jrean\Blogit\Parser\ParserInterface  $parser
     * @param  array  $metadata
     * @param  array  $commits
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
     * @return \Jrean\Blogit\Document\Article
     *
     * @param  array $metadata
     *
     * @throws \Exception
     */
    protected function setTitle(array $metadata)
    {
        if ( ! array_key_exists('title', $metadata) || empty($metadata['title'])) {
            throw new RuntimeException('Title metadata is missing or empty.');
        }

        $this->title = $metadata['title'];

        return $this;
    }

    /**
     * Get the article title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the article slug.
     *
     * Will generate a slug from the article title if a custom slug is not
     * provided within the metadata.
     *
     * @param  array $metadata
     *
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
     * Get the article slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the article tag(s).
     *
     * @param  array $metadata
     *
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
     * Set the article history url.
     *
     * @return \Jrean\Blogit\Document\Article
     */
    protected function setHistoryUrl()
    {
        $base = 'https://github.com/';
        $user = env('GITHUB_USER');
        $repository = env('GITHUB_REPOSITORY');
        $path = '/commits/master/';
        $directoryPath = env('GITHUB_ARTICLES_DIRECTORY_PATH');

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
     * @param  \Jrean\Blogit\BlogitCollection  $articles
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
