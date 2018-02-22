<?

/**
 * Template engine
 *
 * @param $pathToTemplate
 * @param array $dataTemplate
 *
 * @return string
 */
function getContent($pathToTemplate, $dataTemplate = []) {

  $basePath = dirname(__FILE__, 2) . "\\" ;
  $pathTemplate = $basePath . $pathToTemplate;

  if (!file_exists($pathTemplate)) {
    echo "не найден файл";
    return '';
  }

  extract($dataTemplate);
  ob_start();
  require($pathTemplate);
  $renderHtml = ob_get_clean();

  return $renderHtml;

}

/**
 * Dispatching special status code / headers for 404 page
 *
 * @return null
 */
function send404() {

  http_response_code(404);
  header("HTTP/1.0 404 Not Found", false, 404);
  header("HTTP/1.1 404 Not Found", false, 404);
  header("Status: 404 Not Found", false, 404);

  require('./phpTemplates/errorTemplate.php');

  exit;
}

/**
 * Dispatching special status code / headers for error page (403)
 *
 * @return null
 */
function send403() {

  http_response_code(403);
  header("HTTP/1.1 403 Restricted Content", false, 403);
  header("HTTP/1.1 403 Restricted Content", false, 403);
  header("HTTP/1.1 403 Restricted Content", false, 403);

  require('./phpTemplates/errorTemplate.php');

  exit;
}

?>