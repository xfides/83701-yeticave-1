<?
//--------------------------------------------------------------------------
//    imported files / modules
//-------------------------------------

require_once ('./helpers/enableErrorReporting.php');
require_once('./helpers/handleTemplates.php');
require_once('./helpers/handleForms.php');
require_once('./helpers/common.php');
require_once('./helpers/handleUsers.php');
require_once('./helpers/mysql_helper.php');
require_once('./helpers/helperDB.php');
$dbLink = require_once('./config/init.php');

/*$users = require('./phpInitialData/userdata.php');*/

//-------------------------------------
//    imported files/modules
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    common variables for layout
//-------------------------------------

$title = "Авторизация";
$user_avatar = 'img/user.jpg';
$content = "Если видно это сообщение, то контент шаблона не был сформирован";

//-------------------------------------
//    common variables for layout
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    declaration system form variables for processing with it
//-------------------------------------

$formFields = [
    "email"    =>
        [
            "errorEmpty" => "Проверьте введенный e-mail.",
            "errorEmail" => "Нет такого пользователя."
        ],
    "password" =>
        [
            "errorEmpty"    => "Поле с паролем не может быть пустым.",
            "errorPassword" => "Вы ввели неверный пароль."
        ]
];

$savedFormUserInput = [];

$formFieldsErrors = [];

//-------------------------------------
//    declaration system form variables for processing with it
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

  foreach ($formFields as $formFieldKey => $formFieldValue) {

    //(( переданы ли вообще данные ? ))
    array_key_exists($formFieldKey, $_POST) ?:
        $formFieldsErrors[$formFieldKey] = $formFieldValue["errorEmpty"];

    //(( фильтруем пользовательский ввод ))
    $formUserInput = filterUserString($_POST[$formFieldKey]);

    //(( сохраняем введенные пользовательские данные ))
    $savedFormUserInput[$formFieldKey] = $formUserInput;

  }

  //(( информаци о статусе авторизации пользователя ))
  $authUserInfo = '';

  if (
      isset($savedFormUserInput["email"])
      &&
      isset($savedFormUserInput["password"])
  ) {
    $authUserInfo = authorizeUser(
        $savedFormUserInput["email"],
        $savedFormUserInput["password"],
        $dbLink
    );
  }

  switch ($authUserInfo) {

    case "emptyEmail":
      $formFieldsErrors["email"] = $formFields["email"]["errorEmpty"];
      break;

    case "wrongEmail":
      $formFieldsErrors["email"] = $formFields["email"]["errorEmail"];
      break;

    case "emptyPassword":
      $formFieldsErrors["password"] = $formFields["password"]["errorEmpty"];
      break;

    case "wrongPassword":
      $formFieldsErrors["password"] = $formFields["password"]["errorPassword"];
      break;

    case "":
    default:
      break;
  }

  //(( ------------------------------------------------------------------ ))
  //(( формирование страницы с подходящим шаблоном, данными и параметрами ))

  if (count($formFieldsErrors) !== 0) {
    //(( есть ошибки, показываем форму снова с заполненными значениями ))

    $templateData = compact(
        "formFieldsErrors",
        "savedFormUserInput",
        "categories"
    );
    $content = getContent('phpTemplates/loginTemplate.php', $templateData);

  } else {
    //(( нет ошибок, открываем сессию, перенаправляем на главную ))

    //(( на всякий случай, очищаем предыдущие хвосты ))
    $_SESSION = [];

    //(( и стартуем сессию ))
    session_start();
    $_SESSION['userName'] = $authUserInfo["name"];
    $_SESSION['userId'] = $authUserInfo["id"];

    header("Location: index.php", true, 301);
    exit;

  }


} else {

  //(( ------------------------------------------------------------------ ))
  //(( -------------------- это GET запрос а не POST -------------------- ))

  $templateData = compact(
      "formFieldsErrors",
      "savedFormUserInput",
      "categories"
  );
  $content = getContent('phpTemplates\loginTemplate.php', $templateData);

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