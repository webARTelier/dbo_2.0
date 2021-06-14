<?php

class Pagination
{
  private $getParam = 'page';
  private $cutLR = 1;
  private $entriesPerPage = 10;
  private $totalEntries = 0;
  private $totalPages = 0;
  private $currentPage = 1;
  private $html = '';
  private $html_count = '';



  public function getPaginationHtml()
  {
    $this->renderPaginationHtml();
    return $this->html;
  }



  private function renderPaginationHtml()
  {
    if ($this->totalPages > 1) {
      $this->html = '<div class="c-pagination">';
      $this->html .= $this->renderBackwardItem();
      $this->html .= $this->renderPageItems();
      $this->html .= $this->renderForwardItem();
      $this->html .= '</div>';
    }
  }



  private function renderBackwardItem()
  {
    $previousPage = 1;
    $previousStatus = ' is-inactive';
    $html = '';

    if ($this->currentPage > 1) {
      $previousPage = ($this->currentPage - 1);
      $previousStatus = '';
    }

    $html .= '<div class="c-pagination__item">';
    $html .= '<a class="c-pagination__link' . $previousStatus . '" ';
    $html .= 'href="' . $_SERVER['PHP_SELF'];
    $html .= '?' . $this->getParam . '=' . $previousPage . '">';
    $html .= '&#10094;';
    $html .= '</a>';
    $html .= '</div>';

    return $html;
  }



  private function renderPageItems()
  {
    $html = '';

    for ($currentPage = 1; $currentPage <= $this->totalPages; $currentPage++) {

      $currentPage == $this->currentPage
        ? $currentStatus = ' is-active'
        : $currentStatus = '';

      $withinCutRangeLeft = $currentPage < ($this->currentPage - $this->cutLR) && $currentPage != 1;
      $whithinCutRangeRight = $currentPage > ($this->currentPage + $this->cutLR) && $currentPage != ($this->totalPages);

      if ($withinCutRangeLeft || $whithinCutRangeRight) {
        if ($currentPage == 2 || $currentPage == $this->totalPages - 1) {
          $html .= '<div class="c-pagination__ellipsis">…</div>';
        }
        continue;
      }

      $html .= '<div class="c-pagination__item">';
      $html .= '<a class="c-pagination__link' . $currentStatus . '" ';
      $html .= 'href="' . $_SERVER['PHP_SELF'];
      $html .= '?' . $this->getParam . '=' . $currentPage . '">';
      $html .= $currentPage;
      $html .= '</a>';
      $html .= '</div>';
    }

    return $html;
  }



  private function renderForwardItem()
  {
    $nextPage = $this->totalPages;
    $nextStatus = ' is-inactive';
    $html = '';

    if ($this->currentPage < $this->totalPages) {
      $nextPage = ($this->currentPage + 1);
      $nextStatus = '';
    }

    $html .= '<div class="c-pagination__item">';
    $html .= '<a class="c-pagination__link' . $nextStatus . '" ';
    $html .= 'href="' . $_SERVER['PHP_SELF'];
    $html .=  '?' . $this->getParam . '=' . $nextPage . '">';
    $html .= '&#10095;';
    $html .= '</a>';
    $html .= '</div>';

    return $html;
  }



  public function getPaginationCountHtml()
  {
    $this->renderCountHTML();
    return $this->html_count;
  }



  private function renderCountHTML()
  {
    $firstEntryOnPage = ($this->entriesPerPage * ($this->currentPage - 1) + 1);
    $lastEntryOnPage = (($firstEntryOnPage + $this->entriesPerPage) - 1);

    if ($this->currentPage == 1) {
      $firstEntryOnPage = 1;
      $lastEntryOnPage = $this->entriesPerPage;
    }

    if ($lastEntryOnPage > $this->totalEntries) {
      $lastEntryOnPage = $this->totalEntries;
    }

    $this->html_count = '<div class="c-pagination__count">';
    $this->html_count .= 'Einträge ' . $firstEntryOnPage . '&ndash;' . $lastEntryOnPage . ' von ' . $this->totalEntries;
    $this->html_count .= '</div>';
  }



  public function setTotalEntries(int $totalEntries)
  {
    $this->totalEntries = $totalEntries;
    $this->totalPages = ceil($this->totalEntries / $this->entriesPerPage);
  }



  public function setCurrentPage(int $currentPage)
  {
    if ($currentPage < 1) {
      $currentPage = 1;
    }

    if ($currentPage > $this->totalPages) {
      $currentPage = $this->totalPages;
    }

    $this->currentPage = $currentPage;
  }



  public function setGetParam(string $getParam)
  {
    Utils::checkNotEmpty($getParam, 'get parameter');
    $this->getParam = $getParam;
  }



  public function setCutLR(int $cutLR)
  {
    $this->cutLR = $cutLR;
  }



  public function setEntriesPerPage(int $entriesPerPage)
  {
    Utils::checkNotEmpty($entriesPerPage, 'entries per page');
    $this->entriesPerPage = $entriesPerPage;
  }



  public function getLimit()
  {
    return $this->entriesPerPage;
  }



  public function getOffset()
  {
    $this->totalEntries > $this->entriesPerPage
      ? $offset = $this->entriesPerPage * ($this->currentPage - 1)
      : $offset = 0;

    return $offset;
  }
}
