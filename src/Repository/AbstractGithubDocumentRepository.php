<?php

/*
 * This file is part of Blogit.
 *
 * (c) Jong <go@askjong.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jrean\Blogit\Repository;

use Github\Client;
use Github\HttpClient\Message\ResponseMediator;

abstract class AbstractGithubDocumentRepository implements DocumentRepositoryInterface
{
    /**
     * Github client instance.
     *
     * @var \Github\Client
     */
    protected $github;

    /**
     * Github Username.
     *
     * @var string
     */
    protected $user;

    /**
     * Github Repository.
     *
     * @var string
     */
    protected $repository;

    /**
     * Create a new Github Document Respository Instance.
     *
     * @param \Github\Client $client
     */
    public function __construct(Client $client)
    {
        $this->github     = $client;
        $this->user       = env('GITHUB_USER');
        $this->repository = env('GITHUB_REPOSITORY');
    }

    /**
     * Fetch all Documents.
     *
     * @return array
     */
    public function getAll($path = null)
    {
        return $this->github->api('repo')->contents()->show($this->user, $this->repository, $path);
    }

    /**
     * Fetch a single Document by its path.
     *
     * @param  string $path
     * @return array
     */
    public function getByPath($path)
    {
        return $this->github->api('repo')->contents()->show($this->user, $this->repository, $path);
    }

    /**
     * Fetch commit(s) for a Document.
     *
     * @param  string $path
     * @return array
     */
    public function getCommitsByPath($path)
    {
        return $this->github->api('repo')->commits()->all($this->user, $this->repository, ['path' => $path]);
    }

    /**
     * Get the Api rate limit.
     *
     * @return array
     */
    public function getApiRateLimit()
    {
        return ResponseMediator::getContent($this->github->getHttpClient()->get('rate_limit'));
    }

    /**
     * Get the Api core rate limit, remaining and reset delay.
     *
     * @return array
     */
    public function getApiCoreRateLimit()
    {
        $array = $this->getApiRateLimit();
        return $array['resources']['core'];
    }
}
