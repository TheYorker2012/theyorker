<?php

/**
 * @file views/templates/list.php
 * @brief View for displaying a list of items all using some view.
 *
 * @param $Items array[data] Array of comments, sequentially indexed.
 * @param $InnerView string Name of item view.
 * @param $InnerItemName string Name item to pass to @a $InnerView.
 * @param $MaxPerPage int,(NULL) Maximum number of items to display per page.
 * @param $PageLinkSpan int,(NULL) Page link span.
 * @param $IncludedIndex int,(NULL) An included index to display (independent of page size).
 * @param $PageUrlPrefix string,(NULL) Page url before the included index.
 * @param $PageUrlPostfix string,(NULL) Page url after the included index.
 */

$CI = &get_instance();

if (is_int($MaxPerPage) && $MaxPerPage > 0) {
	// figure out start and end indicies
	--$IncludedIndex;
	$quantity = count($Items);
	if ($IncludedIndex >= $quantity) {
		$IncludedIndex = ($quantity ? $quantity-1 : 0);
	} elseif ($IncludedIndex < 0) {
		$IncludedIndex = 0;
	}
	$start_index = $IncludedIndex - ($IncludedIndex % $MaxPerPage);
	$end_index = $start_index + $MaxPerPage;
	if ($end_index > $quantity) {
		$end_index = $quantity;
	}
	
	// generate html for page links
	$max_page      = (int)(($quantity-1)/$MaxPerPage);
	if ($max_page > 0) {
		$current_page  = (int)($start_index/$MaxPerPage);
		$is_first_page = ($current_page == 0);
		$is_last_page  = ($current_page == $max_page);
		$links = array();
		if (!$is_first_page) {
			$links[] = '<span><a href="'.
				$PageUrlPrefix.(1+($current_page-1)*$MaxPerPage).$PageUrlPostfix.
				'">&lt;</a></span>';
		}
		if ($current_page > $PageLinkSpan) {
			$links[] = '<span><a href="'.
				$PageUrlPrefix.'1'.$PageUrlPostfix.
				'">1</a></span>';
			if ($current_page > $PageLinkSpan+1) {
				$links[] = '...';
			}
		}
		for ($page_counter = max(0,$current_page-$PageLinkSpan);
		     $page_counter <= min($max_page, $current_page+$PageLinkSpan);
		     ++$page_counter) {
			if ($page_counter === $current_page) {
				$links[] = '<span class="selected">'.($page_counter+1).'</span>';
			} else {
				$links[] = '<span><a href="'.
					$PageUrlPrefix.(1+($page_counter)*$MaxPerPage).$PageUrlPostfix.
					'">'.($page_counter+1).'</a></span>';
			}
		}
		if ($current_page < $max_page-$PageLinkSpan) {
			if ($current_page+1 < $max_page-$PageLinkSpan) {
				$links[] = '...';
			}
			$links[] = '<span><a href="'.
				$PageUrlPrefix.(1+($max_page)*$MaxPerPage).$PageUrlPostfix.
				'">'.($max_page+1).'</a></span>';
		}
		if (!$is_last_page) {
			$links[] = '<span><a href="'.
				$PageUrlPrefix.(1+($current_page+1)*$MaxPerPage).$PageUrlPostfix.
				'">&gt;</a></span>';
		}
		$comment_text = 'comment';
		if ($quantity > 1) {
			$comment_text .= 's';
		}
		$page_index = '<div style="float:left;width:100%;margin-bottom:0.5em;"><div class="Pagination">'.implode('',$links).'</div>Showing '.($start_index + 1).' - '.$end_index.' of '.$quantity.' '.$comment_text.'</div>';

		
		// draw it
		echo($page_index);
	}
	
	// call the views for this page
	for ($item_counter = $start_index; $item_counter < $end_index; ++$item_counter) {
		$CI->load->view($InnerView, array(
			$InnerItemName => $Items[$item_counter],
			'ListNumber' => $item_counter+1,
		));
	}
	
	// draw the page index again
	if (isset($page_index)) {
		echo($page_index);
	}
} else {
	foreach ($Items as $key => $item) {
		$CI->load->view($InnerView, array($InnerItemName => $item));
	}
}


?>