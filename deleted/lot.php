<?php
//--------------------------------------------------------------------------
//    imported files / modules
//-------------------------------------

session_start();

require_once ('./helpers/enableErrorReporting.php');
require_once('./helpers/handleTemplates.php');
require_once('./helpers/common.php');
require_once('./helpers/handleForms.php');
require_once('./config/init.php');
require_once('./helpers/helperDB.php');

/*$goodsAds = require('phpInitialData/dataLots.php');
$bets = require('phpInitialData/dataLot.php');*/

//-------------------------------------
//    imported files/modules
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    common variables for layout
//-------------------------------------

$title = "Лот";
$user_avatar = 'img/user.jpg';
$content = "Если видно это сообщение, то контент шаблона не был сформирован";

//-------------------------------------
//    common variables for layout
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    declaration system variables for processing with it
//-------------------------------------

$formFields = [

    "cost" =>
        [
            "errorEmpty"     => "Цена должна быть указана.",
            "errorNotDigit"  => "Цена должна быть цифрой.",
            "errorWrongRate" => "Цена ставки не должна быть ниже минимальной",
        ],

    "idLot" => []


];

$savedFormUserInput = [];

$formFieldsErrors = [];

$rateCookiesIds = [];

//(( по умолчанию показываем форму для добавления ставки ))
$showRateForm = true;

//(( проверить, есть ли уже cookie))
$valueCookieArr = [];

if (isset($_COOKIE["myLots"])) {
  $valueCookieArr = json_decode($_COOKIE["myLots"], true);
}

//(( запоминаем id тех лотов, которые в Cookies ))
foreach ($valueCookieArr as $valueCookieLot) {
  array_push($rateCookiesIds, $valueCookieLot["lotId"]);
}

//-------------------------------------
//    declaration system variables for processing with it
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    selecting categories from database
//-------------------------------------

$categories = getCategories($dbLink);

//-------------------------------------
//    selecting categories from database
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    parsing URL and checking user input in $_POST data
//-------------------------------------

$reqMethod = $_SERVER['REQUEST_METHOD'];

$isFormSent = ($reqMethod === 'POST') && (count($_POST) !== 0);

if ($isFormSent) {

  //(( ------------------------------------------------------------------ ))
  //(( ------------------ проверка входящих полей формы ------------------))

  $lotInfo = [];

  //(( обрабатываем идентификатор лота ? ))
  $idLotString = $_POST['idLot'] ?? null;

  if (
    // если нет параметра - идентификатора лота
      is_null($idLotString)
      ||
      // если параметр не число
      !(is_numeric($idLotString))
      ||
      // если запрашиваемый индекс отсутствует
      is_null($lotInfo = getLotInfo($dbLink, $idLotString))
  ) {
    send404();
  }

  $savedFormUserInput["idLot"] = filterUserString($_POST["idLot"]);

  //(( обрабатываем пользовательский ввод ставки ))
  $userCostFieldInfo = "";

  if (!array_key_exists("cost", $_POST)) {
    $formFieldsErrors["cost"] = $formFields["cost"]["errorEmpty"];
  } else {
    $userCostString = filterUserString($_POST["cost"]);
    $savedFormUserInput["cost"] = $userCostString;
    $userCostFieldInfo =
        checkUserRateField($userCostString, $lotInfo["minRate"]);
  }

  switch ($userCostFieldInfo) {
    case "emptyRate":
      $formFieldsErrors["cost"] = $formFields["cost"]["errorEmpty"];
      break;

    case "notNumber":
      $formFieldsErrors["cost"] = $formFields["cost"]["errorNotDigit"];
      break;

    case "lowRate":
      $formFieldsErrors["cost"] = $formFields["cost"]["errorWrongRate"];
      break;

    case "":
    default:
      break;
  }

  //(( ------------------------------------------------------------------ ))
  //(( формирование страницы с подходящим шаблоном, данными и параметрами ))

  if (count($formFieldsErrors) !== 0) {
    //(( есть ошибки, показываем форму снова с заполненными значениями ))

    $idLot = (integer)$savedFormUserInput["idLot"];
    $title = $lotInfo['name'];
    $bets = getRatesForLot($dbLink, $idLot);

    $lotDateEnd = $lotInfo["dateEnd"];                // int время конца лота
    $timeNow = strtotime("now");                      // текущее время
    $lotIdAuthor = $lotInfo ["idAuthor"];             // id автора лота
    $userIdSession = $_SESSION["userId"] ?? "-1";     // залогинен ли user
    $idUserBets = [];                                 // список id ставок юзера
    foreach ($bets as $oneBet){
      $idUserBets[] =  $oneBet["idUser"];
    }

    //(( пользователь  залогинен ?))
    $showRateForm1 = isset($_SESSION["userName"]);

    //(( время лота еще не вышло ?))
    $showRateForm2 = $lotDateEnd > $timeNow;

    //(( данный лот не был создан залогиненным юзером ? ))
    $showRateForm3 = $lotIdAuthor != $userIdSession;

    //(( не была ли хоть одной ставка на лот от залогиненного юзера ? ))
    $showRateForm4 = !in_array($userIdSession,$idUserBets);

    $showRateForm =
        ($showRateForm1 && $showRateForm2 && $showRateForm3 && $showRateForm4);

    //(( время ставок/лота из базы данных преобразуем к 'читабельному' виду ))
    foreach ($bets as &$oneBet) {
      $oneBet["ts"] = parseToHumanTime($oneBet["ts"]);
    }
    $lotInfo["dateEnd"] = parseToHumanTime($lotInfo["dateEnd"], true);



    $templateData = compact(
        "categories",
        "idLot",
        "lotInfo",
        "bets",
        "showRateForm",
        "formFieldsErrors",
        "savedFormUserInput"
    );

    $content = getContent('phpTemplates/lotTemplate.php', $templateData);

  } else {
    //(( нет обшибок - делаем Cookie и перенаправляем на другую страницу ))

    //(( отправить запись о сделанной ставке в базу данных ))
    $idLot = (integer)$savedFormUserInput["idLot"];
    $userRate = (integer)$savedFormUserInput["cost"];
    $idUser = (integer)$_SESSION["userId"];

    setNewUserRate($dbLink, $idLot, $userRate, $idUser);

    //(( формируем Cookie ))
    $nameCookie = "myLots";
    $lotForCookie = [
        "rateUserCost" => $userRate,
        "lotId"        => $idLot,
        "rateSetTime"  => strtotime("now"),
    ];
    $expireCookie = strtotime("+30 days");
    $pathCookieDomain = "/";
    array_push($valueCookieArr, $lotForCookie);
    $valueCookieStr = json_encode($valueCookieArr);

    //(( отправляем Cookie ))
    setcookie($nameCookie, $valueCookieStr, $expireCookie, $pathCookieDomain);

    //(( перенаправить на страницу лота ))
    header('Location: /lot.php?id=' . $idLot, true, 301);
    exit;

  }

} else {

  //(( ------------------------------------------------------------------ ))
  //(( -------------------- это GET запрос а не POST -------------------- ))

  $idLotString = $_GET['id'] ?? null;
  $lotInfo = null;

  if (
    // если нет параметра
      is_null($idLotString)
      ||
      // если параметр не число
      !(is_numeric($idLotString))
      ||
      // если запрашиваемый индекс отсутствует
      is_null($lotInfo = getLotInfo($dbLink, $idLotString))
  ) {
    send404();
  }

  /* получам всю информацию по лоту для её последующего отображения */

  $idLot = (integer)$lotInfo["id"];                 // id лота
  $title = $lotInfo['name'];                        // заголовок страницы
  $bets = getRatesForLot($dbLink, $idLot);        // ставки лота


  $lotDateEnd = $lotInfo["dateEnd"];                // int время конца лота
  $timeNow = strtotime("now");                      // текущее время
  $lotIdAuthor = $lotInfo ["idAuthor"];             // id автора лота
  $userIdSession = $_SESSION["userId"] ?? "-1";     // залогинен ли user
  $idUserBets = [];                                 // список id ставок юзера
  foreach ($bets as $oneBet){
    $idUserBets[] =  $oneBet["idUser"];
  }

  //(( пользователь  залогинен ?))
  $showRateForm1 = isset($_SESSION["userName"]);

  //(( время лота еще не вышло ?))
  $showRateForm2 = $lotDateEnd > $timeNow;

  //(( данный лот не был создан залогиненным юзером ? ))
  $showRateForm3 = $lotIdAuthor != $userIdSession;

  //(( не была ли хоть одной ставка на лот от залогиненного юзера ? ))
  $showRateForm4 = !in_array($userIdSession,$idUserBets);

  $showRateForm =
      ($showRateForm1 && $showRateForm2 && $showRateForm3 && $showRateForm4);

  //(( время ставок/лота из базы данных преобразуем к 'читабельному' виду ))
  foreach ($bets as &$oneBet) {
    $oneBet["ts"] = parseToHumanTime($oneBet["ts"]);
  }
  $lotInfo["dateEnd"] = parseToHumanTime($lotInfo["dateEnd"], true);

  $templateData = compact(
      "categories",
      "showRateForm",
      "idLot",
      "lotInfo",
      "bets",
      "formFieldsErrors",
      "savedFormUserInput"
  );
  $content = getContent('phpTemplates/lotTemplate.php', $templateData);

}

//-------------------------------------
//    parsing URL and checking user input in $_POST data
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


