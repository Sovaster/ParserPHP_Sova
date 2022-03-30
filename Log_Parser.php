<?php

namespace Parser;

class Log_Parser
{
    private $path_to_file;
    private $views = 0;
    private $urls = 0;
    private $urls_arr = array();
    private $traffic = 0;
    private $crawlers = array(
        'Google' => 0,
        'Apple' =>0,
        'Gecko' =>0,
        'Trident' =>0,
        'Bing' => 0,
        'Baidu' => 0,
        'Yandex' => 0,
        'Rambler' =>0,
    );
    private $status_codes = array();

    function __construct(string $log_path)
    {
        $this->path_to_file = $log_path;
    }

    public function read_log_file()
    {
        $file = fopen($this->path_to_file,'r') or die ('Не удаётся открыть указанный файл!!!');
        while (!feof($file)) {
            $log_line = trim(fgets($file));
            $this->parse_string_of_log_file($log_line);
        }
        fclose($file);
    }

    public function print_results():array
    {
        $results = array();
        $results['traffic'] = $this->traffic;
        $results['views'] = $this->views;
        $results['unique_urls'] = $this->urls;
        $results['urls_list'] = $this->urls_arr;
        $results['crawlers'] = $this->crawlers;
        $results['status_codes'] = $this->status_codes;
        return $results;
    }

    private function parse_string_of_log_file(string $log_str)
    {
        $pattern = "/(\S+)(\s+-|\S) (\S+|-) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\d+|\d+ - \d+) (\d+) \"(.*?)\" \"(.*?((\) (.*?)\/)|((.*?;){4}) (.*?)\/).*?)\"/";
        preg_match($pattern, $log_str, $log_str_arr);

        $ip = $log_str_arr[1];
        $method = $log_str_arr[7];
        $url = $log_str_arr[8];
        $status = $log_str_arr[10];
        $transfer = $log_str_arr[11];
        $referer = $log_str_arr[12];
        $crawler = $log_str_arr[16];
        if ($crawler == '')
            $crawler = $log_str_arr[19];


        if (!in_array($url,$this->urls_arr)) {
            $this->urls++;
            $this->urls_arr[] = $url;
        }

        $this->traffic += $transfer;

        $status_arr = explode(' ',trim($status));
        $status = $status_arr[0];
        $this->views++;
        if (array_key_exists($status, $this->status_codes)) {
            $this->status_codes[$status]++;
        } else {
            $this->status_codes[$status] = 1;
        }

        if (!in_array($url,$this->urls_arr)) {
            $this->urls++;
            $this->urls_arr[] = $url;
        }

        foreach ($this->crawlers as $key => $value) {
            if(preg_match('/^' . $key . '(.*?)/i',$crawler))
                $this->crawlers[$key]++;
        }
    }
}