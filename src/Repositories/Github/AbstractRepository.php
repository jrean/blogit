<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit\Repositories\Github;

use Github\Client;
use Github\HttpClient\Message\ResponseMediator;

abstract class AbstractRepository
{
    /**
     * Github client instance.
     *
     * @var \Github\Client
     */
    protected $github;

    /**
     * Github username.
     *
     * @var string
     */
    protected $user;

    /**
     * Github repository.
     *
     * @var string
     */
    protected $repository;

    /**
     * Create a new github respository instance.
     *
     * @param \Github\Client $client
     */
    public function __construct(Client $client)
    {
        $this->github = $client;
        $this->user = env('GITHUB_USER');
        $this->repository = env('GITHUB_REPOSITORY');
    }
}
