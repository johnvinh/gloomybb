<?php
/**
 * Construct a forum navigation from an array of associative arrays.
 *
 * @param $links
 * @return string
 */
function construct_navigation($links): string
{
    $output = '';
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i]['url'];
        $name = $links[$i]['name'];
        if ($i !== (count($links) - 1))
            $output .= '<a href="' . $link . '">' . $name . '</a> -> ';
        else
            $output .= '<a href="' . $link . '">' . $name . '</a>';
    }
    return $output;
}