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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Github\HttpClient\CachedHttpClient;
use Github\HttpClient\Cache\FilesystemCache;
use Github\Client;
use Jrean\Blogit\Blogit;
use Jrean\Blogit\Document\Article;
use Jrean\Blogit\Parser\DocumentParser;

/**
 * This is the Blogit service provider class.
 *
 * @author Jong <go@askjong.com>
 */
class BlogitServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGithubClient($this->app);
        $this->registerBlogit($this->app);
        $this->registerArticle($this->app);
        $this->registerDocumentParser($this->app);
    }

    /**
     * Register the Github Client class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    protected function registerGithubClient(Application $app)
    {
        $app->singleton('client', function ($app) {
            $cachedClient = new CachedHttpClient();
            $cachedClient->setCache(new FilesystemCache(storage_path() . '/github-api-cache'));

            $client = new Client($cachedClient);
            $client->authenticate(env('GITHUB_TOKEN'), 'http_token');

            return $client;
        });
        $app->alias('client', 'Github\Client');
    }

    /**
     * Register the Blogit class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    protected function registerBlogit(Application $app)
    {
        $app->singleton('blogit', function ($app) {
            $client = $app['client'];

            return new Blogit($client);
        });
        $app->alias('blogit', 'Jrean\Blogit\Blogit');
    }

    /**
     * Register the Article class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    protected function registerArticle(Application $app)
    {
        $app->bind('article', function($app, $parameters) {
            $parser   = $app['document.parser'];
            $metadata = $parameters['metadata'];
            $commits  = $parameters['commits'];

            return new Article($parser, $metadata, $commits);
        });
        $app->alias('article', 'Jrean\Blogit\Document\Article');
    }

    /**
     * Register the DocumentParser class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    protected function registerDocumentParser(Application $app)
    {
        $app->singleton('document.parser', function($app) {
            return new DocumentParser;
        });
        $app->alias('document.parser', 'Jrean\Blogit\Parser\ParserInterface');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'client',
            'blogit',
            'article',
            'document.parser'
        ];
    }
}
