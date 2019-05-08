<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn .= '<nav>
            <ol class="breadcrumb">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .= '<li class="breadcrumb-item"><a href="'.$arResult[$index]["LINK"].'">'.$title.'</a></li>';
	}
	else
	{
		$strReturn .= '<li class="breadcrumb-item active" aria-current="page">'.$title.'</li>';
	}
}

$strReturn .= '</ol>
        </nav>';

return $strReturn;
