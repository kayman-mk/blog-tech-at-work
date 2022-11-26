<?php
$ch = curl_init();

$headers = [
    'Authorization: token __GH_API_TOKEN_COMMENT_SYSTEM__REPLACE_ME__',
    'Accept-Encoding: gzip, deflate',
    'Cache-Control: no-cache',
    'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0'
];

$url = "";
$action = curl_escape($ch, $_REQUEST['action']);

if ($action == "fetch_comments") {
    $repository = str_replace('%2F', '/', curl_escape($ch, $_REQUEST['repo']));
    $issue_id = curl_escape($ch, $_REQUEST['issue_id']);

    $url = "https://api.github.com/repos/$repository/issues/$issue_id/comments";

    $headers[] = 'Accept: text/json';
} else if ($action == "get_avatar") {
    $username = curl_escape($ch, $_REQUEST['username']);

    $url = "https://github.com/$username";

    $headers[] = 'Accept: image/png';
} else {
    curl_close($ch);

    return;
}

if ($action > "") {
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    echo $response;

    curl_close($ch);
}
