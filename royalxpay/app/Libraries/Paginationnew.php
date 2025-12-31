<?php namespace App\Libraries;

/* * ***************************************************************************\
  +-----------------------------------------------------------------------------+
  | Project        : Chronicare                                             |
  | FileName       : Pagination.php                                            |
  | Version        : 1.0                                                        |
  | Developer      : subedar Yadav                                              |
  | Created On     : 15-10-2016                                                 |
  | Modified On    :                                                            |
  | Modified   By  :                                                            |
  | Authorised By  :  subedar Yadav                                             |
  | Comments       :  This class used for site config related acivity           |
  | Email          : subedar2507@gmail.com                                      |
  +-----------------------------------------------------------------------------+
  \**************************************************************************** */

class Paginationnew {

	 
    public function getPaginate($totalRecords=0,$curPage=1, $limit=50){
		$pager = Array();
		$pager['getNbResults']=$totalRecords;

		$pager['getFirstIndice'] = (($curPage - 1) * $limit) + 1;
		$pager['getLastIndice'] = (((($curPage - 1) * $limit) + $limit) < $pager['getNbResults']) ? ((($curPage - 1) * $limit) + $limit) : $pager['getNbResults'];

		$pager['haveToPaginate'] = $pager['getNbResults'] > $limit ? 1 : 0;
		// print_r($pager['haveToPaginate']);
		// exit;
		$pager['getPage'] = $curPage;

		$pager['getNextPage'] = $curPage + 1;
		$pager['getPreviousPage'] = $curPage - 1;

		$pager['getFirstPage'] = 1;
		$pager['getLastPage'] = ceil($pager['getNbResults'] / $limit);

		$firstElm = '';
		$lastElm = '';
		$links = array();

		$pageToDisplay=50;

		if ($pager['getPage'] % $pageToDisplay != 0)
		{
			$qt = floor($pager['getPage'] / $pageToDisplay);
			$qt = $qt + 1;
			$lastElm = $qt * $pageToDisplay;
			$firstElm = $lastElm - ($pageToDisplay-1);
		}
		else
		{
			$qt = $pager['getPage'] / $pageToDisplay;
			$lastElm = $qt * $pageToDisplay;
			$firstElm = $lastElm - ($pageToDisplay-1);
		}

		$i = $firstElm;
		while ($i <= $lastElm)
		{
			if ($i > $pager['getLastPage'])
			{
				break;
			}

			$links[] = $i;
			$i++;
		}
		$pager['getLinks'] = $links;

		return $pager;
	}

}
