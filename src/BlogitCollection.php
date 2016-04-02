<?php
/**
 * This file is part of Jrean\Blogit package.
 *
 * @author Jean Ragouin <go@askjong.com> <www.askjong.com>
 */
namespace Jrean\Blogit;

use Illuminate\Support\Collection;

class BlogitCollection extends Collection
{
    /**
     * Sort the collection by dates of creation DESC.
     *
     * @return Jrean\Blogit\BlogitCollection
     */
    public function sortByCreatedAtDesc()
    {
        return $this->sortByDesc(function($item) {
            return $item->getCreatedAt();
        });
    }

    /**
     * Sort the collection by dates of update DESC.
     *
     * @return Jrean\Blogit\BlogitCollection
     */
    public function sortByUpdatedAtDesc()
    {
        $collection = $this->filter(function($item) {
            if ($item->getCreatedAt() != $item->getUpdatedAt()) {
                return true;
            }
        });

        return $collection->sortByDesc(function($item) {
            return $item->getUpdatedAt();
        });
    }
}
