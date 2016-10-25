<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Github\Client;
use Github\HttpClient\CachedHttpClient;
use Github\HttpClient\Cache\FilesystemCache;
use Jrean\Blogit\Repositories\Github\DocumentRepository;
use Jrean\Blogit\BlogitCollection;
use Jrean\Blogit\Document\ArticleFactory;
use Jrean\Blogit\Parser\DocumentParser;

class BlogitServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // configurations
        $this->publishes([
            __DIR__ . '/config/blogit.php' => config_path('blogit.php')
        ], 'config');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBlogit($this->app);
        $this->registerGithubClient($this->app);
        $this->registerGithubDocumentRepository($this->app);
        $this->registerGithubCachedHttpClient($this->app);
        $this->registerArticleFactory($this->app);
        $this->registerDocumentParser($this->app);
    }

    /**
     * Register the blogit class.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function registerBlogit(Application $app)
    {
        $app->bind('blogit', function ($app) {
            $repository = $app['blogit.github.repository.document'];
            $collection = new BlogitCollection();
            $articleFactory = $app['blogit.article.factory'];

            return new Blogit($repository, $collection, $articleFactory);
        });

        $app->alias('blogit', Blogit::class);
    }

    /**
     * Register the github client class.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function registerGithubClient(Application $app)
    {
        $app->singleton('blogit.github.client', function ($app) {
            $cachedHttpClient = $app['blogit.github.cachedHttpClient'];

            $client = new Client($cachedHttpClient);
            $client->authenticate(env('GITHUB_TOKEN'), 'http_token');

            return $client;
        });

        $app->alias('blogit.github.client', Client::class);
    }

    /**
     * Register the github cached http client class.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function registerGithubCachedHttpClient(Application $app)
    {
        $app->singleton('blogit.github.cachedHttpClient', function () {
            $cachedHttpClient = new CachedHttpClient();
            $cachedHttpClient->setCache(new FilesystemCache(storage_path() . '/github-api-cache'));

            return $cachedHttpClient;
        });

        $app->alias('blogit.github.cachedHttpClient', CachedHttpClient::class);
    }

    /**
     * Register the article factory class.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function registerArticleFactory(Application $app)
    {
        $app->bind('blogit.article.factory', function ($app) {
            $parser = $app['blogit.document.parser'];

            return new ArticleFactory($parser);
        });

        $app->alias('blogit.article.factory', ArticleFactory::class);
    }

    /**
     * Register the document parser class.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    protected function registerDocumentParser(Application $app)
    {
        $app->singleton('blogit.document.parser', function() {
            return new DocumentParser;
        });

        $app->alias('blogit.document.parser', ParserInterface::class);
    }

    /**
     * Register the github document repository class.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function registerGithubDocumentRepository(Application $app)
    {
        $app->bind('blogit.github.repository.document', function ($app) {
            $client = $app['blogit.github.client'];
            return new DocumentRepository($client);
        });

        $app->alias('blogit.github.repository.document', DocumentRepositoryInterface::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'blogit',
            'blogit.github.repository.document',
            'blogit.github.client',
            'blogit.github.cachedHttpClient',
            'blogit.document.parser',
            'blogit.article.factory',
        ];
    }
}
