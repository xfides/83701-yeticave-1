<?
//--------------------------------------------------------------------------
//    imported files / modules
//-------------------------------------

require_once ('./helpers/enableErrorReporting.php');
require_once('./helpers/handleTemplates.php');
require_once('./helpers/common.php');
require_once('./helpers/helperDB.php');
$dbLink = require_once('./config/init.php');

//$goodsAds = require('./phpInitialData/dataLots.php');

//-------------------------------------
//    imported files/modules
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    redirect if not logged
//-------------------------------------

session_start();

if (!isset($_SESSION["userName"])) {
  send403();
}

//-------------------------------------
//    redirect if not logged
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    common variables for layout
//-------------------------------------

$title = "Мои ставки";
$user_avatar = 'img/user.jpg';
$content = "Если видно это сообщение, то контент шаблона не был сформирован";

//-------------------------------------
//    common variables for layout
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

//(( id текущего юзера, по которому вытащим его ставки ))
$userId = (integer)$_SESSION["userId"];

//(( получаем инфомрацию о ставках нашего пользователя ))
$userRatesInfo = getRatesByUser($dbLink, $userId);

if (is_null($userRatesInfo)) {
  $userRatesInfo = [];
} else {
  foreach ($userRatesInfo as &$oneRate) {
    $oneRate["lot_id"] = (integer)$oneRate["lot_id"];
    $oneRate["rate_createdAt"] =
        parseToHumanTime($oneRate["rate_createdAt"]);
    $oneRate["lot_dateEnd"] =
        parseToHumanTime($oneRate["lot_dateEnd"], true);
  }
}

$templateData = compact("userRatesInfo", "categories");
$content = getContent('phpTemplates\myLotsTemplate.php', $templateData);


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