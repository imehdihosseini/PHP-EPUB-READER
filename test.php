<?php

require_once('epub.class.php');
$epub = new pages('book');
$epub->SetRoot();
echo $epub->read('1-10');//read pages from 1 to 10

?>
