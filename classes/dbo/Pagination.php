<?php

class Pagination
{
  private $getParam = 'page';
  private $cutLR = 1;
  private $entriesPerPage = 10;
  private $totalEntries = 0;
  private $totalPages = 0;
  private $curPage = 1;
  private $html = '';
  private $html_count = '';



  // -------------------------------------------------------------------



  private function renderHtml()
  {
    if ($this->totalPages > 1) {

      if ($this->curPage > 1 ) {
        $previousPage = ($this->curPage - 1);
        $previousStatus = '';
      } else {
        $previousPage = 1;
        $previousStatus = ' is-inactive';
      }

      if ($this->curPage < $this->totalPages) {
        $nextPage = ($this->curPage + 1);
        $nextStatus = '';
      } else {
        $nextPage = $this->totalPages;
        $nextStatus = ' is-inactive';
      }

      $this->html = '<div class="c-pagination">';

      // item 'previous'
      // ---------------
      $this->html .= '<div class="c-pagination__item">';
      $this->html .= '<a class="c-pagination__link'.$previousStatus.'" ';
      $this->html .= 'href="' . $_SERVER['PHP_SELF'] . '?' . $this->getParam . '=' . $previousPage . '">';
      $this->html .= '&#10094;';
      $this->html .= '</a>';
      $this->html .= '</div>';

      // items 'page'
      // ------------
      for ($curPage = 1; $curPage <= $this->totalPages; $curPage++) {

        $renderItem = true;

        // mark active page
        // ----------------
        $curPage == $this->curPage
          ? $currentStatus = ' is-active'
          : $currentStatus = '';

        // cut left/right of current page?
        // -------------------------------
        if (!empty($this->cutLR)) {
          if ($curPage < ($this->curPage - $this->cutLR) && $curPage != 1) {
            $renderItem = false;

            if ($curPage == 2) {
              $this->html .= '<div class="c-pagination__ellipsis">…</div>';
            }
          }

          if ($curPage > ($this->curPage + $this->cutLR) && $curPage != ($this->totalPages)) {
            $renderItem = false;

            if ($curPage == $this->totalPages - 1) {
              $this->html .= '<div class="c-pagination__ellipsis">…</div>';
            }
          }
        }

        if ($renderItem) {
          $this->html .= '<div class="c-pagination__item">';
          $this->html .= '<a class="c-pagination__link' . $currentStatus . '" ';
          $this->html .= 'href="' . $_SERVER['PHP_SELF'] . '?' . $this->getParam . '=' . $curPage . '">';
          $this->html .= $curPage;
          $this->html .= '</a>';
          $this->html .= '</div>';
        }
      }

      // item 'next'
      // -----------
      $this->html .= '<div class="c-pagination__item">';
      $this->html .= '<a class="c-pagination__link' . $nextStatus . '" ';
      $this->html .= 'href="' . $_SERVER['PHP_SELF'] . '?' . $this->getParam . '=' . $nextPage . '">';
      $this->html .= '&#10095;';
      $this->html .= '</a>';
      $this->html .= '</div>';

      $this->html .= '</div>';
    }
  }



  private function renderHtmlCount()
  {
    if ($this->curPage == 1) {
      $from = 1;
      $to = $this->entriesPerPage;
    } else {
      $from = ($this->entriesPerPage * ($this->curPage - 1) + 1);
      $to = (($from + $this->entriesPerPage) -1);

      if ($to > $this->totalEntries) {
        $to = $this->totalEntries;
      }
    }

    $this->html_count = '<div class="c-pagination__count">';
    $this->html_count .= 'Einträge ' . $from . '&ndash;' . $to . ' von ' . $this->totalEntries;
    $this->html_count .= '</div>';
  }



  // -------------------------------------------------------------------



  public function setTotalEntries(int $totalEntries)
  {
    Utils::checkNotEmpty($totalEntries, 'total entries');
    $this->totalEntries = $totalEntries;
    $this->totalPages = ceil($this->totalEntries / $this->entriesPerPage);
    $this->renderHtml();
    $this->renderHtmlCount();
  }



  public function setCurPage(int $curPage)
  {
    Utils::checkNotEmpty($curPage, 'current page');

    if ($curPage < 1) {
      $curPage = 1;
    }

    if ($curPage > $this->totalPages) {
      $curPage = $this->totalPages;
    }

    $this->curPage = $curPage;
    $this->renderHtml();
    $this->renderHtmlCount();
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
    if ($this->totalEntries > $this->entriesPerPage) {
      $offset = $this->entriesPerPage * ($this->curPage - 1);
    } else {
      $offset = 0;
    }

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

?>
