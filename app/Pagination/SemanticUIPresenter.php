<?php

namespace App\Pagination;

use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Pagination\UrlWindow;
use App\Pagination\UrlWindowPresenterTrait;
use App\Pagination\SemanticUINextPreviousButtonRendererTrait;

class SemanticUIPresenter implements PresenterContract
{
    use UrlWindowPresenterTrait;
    use SemanticUINextPreviousButtonRendererTrait;

    /**
     * The paginator implementation.
     *
     * @var \Illuminate\Contracts\Pagination\Paginator
     */
    protected $paginator;

    /**
     * The URL window instance.
     *
     * @var array|null
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
        
        // Only create UrlWindow for LengthAwarePaginator (has total count)
        // Simple Paginator doesn't have total count, so we can't create page links
        if ($paginator instanceof LengthAwarePaginator) {
            $urlWindow = new UrlWindow($paginator);
            $this->window = $urlWindow->get();
        } else {
            // For simple paginators, set window to null (only prev/next buttons will work)
            $this->window = null;
        }
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

    /**
     * Get the dots wrapper.
     *
     * @return string
     */
    protected function getDots()
    {
        return '<a class="icon item disabled">...</a>';
    }

    /**
     * Get the HTML wrapper for an available page link.
     *
     * @param  string  $url
     * @param  int  $page
     * @param  string|null  $icon
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page, $icon = null)
    {
        if ($icon) {
            return '<a class="icon item" href="' . $url . '">' . $icon . '</a>';
        }

        return '<a class="item" href="' . $url . '">' . $page . '</a>';
    }

    /**
     * Get the HTML wrapper for the active page link.
     *
     * @param  int  $page
     * @return string
     */
    protected function getActivePageWrapper($page)
    {
        return '<a class="item active">' . $page . '</a>';
    }
}

