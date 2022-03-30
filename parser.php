<?php

namespace Parser;
require_once 'Log_Parser.php';
$argv = $_SERVER['argv'];

if (isset($argv[1]))
{
    $parser = new Log_Parser($argv[1]);
    $parser->read_log_file();
    $output = $parser->print_results();
    print_r($output);
}
else
{
    print_r('Нужно указать файл!!!');
}
