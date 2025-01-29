<?php
function timeAgo($timestamp) {
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'now';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' min ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hr ago';
    } elseif ($diff < 172800) {
        return 'yesterday';
    } else {
        return date('M d, Y', $timestamp);
    }
}
?>
