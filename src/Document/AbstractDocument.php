<?php

/*
 * This file is part of Blogit.
 *
 * (c) Jong <go@askjong.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jrean\Blogit\Document;

use Jrean\Blogit\Parser\ParserInterface;

abstract class AbstractDocument
{
    /**
     * Parser Instance.
     *
     * @var \Jrean\Blogit\Parser\ParserInterface
     */
    protected $parser;

    /**
     * Document Filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * Document Filepath.
     *
     * @var string
     */
    protected $path;

    /**
     * Document Sha.
     *
     * @var string
     */
    protected $sha;

    /**
     * Document Url.
     *
     * @var string
     */
    protected $url;

    /**
     * Document Html Url.
     *
     * @var string
     */
    protected $htmlUrl;

    /**
     * Document Git Url.
     *
     * @var string
     */
    protected $gitUrl;

    /**
     * Document Downlowd Url.
     *
     * @var string
     */
    protected $downloadUrl;

    /**
     * Document Creation Date.
     *
     * @var string
     */
    protected $createdAt;

    /**
     * Document Last Udpate Date.
     *
     * @var string
     */
    protected $updatedAt;

    /**
     * Document Content.
     *
     * @var string base64 encoded
     */
    protected $content;

    /**
     * Document Commit(s).
     *
     * @var array
     */
    protected $commits;

    /**
     * Create a new Document instance.
     *
     * @param \Jrean\Blogit\Parser\ParserInterface $parser
     * @param array $metadata
     * @param array $commits
     */
    public function __construct(ParserInterface $parser, array $metadata, array $commits)
    {
        $this->parser      = $parser;

        $this->filename    = $metadata['name'];
        $this->path        = $metadata['path'];
        $this->sha         = $metadata['sha'];
        $this->url         = $metadata['url'];
        $this->htmlUrl     = $metadata['html_url'];
        $this->gitUrl      = $metadata['git_url'];
        $this->downloadUrl = $metadata['download_url'];
        $this->content     = $metadata['content'];
        $this->commits     = $commits;

        $this
            ->setCreatedAt($commits)
            ->setUpdatedAt($commits);
    }

    /**
     * Get the document contributor(s).
     *
     * @return array
     */
    public function getContributors()
    {
        $contributors = [];
        foreach($this->commits as $commit) {
            $commiter = array_get($commit, 'author.login');
            if ( ! array_has($contributors, $commiter)) {
                $contributors[$commiter] = [
                    'name'       => $commiter,
                    'avatar_url' => array_get($commit, 'author.avatar_url'),
                    'html_url'   => array_get($commit, 'author.html_url')
                ];
            }
        }
        return $contributors;
    }

    /**
     * Format date.
     *
     * @param  string $date
     * @param  string $format
     * @return string
     */
    private function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        $date = new \DateTime($date);
        if ($format == 'Y-m-d') return $date->format($format);
        return $date->format($format);
    }

    /**
     * Get the Document filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the Document path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the Document sha.
     *
     * @return string
     */
    public function getSha()
    {
        return $this->sha;
    }

    /**
     * Get the Document url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the Document html url.
     *
     * @return string
     */
    public function getHtmlUrl()
    {
        return $this->htmlUrl;
    }

    /**
     * Get the Document Git url.
     *
     * @return string
     */
    public function getGitUrl()
    {
        return $this->gitUrl;
    }

    /**
     * Get the Document downlowd url.
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * Set the Document creation date.
     *
     * @param  array $commits
     * @return \Jrean\Blogit\Document\Document
     */
    protected function setCreatedAt(array $commits)
    {
        $createdAt       = array_get(last($commits), 'commit.author.date');
        $this->createdAt = $this->formatDate($createdAt);
        return $this;
    }

    /**
     * Get the Document creation date.
     *
     * @param  string $format
     * @return string
     */
    public function getCreatedAt($format = 'Y-m-d H:i:s')
    {
        return $this->formatDate($this->createdAt, $format);
    }

    /**
     * Set the Document last update date.
     *
     * @param  array $commits
     * @return \Jrean\Blogit\Document\Document
     */
    protected function setUpdatedAt($commits)
    {
        $updatedAt       = array_get(head($commits), 'commit.author.date');
        $this->updatedAt = $this->formatDate($updatedAt);
        return $this;
    }

    /**
     * Get the Document last update date.
     *
     * @param  string $format
     * @return string
     */
    public function getUpdatedAt($format = 'Y-m-d H:i:s')
    {
        return $this->formatDate($this->updatedAt, $format);
    }

    /**
     * Get the document content.
     *
     * @return string
     */
    public function getContent()
    {
        return base64_decode($this->content);
    }

    /**
     * Get the document body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->parser->parseSection($this->getContent(), 1);
    }

    /**
     * Get the document body converted in Html.
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->parser->text($this->getBody());
    }

    /**
     * Get the Document commit(s).
     *
     * @return array
     */
    public function getCommits()
    {
        return $this->commits;
    }
}
