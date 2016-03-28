<?php


namespace Fairpay\Util\Pagination;


class PageView
{
    public $count;
    public $data;
    public $page;
    public $perPage;
    public $totalPages;
    public $prev;
    public $next;

    /**
     * PageView constructor.
     * @param $count
     * @param $limit
     * @param $data
     * @param $page
     */
    public function __construct($count, $limit, $data, $page)
    {
        $this->count      = $count;
        $this->data       = $data;
        $this->page       = $page;
        $this->perPage    = $limit;
        $this->totalPages = ceil($count / $limit);
    }
}