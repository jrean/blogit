<?php

/*
 * This file is part of.Blogit
 *
 * (c) Jong <go@askjong.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jrean\Blogit;

use Jrean\Blogit\Repository\AbstractGithubDocumentRepository;
use Illuminate\Support\Collection;
use Github\Client;

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
     * Create a new Github Document Respository Instance.
     *
     * @param \Github\Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);

        $this->articlesDirPath = env('GITHUB_ARTICLES_DIRECTORY_PATH');
        $this->pagesDirPath    = env('GITHUB_PAGES_DIRECTORY_PATH');
    }

    /**
     * Fetch all Articles.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getArticles()
    {
        $articles   = parent::getAll($this->articlesDirPath);
        $collection = new Collection;

        foreach ($articles as $article) {
            $githubMetadata = $this->getByPath($article['path']);
            $commits  = $this->getCommitsByPath($githubMetadata['path']);

            $collection->push(
                app()->make('article', [
                    'metadata' => $githubMetadata,
                    'commits'  => $commits
                ])
            );
        }
        return $collection;
    }

    /**
     * Fetch all articles sorted by date.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNewArticles()
    {
        return $this->sortByCreatedAtDesc($this->getAll());
    }

    /**
     * Fetch all Updated Articles Sorted by Date.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUpdatedArticles()
    {
        return $this->sortByUpdatedAtDesc($this->getAll());
    }

    /**
     * Fetch an Article by its Slug.
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
     * Fetch news and updates for index page.
     *
     * @return array Collection of Articles
     */
    public function getForIndex()
    {
        $articles  = $this->getArticles();
        $news      = $this->sortByCreatedAtDesc($articles);
        $updates   = $this->sortByUpdatedAtDesc($articles);
        return compact('news', 'updates');
    }
}
