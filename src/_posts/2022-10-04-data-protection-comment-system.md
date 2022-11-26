---
comments_id: 70
comments_id: 69
date: 2022-10-04
title: "Protect sensitive data from being exposed to GitHub"
---

As described in my [previous post](comment-system-in-static-blog/) I added comments to my posts using GitHub issues.
I also mentioned that several other tools are not compliant to the GDPR. But why should GitHub be compliant?

Whenever the comments were loaded it was done with a call to [GitHub API](https://api.github.com/) So the visitor's IP address
was leaked to GitHub. I thought about several solutions to fix this issue. The best I came up with was to implement
a little proxy script which runs on my server and simply forwards the requests to GitHub. This way GitHub sees my server
IP only and not the one from the visitor. Much better!

## Proxy Script

The script has to handle two main tasks:

1. load the comments from GitHub issues
2. load the user's avatar from GitHub

```php
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
    $repository_name = curl_escape($ch, $_REQUEST['repo']);

    if ($repository_name !== false) {
        $repository = str_replace('%2F', '/', $repository_name);
        $issue_id = curl_escape($ch, $_REQUEST['issue_id']);

        $url = "https://api.github.com/repos/$repository/issues/$issue_id/comments";

        $headers[] = 'Accept: text/json';
    }
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
```

`curl` was the easiest way to implement the script. The personal access token is injected in the script at build time, and
it is not committed to the repository.
