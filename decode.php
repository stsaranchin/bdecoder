<?php

include_once('bdecoder.php');

var_dump(BDecoder::parse(file_get_contents('resume.dat')));
