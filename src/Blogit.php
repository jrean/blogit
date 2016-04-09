<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit;

use Illuminate\Support\Collection;
use Jrean\Blogit\Repositories\Contracts\DocumentRepositoryInterface;
use Jrean\Blogit\BlogitCollection;
use Jrean\Blogit\Document\ArticleFactory;
use Jrean\Blogit\Document\Article;

class Blogit
{
    /**
     * Github document repository instance.
     *
     * @var \Jrean\Blogit\Repositories\Contracts\DocumentRepositoryInterface;
     */
    protected $documents;

    /**
     * Document(s) collection.
     *
     * @var \Jrean\Blogit\BlogitCollection
     */
    protected $collection;

    /**
     * Article factory instance.
     *
     * @var \Jrean\Blogit\Document\ArticleFactory
     */
    protected $articleFactory;

    /**
     * Create a new blogit instance.
     *
     * @param  \Github\Client  $client
     * @param  \Jrean\Blogit\BlogitCollection  $collection
     * @param  \Jrean\Blogit\Document\ArticleFactory  $articleFactory
     */
    public function __construct(
        DocumentRepositoryInterface $documents,
        BlogitCollection $collection,
        ArticleFactory $articleFactory
    )
    {
        $this->documents = $documents;

        $this->collection = $collection;

        $this->articleFactory = $articleFactory;

        $this->init();
    }

    /**
     * Init blogit.
     *
     * Fetch all files on the Github repository, transform and store them into
     * a collection of articles.
     *
     * @return void
     */
    protected function init()
    {
        $documents = $this->documents->getAll();

        // Make and push the new article into the collection.
        foreach ($documents as $document) {
            $article = $this->makeArticle($document['path']);
            $this->collection->push($article);
        }

        // Set the previous and next articles for each article.
        $this->collection = $this->collection->each(function($article, $key) {
            if (($previous = $this->collection->get($key - 1)) instanceof Article) {
                $article->setPrevious($previous);
            }

            if (($next = $this->collection->get($key + 1)) instanceof Article) {
                $article->setNext($next);
            }
        });

        // Set the related articles for each article.
        $this->collection = $this->collection->each(function($article) {
            $related = $this->getRelatedArticlesByTags($article);
            $article->setRelatedArticles($related);
        });
    }

    /**
     * Create a new article instance.
     *
     * @param  string  $path
     * @return void Jrean\Blogit\Document\Article
     */
    protected function makeArticle($path)
    {
        $metadata = $this->getMetadata($path);

        $commits = $this->getCommits($path);

        return $this->articleFactory->make($metadata, $commits);
    }

    /**
     * Get the document metadata including the content.
     *
     * @param  string  $path
     * @return array
     */
    protected function getMetadata($path)
    {
        return $this->documents->getByPath($path);
    }

    /**
     * Get the document commit(s).
     *
     * @param  string  $path
     * @return array
     */
    protected function getCommits($path)
    {
        return $this->documents->getCommitsByPath($path);
    }

    /**
     * Get all articles.
     *
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getArticles()
    {
        return $this->collection;
    }

    /**
     * Get related articles from tag(s).
     *
     * @param  \Jrean\Blogit\Document\Article  $article
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getRelatedArticlesByTags(Article $article)
    {
        $related = $this->getArticlesByTags($article->getTags());

        return $related->reject(function($item) use($article) {
            return $item->getSha() == $article->getSha();
        });
    }

    /**
     * Get articles by a tag.
     *
     * @param  string  $tag
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getArticlesByTag($tag)
    {
        return $this->getArticles()->filter(function($article) use($tag) {
            return in_array($tag, $article->getTags());
        });
    }

    /**
     * Get articles with tag(s).
     *
     * @param  array  $tags
     * @return \Jrean\Blogit\BlogitCollection
     */
    public function getArticlesByTags(array $tags)
    {
        return $this->getArticles()->filter(function($article) use($tags) {
            return !empty(array_intersect($tags, $article->getTags()));
        });
    }

    /**
     * Get article by slug.
     *
     * @param  string  $slug
     * @return \Jrean\Blogit\Document\Article;
     */
    public function getArticleBySlug($slug)
    {
        return $this->getArticles()->filter(function($article) use($slug) {
            return $article->getSlug() == $slug;
        })->first();
    }

    /**
     * Get the full tag list.
     *
     * - tag name
     * - tag slug
     * - number of article(s) for a tag
     *
     * @param  \Jrean\Blogit\BlogitCollection  $articles
     * @return \Illuminate\Support\Collection
     */
    public function getTagsList(BlogitCollection $articles)
    {
        $tags = new Collection;

        $articles->each(function($article) use(&$tags) {
            foreach ($article->getTags() as $tag) {
                if ( ! $tags->contains('name', $tag)) {
                    $tags->push([
                        'name' => $tag,
                        'slug' => str_slug($tag),
                        'articles' => $this->getArticlesByTag($tag)->count()
                    ]);
                }
            }
        });

        return $tags;
    }
}
