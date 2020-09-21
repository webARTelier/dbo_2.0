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



  public function check_empty($value, string $label)
  {
    if (empty($value)) {
      throw new CustomException('Value for ›' . $label  . '‹ is empty!');
    }
  }



  // -------------------------------------------------------------------



  private function render_html()
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



  private function render_html_count()
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



  public function set_totalEntries(int $totalEntries)
  {
    $this->check_empty($totalEntries, 'total entries');
    $this->totalEntries = $totalEntries;
    $this->totalPages = ceil($this->totalEntries / $this->entriesPerPage);
    $this->render_html();
    $this->render_html_count();
  }



  public function set_curPage(int $curPage)
  {
    $this->check_empty($curPage, 'current page');

    if ($curPage < 1) {
      $curPage = 1;
    }

    if ($curPage > $this->totalPages) {
      $curPage = $this->totalPages;
    }

    $this->curPage = $curPage;
    $this->render_html();
    $this->render_html_count();
  }



  public function set_getParam(string $getParam)
  {
    $this->check_empty($getParam, 'get parameter');
    $this->getParam = $getParam;
  }



  public function set_cutLR(int $cutLR)
  {
    $this->cutLR = $cutLR;
  }



  public function set_entriesPerPage(int $entriesPerPage)
  {
    $this->check_empty($entriesPerPage, 'entries per page');
    $this->entriesPerPage = $entriesPerPage;
  }



  public function get_limit()
  {
    return $this->entriesPerPage;
  }



  public function get_offset()
  {
    if ($this->totalEntries > $this->entriesPerPage) {
      $offset = $this->entriesPerPage * ($this->curPage - 1);
    } else {
      $offset = 0;
    }

    return $offset;
  }



  public function get_html()
  {
    return $this->html;
  }



  public function get_html_count()
  {
    return $this->html_count;
  }
}

?>
