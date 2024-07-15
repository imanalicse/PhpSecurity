Fix SQL injection and cross site scripting attract by validating cookie
<?php
// call from before filter
validateCookieAndTerminate();

function validateCookieAndTerminate() {
    $csrfToken = $this->request->getCookie('csrfToken');
    $rgs_cookie = $this->request->getCookie('RGS');
    if ($csrfToken && !isAlphaNumeric($csrfToken)) {
        http_response_code(400);
        die('Invalid csrfToken cookie');
    }
    else if ($rgs_cookie && !isAlphaNumeric($rgs_cookie)) {
        http_response_code(400);
        die('Invalid RGS cooke');
    }
}

function isAlphaNumeric($string) : bool {
    if (ctype_alnum($string)) {
        return true;
    }
    return false;
}