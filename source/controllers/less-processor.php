<?php
$file = (isset($_REQUEST['file']) ? $_REQUEST['file'] : '');
if ( ($file) && (file_exists(TYPEF_DIR . $file)) && (is_file(TYPEF_DIR . $file)) ) {
	header('Content-type: text/css');
	$dir = dirname($file);
	$md5 = md5($file) . '.css';
	$dst = TYPEF_DIR . '/files/cache/lessphp/' . $md5;
	lessc::ccompile(TYPEF_DIR . $file, $dst);
	$css = SimpleCss::LoadFile($dst, TYPEF_WEB_DIR . $dir);
	$code = $css->toString();
	preg_match_all('/url\(\"([\w\W\s\S]*?)"\)/', $code, $matches);
	if (isset($matches[1])) {
		foreach ($matches[1] as $url) {
			$url = Typeframe_Attribute_Url::ConvertShortUrlToExpression($url);
			$url = Typeframe::Pagemill()->data()->parseVariables($url);
			$code = str_replace($matches[1], $url, $code);
		}
	}
	echo $code;
	exit;
} else {
	http_response_code(404);
	Typeframe::SetPageTemplate('/404.html');
	Typeframe::CurrentPage()->stop();
}
