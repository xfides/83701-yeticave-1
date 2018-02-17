<?

/**
 * Processing of custom / user's strings
 *
 * @param $userString
 *
 * @return string
 */
function filterUserString($userString) {

  $userString = is_string($userString) ? $userString : '';
  $userString = strip_tags($userString);
  $userString = stripslashes($userString);
  $userString = trim($userString);
  $userString = htmlspecialchars($userString);

  return $userString;

}

/**
 * Check if user or another string == ''
 *
 * @param $someStr
 *
 * @return bool
 */
function isEmptyString($someStr) {
  return (strlen(trim($someStr)) === 0);
}

/**
 * Turning timestamp into readable human view
 *
 * @param $givenTimestamp
 * @param $lateTimestamp
 *
 * @return string
 */
function parseToHumanTime($givenTimestamp, bool $lateTimestamp = false) {

  //(( явно приводим  временную метку к числу ))
  $givenTimestamp = (integer)$givenTimestamp;

  /* get left time to end of bet */
  $now = strtotime('now');

  $betTimeUntil = 0;

  if ($lateTimestamp) {
    $betTimeUntil = $givenTimestamp - $now;

    //(( пользователи могут запросить свои ставки, которые уже истекли ))
    if($betTimeUntil <= 0){
      return "время истекло";
    }

  } else {
    $betTimeUntil = $now - $givenTimestamp;
  }

  /* parse time into parts */
  $betDaysUntil = gmdate("d", $betTimeUntil);
  $betMonthUntil = gmdate("m", $betTimeUntil);
  $betYearsUntil = gmdate("y", $betTimeUntil);
  $betHoursUntil = gmdate("H", $betTimeUntil);
  $betMinutesUntil = gmdate("i", $betTimeUntil);

  /* different readable output for humans mind */
  if (($betDaysUntil > 1) || ($betMonthUntil > 1) || ($betYearsUntil > 70)) {

    if ($lateTimestamp) {
      return (string)floor($betTimeUntil / 60 / 60 / 24) . " days";
    } else {
      return gmdate('d.m.y в H:i', $givenTimestamp);
    }

  }

  if ($betHoursUntil >= 1 && $betHoursUntil < 24) {

    if ($lateTimestamp) {
      return (string)$betHoursUntil . ":" . (string)$betMinutesUntil;
    } else {
      return (string)$betHoursUntil . " часов назад";
    }

  }

  if ($lateTimestamp) {
    return (string)$betMinutesUntil . " минут";
  } else {
    return (string)$betMinutesUntil . " минут назад";
  }


}

?>