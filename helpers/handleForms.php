<?

/**
 * Parses string fileName and extract it's extension
 *
 * @param string $path
 *
 * @return string
 */
function getExtension(string $path) {

  $path_info_extension = pathinfo($path, PATHINFO_EXTENSION);
  if (is_null($path_info_extension) || isEmptyString($path_info_extension)) {
    return '';
  }

  return $path_info_extension;

}

/**
 * Checking if the uploaded file is image with right extension
 *
 * @param array $uploadImage
 *
 * @return string
 */
function checkUploadImage(array $uploadImage) {

  $grantedMimeType = ["image/jpeg", "image/png"];

  $grantedExtensions = ["jpg", "jpeg", "png"];

  //(( отправлен ли хоть какой то файл ?  ))
  if ($uploadImage["error"] === 4) {
    return "fileIsNotFound";
  }

  //(( Проверяем, а действительно ли загружен файл пользователем через HTTP ))
  if (!(is_uploaded_file($uploadImage["tmp_name"]))) {
    return "somePhpError";
  }

  //(( удовлетворяет ли файл директивам php.ini ? ))
  if ($uploadImage["error"] !== 0) {
    return "somePhpError";
  }

  //(( проверка на допустимы MIME_TYPE ))
  if (!in_array(mime_content_type($uploadImage["tmp_name"]), $grantedMimeType)) {
    return "wrongMimeType";
  }

  //(( проверка расширени файла изображения ))
  if (!(in_array(getExtension($uploadImage["name"]), $grantedExtensions))) {
    return "wrongExtension";
  }

  return "";
}

/**
 * Checking correct user rate
 *
 * @param $costField
 * @param $minCost
 *
 * @return string
 */
function checkUserRateField($costField, $minCost) {

  //(( являтся ли переданное значение пустым? ))
  if (isEmptyString($costField)) {
    return "emptyRate";
  }

  //(( является ли цифрой значение поля lot-rate ? ))
  if (!(is_numeric($costField))) {
    return "notNumber";
  }

  //(( является ли ставка пользователя выше минимальной ? ))
  if(round($costField) < $minCost){
    return "lowRate";
  }

}

?>