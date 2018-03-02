<?

//--------------------------------------------------------------------------
//    CheckUserEmail by php array
//-------------------------------------

/**
 * Checking if given user Email is in our store
 *
 * @param string $userEmail
 * @param array $dataUsers
 *
 * @return mixed
 */
function checkUserEmail(string $userEmail, array $dataUsers) {

  foreach ($dataUsers as $oneUser) {
    if ($userEmail === $oneUser["email"]) {
      return $oneUser;
    }
  }

  return false;

}

//-------------------------------------
//    CheckUserEmail by php array
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    CheckUserEmail by database
//-------------------------------------

/**
 * Checking if given user Email is in database
 *
 * @param $userEmail
 * @param $dbLink
 *
 * @return mixed
 */
function checkUserEmailDB($userEmail, $dbLink) {

  $safeUserEmail = mysqli_real_escape_string($dbLink, $userEmail);

  $sql = "
SELECT 
  users.id,
  users.email,
  users.name,
  users.password
FROM `users`
WHERE users.email = ?
";

  // подготавливаем запрос
  $stmt = db_get_prepare_stmt($dbLink, $sql, [$safeUserEmail]);

  // выполняем запрос
  $resultQuery = mysqli_stmt_execute($stmt);

  //(( если запрос не прошел - показать ошибку ))
  $resultQuery ?: displayDataErrorFromDB($dbLink);

  //(( 'вытряхиваем' результат из базды данных ))
  $resultQueryDB = mysqli_stmt_get_result($stmt);
  $rowsQueryDB = mysqli_fetch_all($resultQueryDB, MYSQLI_ASSOC);

  if (count($rowsQueryDB) === 0) {
    return false;
  }

  $userRow = $rowsQueryDB[0];

  foreach ($userRow as $userRowKey => $userRowValue) {
    if (is_string($userRowValue)) {
      $userRow[$userRowKey] = filterUserString($userRowValue);
    }
  }

  return $userRow;

}

//-------------------------------------
//    CheckUserEmail by database
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//    CheckUserPassword
//-------------------------------------

/**
 * Checking if given user password correct
 *
 * @param string $userPassword
 * @param string $userPasswordFromData
 *
 * @return bool
 */
function checkUserPassword(
    string $userPassword,
    string $userPasswordFromData
) {
  return password_verify($userPassword, $userPasswordFromData);

}

//-------------------------------------
//    CheckUserPassword
//--------------------------------------------------------------------------

//--------------------------------------------------------------------------
//     Authorize User
//-------------------------------------

/**
 * Authorizing user
 *
 * @param $userEmail
 * @param $userPassword
 * @param $users
 *
 * @return mixed
 */
function authorizeUser($userEmail, $userPassword, $dbLink) {

  //(( если email - пустая строка, то вернуть что email не заполнен ))
  if ($userEmail === '') {
    return "emptyEmail";
  }

  //(( если пользозвателя нет в наших списках, вернуть что email - неверный))

  $infoUser = checkUserEmailDB($userEmail, $dbLink);

  if (!$infoUser) {
    return "wrongEmail";
  }

  //(( если password - пустая строка, то вернуть что password не заполнен ))
  if ($userPassword === '') {
    return "emptyPassword";
  }

  //(( если пароль пользователя не подошел, вернуть что пароль - неверный))
  if (!checkUserPassword($userPassword, $infoUser["password"])) {
    return "wrongPassword";
  }

  //(( и email правильный, и пароль прошел проверку ))
  return $infoUser;

}

//-------------------------------------
//     Authorize User
//--------------------------------------------------------------------------


?>