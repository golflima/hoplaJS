<?php

/*
 * This file is part of hoplaJS.
 * See: <https://github.com/golflima/hoplaJS>.
 *
 * Copyright (C) 2017 Jérémy Walther <jeremy.walther@golflima.net>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Otherwise, see: <https://www.gnu.org/licenses/agpl-3.0>.
 */

require_once __DIR__ . '/vendor/autoload.php'; 

use Patchwork\JSqueeze;

function compress($js) {
    // Minify JS
    $jz = new JSqueeze();
    $compressed = $jz->squeeze(
        $js,
        true,   // $singleLine
        true,   // $keepImportantComments
        false   // $specialVarRx
    );
    // Compress JS
    $compressed = gzcompress($compressed, 9);
    // Base64 JS for URL
    $compressed = rtrim(strtr(base64_encode($compressed), '+/', '-_'), '=');
    return $compressed;
}

function decompress($compressed) {
    // Decode Base64 from URL
    $data = base64_decode(str_pad(strtr($compressed, '-_', '+/'), strlen($compressed) % 4, '=', STR_PAD_RIGHT));
    // decompress
    $data = gzuncompress($data);
    return $data;
}

if (!empty($_POST['js'])) {
    $base_url =  "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $data = compress($_POST['js']);
    echo '<a href="'.$base_url.'?js='.$data.'" target="_blank">js</a> | ';
    echo '<a href="'.$base_url.'?ht='.$data.'" target="_blank">html</a> | ';
    echo '<a href="'.$base_url.'?jq='.$data.'" target="_blank">jquery</a>';
    echo ' ('.strlen($base_url.'?js='.$data).')';
}
elseif (!empty($_GET['js'])) {
    die(decompress($_GET['js']));
}
elseif (!empty($_GET['ht'])) {
    die('<script type="text/javascript">'.decompress($_GET['ht']).'</script>');
}
elseif (!empty($_GET['jq'])) {
    die('<script type="text/javascript" src="assets/js/jquery-3.1.1.min.js"></script><script type="text/javascript">'.decompress($_GET['jq']).'</script>');
}
?>

<form method="POST" target="_self">
    <textarea name="js" rows="32" cols="128"><?php echo $_POST['js']; ?></textarea>
    <br />
    <button name="get_url" type="submit">Get URL !</button>
</form>