<?php

date_default_timezone_set('Asia/Tashkent');
// Sáne formatı: Jıl-ay-kún (2021-09-18)
$date = date('Y-m-d');

// Járdemshi funkciyalardı qosamız
require_once dirname(__FILE__) . '/helper.php';

// Tiykarǵı ózgermesler (konstantalar)
define('METHOD', $_SERVER['REQUEST_METHOD']);
// Júkleniwi kerek bolǵan fayl
define('FILE', $_FILES['file']);
// Kelgen fayllar ushın jol (path)     ms: .../static/2021-09-18
define('TARGET_DIR', dirname(__FILE__) . '/static/' . $date);
// Qabıllaytuǵın HTTP metodlar massivi
define('ALLOWED_METHODS', ['POST', 'GET']);


// HTTP metodı tekserip kóriw
if (!in_array(METHOD, ALLOWED_METHODS)) {
  response(405, METHOD . ' metodı qabıllanbaydı.');
}

// Eger 'GET' metodı kelse documentaciya betine jiberemiz (redirect)
if (METHOD == 'GET') {
  header('Location: docs', true);
  exit; // Scriptti juwmaqlaymız sebebi fayl joq bolsa tómendegi kodtı júrgiziwden (run) payda joq.
}

// File júklewdegi qátelikler
if (boolval(FILE['error'])) {
  $max_file_size = ini_get('upload_max_filesize'); // PHP diń konfiguraciyasınan fayllardı júklew ushın maksimal fayl kólemine limitin alamız.
  $php_file_upload_errors = [
    'Heshqanday qátelik joq.',    // Bul qatardı óshirmeń.
    'Júklengen fayl kólemi ' . $max_file_size . 'B limitten artıq.',
    'Júklengen fayl kólemi HTML formada belgilenge MAX_FILE_SIZE direktivasın artıq.',
    'Fayldıń tek ǵana bir bólegi júklendi.',
    'Fayl bos.',
    'Waqtınsha papka belgilenbegen (joq).',
    'Fayldı jazıwda qátelik.',
    'PHP qosımshası (extension) fayl júkleniwin toqtattı.',
  ];
  response(500, $php_file_upload_errors[FILE['error']]);
}

$original_filename = basename(FILE['name']);
$tmp = explode('.', $original_filename);
// Fayl keńeytpisi
define('FILE_EXTENSION', strtolower(end($tmp)));

// Búgingi sánege papka jaratılǵanlıǵın tekseremiz bolmasa papka jaratamız
if (!is_dir(TARGET_DIR)) {
  mkdir(TARGET_DIR);
}

/* ------------------- Fayldı serverge júklew ------------------- */

/*
  Fayl atın házirgi unix waqıtı boyınsh md5 funkciyasında shifrlaymız.
  Maqset fayl atları menen konflict júzege keltirmew.
*/
$new_file_name = substr(md5(time() . $original_filename), 0, 12);
$target_file = TARGET_DIR . '/' . $new_file_name . '.' . FILE_EXTENSION;

if (move_uploaded_file(FILE['tmp_name'], $target_file)) {
  header('Content-Type: application/json', true, 200);
  echo json_encode([
    'message' => 'Fayl áwmetli túrde júklendi.',
    'src' => "static/{$date}/{$new_file_name}." . FILE_EXTENSION,
    'url' => "https://$_SERVER[HTTP_HOST]/static/{$date}/{$new_file_name}." . FILE_EXTENSION
  ]);
} else {
  response(500, 'Fayldı júklewde serverde qátelik júz berdi.');
}
