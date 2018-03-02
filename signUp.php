<?
//--------------------------------------------------------------------------
//    imported files / modules
//-------------------------------------


session_start();
require_once ('./helpers/enableErrorReporting.php');
require_once('./helpers/common.php');
require_once('./helpers/handleTemplates.php');
require_once('./helpers/handleForms.php');
require_once('./helpers/mysql_helper.php');
require_once('./helpers/helperDB.php');
$dbLink = require_once('./config/init.php');

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
//    declaration system form variables for processing with it
//-------------------------------------

$formFields = [
    "email"      =>
        [
            "errorEmpty"  => "Введите email",
            "errorWrong"  => "Введенная строка не похожа на email",
            "errorExists" => "Такой email уже существует"
        ],
    "password"   =>
        [
            "errorEmpty" => "Поле с паролем не может быть пустым.",
            "errorShort" => "Пароль должен быть не менее 4 символов"
        ],
    "name"       =>
        [
            "errorEmpty" => "Имя не может быть пустым"
        ],
    "message"    =>
        [
            "errorEmpty" => "Укажите контактные данные"
        ],
    "userAvatar" =>
        [
            "errorPHP"       => "Какая то ошибка при загрузке. Попробуйте другой файл.",
            "errorWrongType" => "К загрузке допускаются только jpg, jpeg, png",
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

  $userInfo = [];

  //(( ------------------------------------------------------------------ ))
  //(( ------------------ проверка входящих полей формы ------------------))

  $isError = false;

  //(( ---- этап проверки формы ----))

  foreach ($formFields as $formFieldKey => $formFieldValue) {

    //(( пропускаем изображение, потому что он в массиве $_FILES ))
    if ($formFieldKey === "userAvatar") {
      continue;
    }

    //(( переданы ли вообще данные ? ))
    if (!array_key_exists($formFieldKey, $_POST)) {
      $formFieldsErrors[$formFieldKey] = $formFieldValue["errorEmpty"];
      $isError = true;
    }

    //(( фильтруем пользовательский ввод ))
    $formUserInput = filterUserString($_POST[$formFieldKey]);

    //(( являтся ли переданное значение пустым? ))
    if (isEmptyString($formUserInput)) {
      $formFieldsErrors[$formFieldKey] = $formFieldValue["errorEmpty"];
      $isError = true;
    };

    //(( сохраняем введенные пользовательские данные ))
    $savedFormUserInput[$formFieldKey] = $formUserInput;

  }

  //(( ---- этап проверки формы ------- ))

  if (!$isError) {

    //(( похожа ли строка на email ))
    if (!filter_var($savedFormUserInput["email"], FILTER_VALIDATE_EMAIL)) {
      $formFieldsErrors["email"] = $formFields["email"]["errorWrong"];
      $isError = true;
    }

    //(( пароль не менее 4 символов ))
    if (strlen($savedFormUserInput["password"]) < 4) {
      $formFieldsErrors["password"] = $formFields["password"]["errorShort"];
      $isError = true;
    }

  }

  //(( ---- этап проверки формы ------- ))

  if (!$isError) {

    //(( уникален ли введенный email ))
    $userInfo = getUserByEmail($dbLink, $savedFormUserInput["email"]);

    if (!is_null($userInfo)) {
      $formFieldsErrors["email"] = $formFields["email"]["errorExists"];
      $isError = true;
    }

  }

  //(( ---- этап провероки формы  -  обработка изображения ----- ))

  $userAvatar = $_FILES["userAvatar"];
  $uploadFile = "";

  if ($userAvatar["error"] !== 4) {

    $strErrorImage = checkUploadImage($userAvatar);

    switch ($strErrorImage) {

      //(( удовлетворяет ли файл директивам php.ini ? ))
      case "somePhpError":
        $formFieldsErrors["userAvatar"] = $formFields["userAvatar"]["errorPHP"];
        break;

      //(( похож ли файл на изображение ? ))
      case "wrongMimeType":
      case "wrongExtension":
        $formFieldsErrors["userAvatar"] =
            $formFields["userAvatar"]["errorWrongType"];
        break;

      //(( хорошо, убедились, что файл - допустимое изображение ))
      case "":
        $uploadsDir = "./img/";
        $fileName = $_FILES['userAvatar']['name'];
        $tmpName = $_FILES["userAvatar"]["tmp_name"];
        $uploadFile = $uploadsDir . $fileName;
        move_uploaded_file($tmpName, $uploadFile);
    }

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
    $content = getContent('phpTemplates/signUpTemplate.php', $templateData);

  } else {
    //(( нет ошибок, собираем данные о новом пользователе ))

    //(( собираем вменяемые данные для sql запроса, порядок важен! ))
    $userPassword =
        password_hash($savedFormUserInput["password"], PASSWORD_DEFAULT);

    $userAvatarUrl = ($uploadFile !== "") ? $uploadFile : null;

    $userInfo = [
        "email"     => $savedFormUserInput["email"],
        "name"      => $savedFormUserInput["name"],
        "password"  => $userPassword,
        "avatarUrl" => $userAvatarUrl,
        "message"   => $savedFormUserInput["message"],
    ];

    //(( отправляем запрос в базу данных ))
    setNewUser($dbLink, $userInfo);
    
    //(( переадресовать на страницу входа ))
    header('Location: /login.php', true, 301);
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
  $content = getContent('phpTemplates/signUpTemplate.php', $templateData);

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