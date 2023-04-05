<?php
function getMainDomain($host) {
    $domainParts = explode('.', $host);
    $domainPartsCount = count($domainParts);

    if ($domainPartsCount > 1) {
        $mainDomain = $domainParts[$domainPartsCount - 2] . '.' . $domainParts[$domainPartsCount - 1];
        return $mainDomain;
    }
    return $host;
}

function storeUtmParametersInCookieAndRedirect($redirectTarget) {
    $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $parsedUrl = parse_url($currentUrl);

    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $queryParams);

        $utmParameters = array();
        foreach ($queryParams as $key => $value) {
            if (strpos($key, 'utm_') === 0) {
                $utmParameters[$key] = $value;
            }
        }

        if (!empty($utmParameters)) {
            $encodedUtmParameters = http_build_query($utmParameters);
            $mainDomain = getMainDomain($_SERVER['HTTP_HOST']);
            setcookie("_shopify_sa_p", $encodedUtmParameters, time() + (86400 * 90), "/", '.' . $mainDomain); // Expires in 90 days
        }
    }

    header("Location: " . $redirectTarget, true, 301);
    exit();
}
?>
