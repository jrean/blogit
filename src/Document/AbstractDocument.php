<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit\Document;

use Jrean\Blogit\Parser\ParserInterface;
use Carbon\Carbon;

abstract class AbstractDocument
{
    /**
     * Document parser instance.
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
     * Document filepath.
     *
     * @var string
     */
    protected $path;

    /**
     * Document sha.
     *
     * @var string
     */
    protected $sha;

    /**
     * Document url.
     *
     * @var string
     */
    protected $url;

    /**
     * Document html url.
     *
     * @var string
     */
    protected $htmlUrl;

    /**
     * Document git url.
     *
     * @var string
     */
    protected $gitUrl;

    /**
     * Document downlowd url.
     *
     * @var string
     */
    protected $downloadUrl;

    /**
     * Document creation date.
     *
     * @var string
     */
    protected $createdAt;

    /**
     * Document last udpate date.
     *
     * @var string
     */
    protected $updatedAt;

    /**
     * Document content.
     *
     * @var string base64 encoded
     */
    protected $content;

    /**
     * Document commit(s).
     *
     * @var array
     */
    protected $commits;

    /**
     * Create a new document instance.
     *
     * @param  \Jrean\Blogit\Parser\ParserInterface  $parser
     * @param  array  $metadata
     * @param  array  $commits
     */
    public function __construct(ParserInterface $parser, array $metadata, array $commits)
    {
        $this->parser = $parser;
        $this->filename = $metadata['name'];
        $this->path = $metadata['path'];
        $this->sha = $metadata['sha'];
        $this->url = $metadata['url'];
        $this->htmlUrl = $metadata['html_url'];
        $this->gitUrl = $metadata['git_url'];
        $this->downloadUrl = $metadata['download_url'];
        $this->content = $metadata['content'];
        $this->commits = $commits;
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
                    'name' => $commiter,
                    'avatar_url' => array_get($commit, 'author.avatar_url'),
                    'html_url' => array_get($commit, 'author.html_url')
                ];
            }
        }

        return $contributors;
    }

    /**
     * Format date.
     *
     * @param  string  $date
     * @param  string  $format
     *
     * @return string
     */
    private function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        $date = new \DateTime($date);

        if ($format == 'Y-m-d') return $date->format($format);

        return $date->format($format);
    }

    /**
     * Get the document filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the document path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the document sha.
     *
     * @return string
     */
    public function getSha()
    {
        return $this->sha;
    }

    /**
     * Get the document url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the document html url.
     *
     * @return string
     */
    public function getHtmlUrl()
    {
        return $this->htmlUrl;
    }

    /**
     * Get the document git url.
     *
     * @return string
     */
    public function getGitUrl()
    {
        return $this->gitUrl;
    }

    /**
     * Get the document downlowd url.
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }

    /**
     * Set the document creation date.
     *
     * @param  array  $commits
     *
     * @return \Jrean\Blogit\Document\Document
     */
    protected function setCreatedAt(array $commits)
    {
        $createdAt = array_get(last($commits), 'commit.author.date');

        $this->createdAt = $this->formatDate($createdAt);

        return $this;
    }

    /**
     * Get the document creation date.
     *
     * @param  string  $format
     *
     * @return string
     */
    public function getCreatedAt($format = 'Y-m-d H:i:s')
    {
        return $this->formatDate($this->createdAt, $format);
    }

    /**
     * Set the document last update date.
     *
     * @param  array  $commits
     *
     * @return \Jrean\Blogit\Document\Document
     */
    protected function setUpdatedAt($commits)
    {
        $updatedAt = array_get(head($commits), 'commit.author.date');

        $this->updatedAt = $this->formatDate($updatedAt);

        return $this;
    }

    /**
     * Get the document last update date.
     *
     * @param  string  $format
     *
     * @return string
     */
    public function getUpdatedAt($format = 'Y-m-d H:i:s')
    {
        return $this->formatDate($this->updatedAt, $format);
    }

    /**
     * Get the document last update date diff for humans.
     *
     * @param  string  $format
     *
     * @return string
     */
    public function getUpdatedAtDiff()
    {
        return (new Carbon)->createFromFormat('Y-m-d H:i:s', $this->updatedAt)->diffForHumans();
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

    /**
     * Get the last commit url.
     *
     * @return string
     */
    public function getLastCommitUrl()
    {
        return array_get(head($this->getCommits()), 'html_url');
    }
}
