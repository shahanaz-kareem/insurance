<?php

namespace App\Pagination;

use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use App\Pagination\UrlWindow;
use App\Pagination\UrlWindowPresenterTrait;

class SemanticUIPresenter implements PresenterContract
{
    use UrlWindowPresenterTrait;

    /**
     * The paginator implementation.
     *
     * @var \Illuminate\Contracts\Pagination\Paginator
     */
    protected $paginator;

    /**
     * The URL window instance.
     *
     * @var \Illuminate\Pagination\UrlWindow
     */
    protected $window;

    /**
     * Create a new Semantic UI presenter instance.
     *
     * @param  \Illuminate\Contracts\Pagination\Paginator  $paginator
     * @return void
     */
    public function __construct(PaginatorContract $paginator)
    {
        $this->paginator = $paginator;
        $this->window = new UrlWindow($paginator);
    }

    /**
     * Determine if the underlying paginator being presented has pages to show.
     *
     * @return bool
     */
    public function hasPages()
    {
        return $this->paginator->hasPages() && count($this->paginator->items()) > 0;
    }

    /**
     * Convert the URL window into Semantic UI HTML.
     *
     * @return string
     */
    public function render()
    {
        if ($this->hasPages()) {
            return sprintf(
                '<div class="ui pagination menu">%s %s %s</div>',
                $this->getPreviousButton(),
                $this->getLinks(),
                $this->getNextButton()
            );
        }

        return '';
    }

    /**
     * Get the disabled text wrapper for a pagination link.
     *
     * @param  string  $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<a class="icon item disabled">' . $text . '</a>';
    }

    /**
     * Get the HTML wrapper for a page link.
     *
     * @param  string  $url
     * @param  int  $page
     * @param  string|null  $rel
     * @return string
     */
    protected function getPageLinkWrapper($url, $page, $rel = null)
    {
        if ($this->paginator->currentPage() == $page) {
            return '<a class="item active">' . $page . '</a>';
        }

        return '<a class="item" href="' . $url . '">' . $page . '</a>';
    }
}

