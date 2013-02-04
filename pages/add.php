<?php
gatekeeper();
$title_text = elgg_echo("content_redirector:add:title");
$body = elgg_view("content_redirector/selector");
echo elgg_view_page($title_text, elgg_view_layout("one_column", array("title" => $title_text, "content" => $body)));
