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
use Illuminate\Support\Collection;

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
     * Sort the collection by dates of creation DESC.
     *
     * @param  \Illuminate\Support\Collection $documents
     * @return \Illuminate\Support\Collection
     */
    public function sortByCreatedAtDesc(Collection $documents)
    {
        return $documents->sortByDesc(function($document) {
            return $document->getCreatedAt();
        });
    }

    /**
     * Sort the collection by dates of update DESC.
     *
     * @param  \Illuminate\Support\Collection $documents
     * @return \Illuminate\Support\Collection
     */
    public function sortByUpdatedAtDesc(Collection $documents)
    {
        $documents = $documents->filter(function($document) {
            if ($document->getCreatedAt() != $document->getUpdatedAt()) {
                return true;
            }
        });

        return $documents->sortByDesc(function($document) {
            return $document->getUpdatedAt();
        });
    }
}
