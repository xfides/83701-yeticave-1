<?

//--------------------------------------------------------------------------
//    display errors if correct data were not received
//-------------------------------------

/**
 * display error if something was wrong with connection of database
 *
 * @param $dbLink
 */
function displayDataErrorFromDB($dbLink) {

  //(( собираем сообщение об ошибке ))
  $errorMessageSql = mysqli_error($dbLink);
  $errorMessageSubject = "Извините, ошибка при получении данных. ";
  $errorMessageFull = $errorMessageSubject . $errorMessageSql;

  //(( формируем шаблон с ошибкой ))
  $templateData = compact("errorMessageFull");
  $errorDBPage = getContent("phpTemplates/errorDBTemplate.php", $templateData);

  //(( показываем ошибку и прекращаем скрипт ))
  print ($errorDBPage);
  exit;

}

//-------------------------------------
//    display errors if correct data were not received
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    selecting categories from database
//-------------------------------------

/**
 * get list of full categories or one category by id
 *
 * @param $dbLink           - resource of connection with db
 * @param bool $withId      - id of concrete category
 *
 * @return array
 */
function getCategories($dbLink, $withId = false) {

  $categories = [];
  $sql = "SELECT * FROM `categories`";
  $resultQueryDB = mysqli_query($dbLink, $sql);

  //(( если запрос не прошел - показать ошибку ))
  $resultQueryDB ?: displayDataErrorFromDB($dbLink);

  //(( 'вытряхиваем' результат из базды данных ))
  $rowsQueryDB = mysqli_fetch_all($resultQueryDB, MYSQLI_ASSOC);

  if ($withId) {

    foreach ($rowsQueryDB as $row) {
      $categories[] = $row;
    }

  } else {

    foreach ($rowsQueryDB as $row) {
      $categories[] = $row["name"];
    }

  }

  //(( если результат нулевой, возращаем просто пустой массив))
  if (count($categories) === 0) {
    $categories = [];
    return $categories;
  }

  //(( возвращаем нормальный массив с данными ))
  return $categories;
}

//-------------------------------------
//    selecting categories from database
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    selecting all open lots from database
//-------------------------------------

/**
 * getting only new open lots from database
 *
 * @param $dbLink
 * @return array
 */
function getOpenLots($dbLink) {

  $lots = [];
  $sql = "
SELECT
  lots.id AS id,
  lots.name AS name,
  lots.init_price AS price,
  lots.url_image AS urlImg,
  UNIX_TIMESTAMP(lots.date_end) AS dateEnd,
  categories.name AS category

FROM `lots`
  JOIN `categories` ON lots.category = categories.id

WHERE lots.date_end > NOW()
      AND lots.user_winner IS NULL
GROUP BY lots.id
";

  $resultQueryDB = mysqli_query($dbLink, $sql);

  //(( если запрос не прошел - показать ошибку ))
  $resultQueryDB ?: displayDataErrorFromDB($dbLink);

  //(( 'вытряхиваем' результат из базды данных ))
  $rowsQueryDB = mysqli_fetch_all($resultQueryDB, MYSQLI_ASSOC);

  foreach ($rowsQueryDB as $row) {
    $lots[] = $row;
  }

  //(( если результат нулевой, возращаем просто пустой массив))
  if (count($lots) === 0) {
    $lots = [];
    return $lots;
  }

  //(( возвращаем нормальный массив с данными ))
  $lotsWithIndex = [];
  foreach ($lots as $oneLot) {
    $lotsWithIndex[(integer)$oneLot["id"]] = $oneLot;
  }

  return $lotsWithIndex;
}

//-------------------------------------
//    selecting all open lots from database
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    selecting all info about  one lot by it's id, include (rates)
//-------------------------------------

/**
 * get info about lot by it's id
 *
 * @param $dbLink   - resource of connection with db
 * @param int $id   - id of requested lot
 *
 * @return null
 */
function getLotInfo($dbLink, int $id) {

  $sql = "
SELECT
  lots.id AS id,
  lots.name AS name,
  lots.url_image AS urlImg,
  lots.init_price AS initPrice,
  lots.rate_step AS rateStep,
  lots.text AS text,
  lots.user_author AS idAuthor,
  UNIX_TIMESTAMP(lots.date_end) AS dateEnd,
  categories.name AS category,
  MAX(rates.price) AS price
  
FROM `lots`
  JOIN `rates` ON lots.id = rates.lot
  JOIN `categories` ON lots.category = categories.id
  
WHERE lots.user_winner IS NULL
      AND lots.id = $id
";
  $resultQueryDB = mysqli_query($dbLink, $sql);

  //(( если запрос не прошел - показать ошибку ))
  $resultQueryDB ?: displayDataErrorFromDB($dbLink);

  $rowsQueryDB = mysqli_fetch_all($resultQueryDB, MYSQLI_ASSOC);

  $lotInfo = $rowsQueryDB[0];

  //(( если такого лота не существует, то вернуть null ))
  if (is_null($lotInfo["name"])) {
    return null;
  }

  //(( если ставок для лота нет, то текущая цена лота -его начальная цена ))
  !is_null($lotInfo["price"]) ?: $lotInfo["price"] = $lotInfo["initPrice"];

  //(( считаем текущую ставку на основании пред макс ставки и шага ставки ))
  $minRate = (integer)$lotInfo["price"] + (integer)$lotInfo["rateStep"];
  $lotInfo["minRate"] = $minRate;

  return $lotInfo;

}

//-------------------------------------
//    selecting all info about  one lot by it's id, include (rates)
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    selecting all rates (bets) for lot by it's id
//-------------------------------------

/**
 * get rates for lot by it's id
 *
 * @param $dbLink   - resource of connection with db
 * @param int $id   - id of requested lot
 *
 * @return array
 */
function getRatesForLot($dbLink, int $id) {
  $rates = [];
  $sql = "
SELECT 
  rates.price AS price,
  rates.user AS idUser,
  users.name AS name,
  UNIX_TIMESTAMP(rates.created_at) AS ts
FROM `rates`
  JOIN `users` ON rates.user = users.id
WHERE rates.lot = $id
      AND rates.created_at > '2017.08.01'
ORDER BY rates.created_at DESC
";

  $resultQueryDB = mysqli_query($dbLink, $sql);

  //(( если запрос не прошел - показать ошибку ))
  $resultQueryDB ?: displayDataErrorFromDB($dbLink);

  //(( 'вытряхиваем' результат из базды данных ))
  $rowsQueryDB = mysqli_fetch_all($resultQueryDB, MYSQLI_ASSOC);

  foreach ($rowsQueryDB as $row) {
    $rates[] = $row;
  }

  //(( если результат нулевой, возращаем просто пустой массив))
  if (count($rates) === 0) {
    $rates = [];
    return $rates;
  }

  //(( возвращаем нормальный массив с данными ))
  return $rates;

}

//-------------------------------------
//    selecting all rates (bets) for lot by it's id
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    insert new rate from user (using user Id)
//-------------------------------------


/**
 * setting user rate for certain lot
 *
 * @param $dbLink       - resource of connection with db
 * @param int $idLot    - id of requested lot
 * @param $userRate     - new user rate
 * @param int $idUser   - concrete user by id
 *
 * @return null
 */
function setNewUserRate($dbLink, int $idLot, $userRate, int $idUser) {

  //(( экранируем данные ))
  $safeUserRate = mysqli_real_escape_string($dbLink, $userRate);

  //(( сам запрос ))
  $sql = "
INSERT INTO rates (`price`, `lot`, `user`) VALUES (?, ?, ?);  
  ";

  $stmt = mysqli_prepare($dbLink, $sql);
  mysqli_stmt_bind_param($stmt, "iii", $safeUserRate, $idLot, $idUser);
  $resultQuery = mysqli_stmt_execute($stmt);

  //(( если запрос не прошел - показать ошибку ))
  $resultQuery ?: displayDataErrorFromDB($dbLink);

  return null;

}

//-------------------------------------
//    insert new rate from user (using user Id)
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    insert new Lot form user
//-------------------------------------

/**
 * check data from user and filling new lot from user
 *
 * @param $dbLink
 * @param array $lotInfo
 *
 * @return int|string
 */
function setNewLot($dbLink, array $lotInfo) {

  $lotInfoForSql = [];

  //(( экранируем данные ))
  foreach ($lotInfo as $lotInfoKey => $lotValue) {
    $lotInfoForSql[] = mysqli_real_escape_string($dbLink, $lotValue);
  }

  //(( сам запрос ))
  $sql = "
INSERT INTO `lots` (
  `name`,
  `text`,
  `url_image`,
  `init_price`,
  `rate_step`,
  `date_end`,
  `user_author`,
  `category`
)
VALUES (?,?,?,?,?,?,?,?)
  ";

  $stmt = db_get_prepare_stmt($dbLink, $sql, $lotInfoForSql);
  $resultQuery = mysqli_stmt_execute($stmt);

  //(( если запрос не прошел - показать ошибку ))
  $resultQuery ?: displayDataErrorFromDB($dbLink);

  return mysqli_insert_id($dbLink);

}

//-------------------------------------
//    insert new Lot form user
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    insert new signed up user   
//-------------------------------------

/**
 * check user data and setting new user in database
 *
 * @param $dbLink
 * @param $userInfo
 */
function setNewUser($dbLink, $userInfo) {

  $userInfoForSql = [];

  //(( экранируем данные ))
  foreach ($userInfo as $userInfoKey => $userValue) {
    if ($userInfoKey !== "avatarUrl") {
      $userInfoForSql[] = mysqli_real_escape_string($dbLink, $userValue);
    } else {
      $userInfoForSql[] = $userValue;
    }
  }

  //(( сам запрос ))
  $sql = "
INSERT INTO `users` (
  `email`,
  `name`,
  `password`,
  `avatar_url`,
  `contacts`
)
VALUES (?,?,?,?,?)
  ";

  $stmt = mysqli_prepare($dbLink, $sql);
  mysqli_stmt_bind_param(
      $stmt,
      "sssss",
      $userInfoForSql[0],
      $userInfoForSql[1],
      $userInfoForSql[2],
      $userInfoForSql[3],
      $userInfoForSql[4]
  );

  $resultQuery = mysqli_stmt_execute($stmt);

  //(( если запрос не прошел - показать ошибку ))
  $resultQuery ?: displayDataErrorFromDB($dbLink);

}

//-------------------------------------
//    insert new signed up user   
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//     get user by it's email
//-------------------------------------

/**
 * get info about user by his email
 *
 * @param $dbLink         - resource of connection with db
 * @param string $email
 *
 * @return null
 */
function getUserByEmail($dbLink, string $email) {

  $sql = "
SELECT 
users.email,
users.password
FROM `users`
WHERE users.email = '$email'
";

  $resultQueryDB = mysqli_query($dbLink, $sql);

  //(( если запрос не прошел - показать ошибку ))
  $resultQueryDB ?: displayDataErrorFromDB($dbLink);

  $rowsQueryDB = mysqli_fetch_all($resultQueryDB, MYSQLI_ASSOC);

  $userInfo = $rowsQueryDB;

  //(( если такого пользователя не существует, то вернуть null ))
  if (count($userInfo) === 0) {
    return null;
  }

  return $userInfo[0];
}

//-------------------------------------
//     get user by it's email
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//     get all rates by one user
//-------------------------------------

/**
 *  get rates for one user form database
 *
 * @param $dbLink
 * @param int $idUser
 *
 * @return array|null
 */
function getRatesByUser($dbLink, int $idUser) {

  $sql = "

SELECT 
rates.id AS rate_id,
rates.price AS rate_price,
UNIX_TIMESTAMP(rates.created_at) AS rate_createdAt,
lots.name AS lot_name,
lots.id AS lot_id,
lots.url_image AS lot_urlImage,
UNIX_TIMESTAMP(lots.date_end) AS lot_dateEnd,
lots.user_winner AS id_userWinner,
categories.name AS category_name,
users.contacts AS user_contacts

FROM `rates`
  JOIN `lots` ON rates.lot=lots.id
  JOIN `categories` ON lots.category=categories.id
  JOIN `users` ON lots.user_author = users.id 

WHERE 
rates.user = $idUser

";

  $resultQueryDB = mysqli_query($dbLink, $sql);

  //(( если запрос не прошел - показать ошибку ))
  $resultQueryDB ?: displayDataErrorFromDB($dbLink);

  $rowsQueryDB = mysqli_fetch_all($resultQueryDB, MYSQLI_ASSOC);

  $ratesInfo = $rowsQueryDB;

  //(( если нет ставок, то вернуть null ))
  if (count($ratesInfo) === 0) {
    return null;
  }

  return $ratesInfo;
}

//-------------------------------------
//     get all rates by one user
//--------------------------------------------------------------------------

?>