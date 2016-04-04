<?php

include "k2sUpload.class.php";

$upl = new k2sUpload('2c6b9b705ca93');

$genre = trim(file_get_contents('/arc/scr/upldir'));

//$upl->createFolder($genre);

//$upl->upload('/arc/upl/vids', 5);

$upl->multyUpload('x','x','x','x','x');
