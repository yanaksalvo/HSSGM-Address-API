<?php

if (getenv('REMOTE_ADDR') !== '127.0.0.1' && getenv('REMOTE_ADDR') !== '::1') {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(array(
        "succes" => false,
        "message" => "Erişiminiz yasaklanmıştır! Geçersiz IP adresi."
    ));
    exit;
}

$tc = htmlspecialchars($_GET['tc']);
if (strlen($tc) != 11) {
    echo json_encode(['status' => false, 'message' => '11 Haneli Tc Olmalidir'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    die;
}

function get($url, $cookies) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36',
        'Origin: null',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'sec-ch-ua: "Google Chrome";v="129", "Not=A?Brand";v="8", "Chromium";v="129"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    $dom = new DOMDocument();
    @$dom->loadHTML($response);
    $viewstate = $dom->getElementById('__VIEWSTATE')->getAttribute('value');
    $viewstategen = $dom->getElementById('__VIEWSTATEGENERATOR')->getAttribute('value');
    $eventvalidation = $dom->getElementById('__EVENTVALIDATION')->getAttribute('value');

    return [$viewstate, $viewstategen, $eventvalidation];
}

function post($cookies) {
    $url = 'https://interaktif.hssgm.gov.tr/Modules/GemiIslemleri/KisiKayit.aspx';
    list($viewstate, $viewstategen, $eventvalidation) = get($url, $cookies);
    $postData = http_build_query([
        '__EVENTTARGET' => '',
        '__EVENTARGUMENT' => '',
        '__VIEWSTATE' => $viewstate,
        '__VIEWSTATEGENERATOR' => $viewstategen,
        '__EVENTVALIDATION' => $eventvalidation,
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtKimlikNo' => $_GET['tc'],
        'ctl00$ctl00$contentBody$GemiIslemleriBody$btnMernis' => "MERNİS'ten Getir",
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtAd' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtSoyad' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtBabaAdi' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$drpCinsiyetListesi' => '-1',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtDogumTarihi' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtDogumYeri' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$drpUyrukListesi' => '-1',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtPasaportNo' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtMail' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtTelefon' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtAdres1' => '',
        'ctl00$ctl00$contentBody$GemiIslemleriBody$txtAdres2' => '',
    ]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookies); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    $headers = [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36',
        'Origin: null',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'sec-ch-ua: "Google Chrome";v="129", "Not=A?Brand";v="8", "Chromium";v="129"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"',
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
$res = json_decode(file_get_contents('cookies.json'), true);
$asp = $res[1]['value'];

$cookies = 'ASP.NET_SessionId='.$asp.'; AuthToken=';

$result = post($cookies);
$dom = new DOMDocument('1.0', 'UTF-8');
@$dom->loadHTML(mb_convert_encoding($result, 'HTML-ENTITIES', 'UTF-8'));

$xpath = new DOMXPath($dom);

$result = [
    'TC' => $xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$txtKimlikNo"]/@value')->item(0)->nodeValue,
    'ADI' => $xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$txtAd"]/@value')->item(0)->nodeValue,
    'SOYADI' => $xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$txtSoyad"]/@value')->item(0)->nodeValue,
    'BABA_ADI' => $xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$txtBabaAdi"]/@value')->item(0)->nodeValue,
    'DOGUM_TARIHI' => $xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$txtDogumTarihi"]/@value')->item(0)->nodeValue,
    'DOGUM_YERI' => $xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$txtDogumYeri"]/@value')->item(0)->nodeValue,
    'CINSIYET' => $xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$drpCinsiyetListesi"]/option[@selected="selected"]/text()')->item(0)->nodeValue,
    'UYRUK' => $xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$drpUyrukListesi"]/option[@selected="selected"]/text()')->item(0)->nodeValue,
    'ADRES' => trim($xpath->query('//*[@name="ctl00$ctl00$contentBody$GemiIslemleriBody$txtAdres1"]/text()')->item(0)->nodeValue),
];
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
