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



  private function renderPaginationHtml()
  {
    if ($this->totalPages > 1) {
      $this->html = '<div class="c-pagination">';
      $this->renderBackwardItem();
      $this->renderPageItems();
      $this->renderForwardItem();
      $this->html .= '</div>';
    }
  }



  private function renderBackwardItem()
  {
    if ($this->currentPage > 1) {
      $previousPage = ($this->currentPage - 1);
      $previousStatus = '';
    } else {
      $previousPage = 1;
      $previousStatus = ' is-inactive';
    }

    $this->html .= '<div class="c-pagination__item">';
    $this->html .= '<a class="c-pagination__link' . $previousStatus . '" ';
    $this->html .= 'href="' . $_SERVER['PHP_SELF'] . '?' . $this->getParam . '=' . $previousPage . '">';
    $this->html .= '&#10094;';
    $this->html .= '</a>';
    $this->html .= '</div>';
  }



  private function renderPageItems()
  {
    for ($currentPage = 1; $currentPage <= $this->totalPages; $currentPage++) {

      $renderItem = true;

      $currentPage == $this->currentPage
        ? $currentStatus = ' is-active'
        : $currentStatus = '';

      $cutIsSet = '!empty($this->cutLR)';
      $withinCutRangeLeft = $currentPage < ($this->currentPage - $this->cutLR) && $currentPage != 1;
      $whithinCutRangeRight = $currentPage > ($this->currentPage + $this->cutLR) && $currentPage != ($this->totalPages);

      if ($cutIsSet) {
        if ($withinCutRangeLeft) {
          $renderItem = false;

          if ($currentPage == 2) {
            $this->html .= '<div class="c-pagination__ellipsis">…</div>';
          }
        }

        if ($whithinCutRangeRight) {
          $renderItem = false;

          if ($currentPage == $this->totalPages - 1) {
            $this->html .= '<div class="c-pagination__ellipsis">…</div>';
          }
        }
      }

      if ($renderItem) {
        $this->html .= '<div class="c-pagination__item">';
        $this->html .= '<a class="c-pagination__link' . $currentStatus . '" ';
        $this->html .= 'href="' . $_SERVER['PHP_SELF'] . '?' . $this->getParam . '=' . $currentPage . '">';
        $this->html .= $currentPage;
        $this->html .= '</a>';
        $this->html .= '</div>';
      }
    }
  }



  private function renderForwardItem()
  {
    if ($this->currentPage < $this->totalPages) {
      $nextPage = ($this->currentPage + 1);
      $nextStatus = '';
    } else {
      $nextPage = $this->totalPages;
      $nextStatus = ' is-inactive';
    }

    $this->html .= '<div class="c-pagination__item">';
    $this->html .= '<a class="c-pagination__link' . $nextStatus . '" ';
    $this->html .= 'href="' . $_SERVER['PHP_SELF'] . '?' . $this->getParam . '=' . $nextPage . '">';
    $this->html .= '&#10095;';
    $this->html .= '</a>';
    $this->html .= '</div>';
  }



  private function renderCountHTML()
  {
    if ($this->currentPage == 1) {
      $firstEntryOnPage = 1;
      $lastEntryOnPage = $this->entriesPerPage;
    } else {
      $firstEntryOnPage = ($this->entriesPerPage * ($this->currentPage - 1) + 1);
      $lastEntryOnPage = (($firstEntryOnPage + $this->entriesPerPage) - 1);
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
    Utils::checkNotEmpty($totalEntries, 'total entries');
    $this->totalEntries = $totalEntries;
    $this->totalPages = ceil($this->totalEntries / $this->entriesPerPage);
    $this->renderPaginationHtml();
    $this->renderCountHTML();
  }



  public function setCurrentPage(int $currentPage)
  {
    Utils::checkNotEmpty($currentPage, 'current page');

    if ($currentPage < 1) {
      $currentPage = 1;
    }

    if ($currentPage > $this->totalPages) {
      $currentPage = $this->totalPages;
    }

    $this->currentPage = $currentPage;
    $this->renderPaginationHtml();
    $this->renderCountHTML();
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



  public function getPaginationHtml()
  {
    return $this->html;
  }



  public function getPaginationCountHtml()
  {
    return $this->html_count;
  }
}
