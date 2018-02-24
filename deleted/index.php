<?
//--------------------------------------------------------------------------
//    imported files / modules
//-------------------------------------

session_start();

require_once ('./helpers/enableErrorReporting.php');
require_once('./helpers/handleTemplates.php');
require_once('./helpers/common.php');
require_once('./helpers/helperDB.php');
$dbLink = require_once('./config/init.php');
$dataForIndexPage = require('./phpInitialData/dataForTemplateIndex.php');
/*$goodsAds = require('./phpInitialData/dataLots.php');*/

//-------------------------------------
//    imported files/modules
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    common variables for layout
//-------------------------------------

$title = "Главная";
$user_avatar = 'img/user.jpg';
$content = "Если видно это сообщение, то контент шаблона не был сформирован";

//-------------------------------------
//    common variables for layout
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    selecting lots from database
//-------------------------------------

$lots = getOpenLots($dbLink);

foreach ($lots as &$oneLot){
  $oneLot["dateEnd"] = parseToHumanTime($oneLot["dateEnd"] ,true);
}

//-------------------------------------
//    selecting lots from database
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    selecting categories from database
//-------------------------------------

$categories = getCategories($dbLink);

//-------------------------------------
//    selecting categories from database
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    choosing appropriate template and corresponding data for it
//-------------------------------------

$requiredTemplate = 'phpTemplates\indexTemplate.php';
$templateData = [
    'goodsAds'             => $lots,
    'goodsCategories'      => $categories,
    'classesForCategories' => $dataForIndexPage['classesForCategories'],
    'lot_time_remaining'   => $dataForIndexPage['lot_time_remaining']
];

$content = getContent($requiredTemplate, $templateData);

//-------------------------------------
//    choosing appropriate template and corresponding data for it
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    final output to user
//-------------------------------------

$templateData = compact("title", "user_avatar", "content", "categories");
$wholeHTML = getContent('phpTemplates\layoutTemplate.php', $templateData);

print($wholeHTML);

//-------------------------------------
//    final output to user
//--------------------------------------------------------------------------

?>