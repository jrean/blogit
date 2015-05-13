<?php

/*
 * This file is part of Blogit.
 *
 * (c) Jong <go@askjong.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jrean\Blogit;

use Github\Client;
use Jrean\Blogit\Repository\AbstractGithubDocumentRepository;
use Jrean\Blogit\Document\Article;
use Jrean\Blogit\BlogitCollection;

class Blogit extends AbstractGithubDocumentRepository
{
    /**
     * Articles Directory Path.
     *
     * @var string
     */
    protected $articlesDirPath;

    /**
     * Pages Directory Path.
     *
     * @var string
     */
    protected $pagesDirPath;

    /**
     * Article(s) Collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $articles;

    /**
     * Create a new Github Document Respository Instance.
     *
     * @param \Github\Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);

        $this->articlesDirPath = env('GITHUB_ARTICLES_DIRECTORY_PATH');
        $this->pagesDirPath    = env('GITHUB_PAGES_DIRECTORY_PATH');

        $this->initArticles();
    }

    /**
     * Fetch all Articles.
     *
     * @return void
     */
    protected function initArticles()
    {
        $this->articles = new BlogitCollection();
        $items          = parent::getAll($this->articlesDirPath);

        foreach ($items as $item) {
            $githubMetadata = $this->getByPath($item['path']);
            $commits        = $this->getCommitsByPath($githubMetadata['path']);

            $article        = app()->make('article', [
                'metadata' => $githubMetadata,
                'commits'  => $commits
            ]);

            $this->articles->push($article);
        }

        foreach ($this->articles as $article) {
            $related = $this->getRelatedArticlesByTags($article, $this->articles);
            $article->setTagsRelated($related);
        }
    }

    /**
     * Get all Articles.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Get Articles sorted by creation date DESC.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNewArticles()
    {
        return $this->getArticles()->sortByCreatedAtDesc();
    }

    /**
     * Get updated Articles sorted by date DESC.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUpdatedArticles()
    {
        return $this->getArticles()->sortByUpdatedAtDesc();
    }

    /**
     * Get an Article by its Slug.
     *
     * @param  string $slug
     * @return \Jrean\Blogit\Document\Article;
     */
    public function getArticleBySlug($slug)
    {
        return $this->getArticles()->filter(function($article) use($slug) {
            return $article->getSlug() == $slug;
        })->first();
    }

    /**
     * Get Articles by a tag.
     *
     * @param  string $tag
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getArticlesByTag($tag)
    {
        return $this->getArticles()->filter(function($article) use($tag) {
            return in_array($tag, $article->getTags());
        });
    }

    /**
     * Get Articles with tag(s).
     *
     * @param  array $tags
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getArticlesByTags(array $tags)
    {
        return $this->getArticles()->filter(function($article) use($tags) {
            return !empty(array_intersect($tags, $article->getTags()));
        });
    }

    /**
     * Get related Articles with the given tag(s).
     *
     * @param  \Jrean\Blogit\Document\Article $article
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getRelatedArticlesByTags(Article $article)
    {
        $related = $this->getArticlesByTags($article->getTags());
        return $related->reject(function($item) use($article) {
            return $item->getSha() == $article->getSha();
        });
    }
}
