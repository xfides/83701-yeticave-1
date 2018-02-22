<?

//--------------------------------------------------------------------------
//    imported files / modules
//-------------------------------------

require_once ('./helpers/enableErrorReporting.php');
require_once('./helpers/handleTemplates.php');
require_once('./helpers/handleForms.php');
require_once('./helpers/common.php');
require_once('./helpers/helperDB.php');
require_once('./helpers/mysql_helper.php');
$dbLink = require_once('./config/init.php');

//-------------------------------------
//    imported files / modules
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

$title = "Добавить Лот";
$user_avatar = 'img/user.jpg';
$content = "Если видно это сообщение, то контент шаблона не был сформирован";

//-------------------------------------
//    common variables for layout
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    declaration system form variables for processing with it
//-------------------------------------

$formFields = [
    "lot-name"  =>
        [
            "errorEmpty" => "Проверьте введенное имя."
        ],
    "category"  =>
        [
            "errorEmpty" => "Выберите обязательно категорию."
        ],
    "message"   =>
        [
            "errorEmpty" => "Введите текст с описанием лота."
        ],
    "lot-image" =>
        [
            "errorEmpty"     => "Проверьте, загрузили ли вы изображение.",
            "errorPHP"       => "Какая то ошибка при загрузке. Попробуйте другой файл.",
            "errorWrongType" => "К загрузке допускаются только jpg, jpeg, png",
        ],
    "lot-rate"  =>
        [
            "errorEmpty"    => "Цена должна быть указана.",
            "errorNotDigit" => "Цена должна быть цифрой.",
            "errorLessZero" => "Цена должна быть больше 0."
        ],
    "lot-step"  =>
        [
            "errorEmpty"    => "Шаг ставки должна быть указан.",
            "errorNotDigit" => "Шаг ставки не должен быть 0.",
            "errorLessZero" => "Цена должна быть больше 0."
        ],
    "lot-date"  =>
        [
            "errorEmpty"  => "Укажите дату.",
            "errorFormat" => "Укажите правильную! дату в формате ДД.ММ.ГГГГ",
            "errorDate"   => "Дата окончания лота должна быть большк 24 часов."
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

$categoriesWithId = getCategories($dbLink, true);

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

  $isError = false;

  //(( ---- этап проверки формы ----))

  foreach ($formFields as $formFieldKey => $formFieldValue) {

    //(( пропускаем изображение, потому что он в массиве $_FILES ))
    if ($formFieldKey === "lot-image") {
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

    //(( является ли цифрой значение поля lot-rate ? ))
    if (!is_numeric($savedFormUserInput["lot-rate"])) {
      $formFieldsErrors["lot-rate"] = $formFields["lot-rate"]["errorNotDigit"];
      $isError = true;
    }

    //(( является ли цифрой значение поля lot-step ? ))
    if (!(is_numeric($savedFormUserInput["lot-step"]))) {
      $formFieldsErrors["lot-step"] = $formFields["lot-step"]["errorNotDigit"];
      $isError = true;
    }

    //(( выбрана ли категория ? ))
    if ($savedFormUserInput["category"] === "Выберите категорию") {
      $formFieldsErrors["category"] = $formFields["category"]["errorEmpty"];
      $isError = true;
    }

    //(( правильно ли набрана пользователем дата ? ))
    $userLotDateEnd = $savedFormUserInput["lot-date"];
    $partsDate = [];
    $isCorrectStringDate = preg_match(
            '/^(\\d{2})\\.(\\d{2})\\.(\\d{4})$/',
            $userLotDateEnd,
            $partsDate
        )
        && checkdate($partsDate[2], $partsDate[1], $partsDate[3]);
    if (!$isCorrectStringDate) {
      $formFieldsErrors["lot-date"] = $formFields["lot-date"]["errorFormat"];
      $isError = true;
    }

  }

  //(( ---- этап провероки формы ------- ))

  if (!$isError) {

    //(( поле начальная цена больше 0 ? ))
    $userStartPrice = (integer)$savedFormUserInput["lot-rate"];
    if ($userStartPrice <= 0) {
      $formFieldsErrors["lot-rate"] = $formFields["lot-rate"]["errorLessZero"];
      $isError = true;
    }

    //(( поле шаг ставки больше 0 ? ))
    $userStepRate = (integer)$savedFormUserInput["lot-step"];
    if ($userStepRate <= 0) {
      $formFieldsErrors["lot-step"] = $formFields["lot-step"]["errorLessZero"];
      $isError = true;
    }

    //(( указанная дата больше хотя бы на один день ? ))
    $userLotDateEnd = strtotime($savedFormUserInput["lot-date"]);
    $diffTimeEndLot = $userLotDateEnd - strtotime("now");
    if ($diffTimeEndLot < 0 || ($diffTimeEndLot / 60 / 60) < 24) {
      $formFieldsErrors["lot-date"] = $formFields["lot-date"]["errorDate"];
    }

  }

  //(( ---- этап провероки формы  -  обработка изображения ----- ))

  $strErrorImage = checkUploadImage($_FILES["lot-image"]);
  $uploadFile = "";

  switch ($strErrorImage) {

    //(( отправлен ли хоть какой то файл ?  ))
    case "fileIsNotFound":
      $formFieldsErrors["lot-image"] = $formFields["lot-image"]["errorEmpty"];
      break;

    //(( удовлетворяет ли файл директивам php.ini ? ))
    case "somePhpError":
      $formFieldsErrors["lot-image"] = $formFields["lot-image"]["errorPHP"];
      break;

    //(( похож ли файл на изображение ? ))
    case "wrongMimeType":
    case "wrongExtension":
      $formFieldsErrors["lot-image"] = $formFields["lot-image"]["errorWrongType"];
      break;

    //(( хорошо, убедились, что файл - допустимое изображение ))
    case "":
      $uploadsDir = "./img/";
      $fileName = $_FILES['lot-image']['name'];
      $tmpName = $_FILES["lot-image"]["tmp_name"];
      $uploadFile = $uploadsDir . $fileName;
      move_uploaded_file($tmpName, $uploadFile);
  }

  //(( ------------------------------------------------------------------ ))
  //(( формирование страницы с подходящим шаблоном, данными и параметрами ))

  if (count($formFieldsErrors) !== 0) {
    //(( есть ошибки, показываем форму снова с заполненными значениями ))

    $templateData = compact("formFieldsErrors", "savedFormUserInput");
    $content = getContent('phpTemplates/addLotTemplate.php', $templateData);

  } else {
    //(( нет ошибок, формируем карточку товара ))

    //(( формируем id категори нового лота ))
    $idCategory = null;
    foreach ($categoriesWithId as $oneCategory) {
      if ($oneCategory["name"] == $savedFormUserInput["category"]) {
        $idCategory = (integer)$oneCategory["id"];
      }
    }

    //(( перегоняем время в вид, воспринимаемый базой данных sql ))
    $lotUserDateEnd = $savedFormUserInput["lot-date"];
    $lotDateParts = explode(".", $lotUserDateEnd);
    $lotUserDateSql =
        "$lotDateParts[2]-$lotDateParts[1]-$lotDateParts[0] 00:00:00";

    //(( собираем вменяемые данные для sql запроса, порядок важен! ))
    $lotInfo = [
        "name"       => $savedFormUserInput["lot-name"],                //+
        "text"       => $savedFormUserInput["message"],                 //+
        "urlImage"   => $uploadFile,                                    //+
        "initPrice"  => (integer)$savedFormUserInput["lot-rate"],       //+
        "rateStep"   => (integer)$savedFormUserInput["lot-step"],       //+
        "dateEnd"    => $lotUserDateSql,                                //+
        "idAuthor"   => (integer)$_SESSION["userId"],                   //+
        "idCategory" => $idCategory                                     //+
    ];

    //(( отправляем запрос в базу данных ))
    $idLot = setNewLot($dbLink, $lotInfo);

    //(( ищем новый лот в бд, извлекаем его id, и перенаправляем на него user ))
    header('Location: /lot.php?id=' . $idLot, true, 301);
    exit;

  }

} else {

  //(( ------------------------------------------------------------------ ))
  //(( -------------------- это GET запрос а не POST -------------------- ))

  $templateData = compact("formFieldsErrors", "savedFormUserInput");
  $content = getContent('phpTemplates/addLotTemplate.php', $templateData);

}

//-------------------------------------
//    parsing URL and checking user input in $_POST data
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    final output to user
//-------------------------------------

$categories = [];
foreach ($categoriesWithId as $oneCategory) {
  $categories[] = $oneCategory["name"];
}

$templateData = compact("title", "user_avatar", "content", "categories");
$wholeHTML = getContent('phpTemplates\layoutTemplate.php', $templateData);

print($wholeHTML);

//-------------------------------------
//    final output to user
//--------------------------------------------------------------------------

?>