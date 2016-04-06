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
     * Previous article.
     *
     * @var \Jrean\Blogit\Document\Article
     */
    protected $previous;

    /**
     * Next article.
     *
     * @var \Jrean\Blogit\Document\Article
     */
    protected $next;

    /**
     * Related articles by tags.
     *
     * @var \Jrean\Blogit\BlogitCollection
     */
    protected $relatedArticles;

    /**
     * Article metadata.
     *
     * @var array
     */
    protected $metadata;

    /**
     * Create a new Article instance.
     *
     * @param  \Jrean\Blogit\Parser\ParserInterface  $parser
     * @param  array  $metadata document metadata
     * @param  array  $commits
     */
    public function __construct(ParserInterface $parser, array $metadata, array $commits)
    {
        parent::__construct($parser, $metadata, $commits);

        $this->metadata = $this->parser->parseMetadata($this->getContent());

        $this
            ->setTitle($this->metadata)
            ->setSlug($this->metadata)
            ->setTags($this->metadata);
    }

    /**
     * Get the proper getMetadata call.
     *
     * @param  string  $name
     * @param  string  $arguments
     * @return string
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) != 'get') {
            throw new RuntimeException('Call to undefined method Jrean\Blogit\Document\Article::' . $name);
        }

        return $this->getMetadata(strtolower(ltrim($name, 'get')));
    }

    /**
     * Get the requested metadata value.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getMetadata($value)
    {
        if ( ! array_key_exists($value, $this->metadata) || empty($this->metadata[$value])) {
            throw new RuntimeException('The metadata ' . $value . ' for the article ' . $this->title . ' doesn\'t exist or is empty');
        }

        return $this->metadata[$value];
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
     * Set related articles.
     *
     * @param  \Jrean\Blogit\BlogitCollection  $articles
     * @return \Jrean\Blogit\Document\Article
     */
    public function setRelatedArticles(BlogitCollection $articles)
    {
        $this->relatedArticles = $articles;

        return $this;
    }

    /**
     * Get related articles.
     *
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getRelatedArticles()
    {
        return $this->relatedArticles;
    }

    /**
     * Set previous article.
     *
     * @param  \Jrean\Blogit\Document\Article  $article
     * @return \Jrean\Blogit\Document\Article
     */
    public function setPrevious(Article $article)
    {
        $this->previous = $article;

        return $this;
    }

    /**
     * Get previous articles.
     *
     * @return \Jrean\Blogit\Document\Article | null
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * Set previous article.
     *
     * @param  \Jrean\Blogit\BlogitCollection  $article
     * @return \Jrean\Blogit\Document\Article
     */
    public function setNext(Article $article)
    {
        $this->next = $article;

        return $this;
    }

    /**
     * Get next article.
     *
     * @return \Jrean\Blogit\Document\Article | null
     */
    public function getNext()
    {
        return $this->next;
    }
}
