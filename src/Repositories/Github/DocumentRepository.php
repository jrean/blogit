<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit\Repositories\Github;

use Github\Client;
use Jrean\Blogit\Repositories\Contracts\DocumentRepositoryInterface;
use Jrean\Blogit\Repositories\Github\AbstractRepository;

class DocumentRepository extends AbstractRepository implements DocumentRepositoryInterface
{
    /**
     * Github documents directory path.
     *
     * @var string
     */
    protected $documentsDirPath;

    /**
     * Create a new github document respository instance.
     *
     * @param \Github\Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
        $this->documentsDirPath = env('GITHUB_DOCUMENTS_DIRECTORY_PATH');
    }

    /**
     * Fetch all documents for a given directory path.
     *
     * @param  string  $dirPath
     * @return array
     */
    public function getAll($dirPath = null)
    {
        return $this
            ->github
            ->api('repo')
            ->contents()
            ->show(
                $this->user,
                $this->repository,
                $dirPath === null? $this->documentsDirPath : $dirPath
            );
    }

    /**
     * Fetch a document content by its path.
     *
     * @param  string  $path
     * @return array
     */
    public function getByPath($path)
    {
        return $this
            ->github
            ->api('repo')
            ->contents()
            ->show(
                $this->user,
                $this->repository,
                $path
            );
    }

    /**
     * Fetch commit(s) for a given document.
     *
     * @param  string  $path
     * @return array
     */
    public function getCommitsByPath($path)
    {
        return $this
            ->github
            ->api('repo')
            ->commits()
            ->all(
                $this->user,
                $this->repository,
                ['path' => $path]
            );
    }
}
