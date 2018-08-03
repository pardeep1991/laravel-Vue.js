<?php

namespace App\Console\Commands;

use App\Task;
use App\Result;
use App\Keyword;
use App\Helpers\IP2RegionHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

use Carbon\Carbon;

class Crawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Autorun crawler';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->IP2Region = new IP2RegionHelper(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . 'ip2region.db');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $task = Task::with('keyword')->where('status', 'Pending')->orderBy('created_at')->first();

        if ($task) {
            $task->update(['status' => 'Processing']);
            $keyword_data = $task->keyword;
            $keyword = Keyword::find($keyword_data->id);
            $search_engines = $task->search_engines;
            $client = new Client();
            $headers = array(
                'Connection' => 'keep-alive',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'no-cache',
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8,ja;q=0.7,zh-TW;q=0.6'
            );

            if (in_array("baidu", $search_engines)) {
                
                $resp = $client->request('GET', 'https://www.baidu.com/s?wd='.$keyword_data->keyword.'&rn='.$task->keyword_result_number, [
                    'headers' => $headers
                ]);
                $dom = new \DOMDocument();
                @$dom->loadHTML($resp->getBody());
                $xpath = new \DOMXPath($dom);
                $items = $xpath->query("//div[contains(@class, 'result')]");

                foreach ($items as $node) {
                    $result = new Result;
                    $result->keyword_id = $keyword_data->id;
                    $result->search_engine = 'baidu';
                    $result->url_status = 'unreviewed';
                    foreach ($xpath->query(".//h3/a", $node) as $child) {
                        $result->title = $child->nodeValue;
                        $url = $client->request('GET', $child->getAttribute('href'), [
                            'headers' => $headers,
                            'allow_redirects' => false
                        ])->getHeader('Location')[0];

                        $result->url = $url;
                        $host = parse_url($url, PHP_URL_HOST);
                        $ip = gethostbyname($host);
                        if ($ip != $host) {
                            $result->ip = $ip;
                            $result->ip_region = $this->IP2Region->btreeSearch($ip)['region'];
                        } else {
                            $result->url_status = 'KO';
                        }
                    }
                    foreach ($xpath->query(".//div[contains(@class, 'c-abstract')]") as $child) {
                        $result->description = $child->nodeValue;
                    }
                    foreach ($xpath->query(".//a[contains(@class, 'm')]") as $child) {
                        $result->url_redirected = $child->getAttribute('href');
                    }
                    if ($result->title) {
                        $result->save();
                    }
                }
                $keyword->update(['baidu_crawler_exec_date' => Carbon::now()]);
            }

            if (in_array("bing", $search_engines)) {
                $resp = $client->request('GET', 'https://www.bing.com/search?q='.$keyword_data->keyword, [
                    'headers' => $headers
                ]);

                $dom = new \DOMDocument();
                @$dom->loadHTML($resp->getBody());
                $xpath = new \DOMXPath($dom);
                $items = $xpath->query("//li[contains(@class, 'b_algo')]");

                foreach ($items as $node) {
                    $result = new Result;
                    $result->keyword_id = $keyword_data->id;
                    $result->search_engine = 'bing';
                    $result->url_status = 'unreviewed';
                    foreach ($xpath->query(".//h2/a", $node) as $child) {
                        $result->title = $child->nodeValue;
                        $url = $child->getAttribute('href');

                        $result->url = $url;
                        $host = parse_url($url, PHP_URL_HOST);
                        $ip = gethostbyname($host);
                        if ($ip != $host) {
                            $result->ip = $ip;
                            $result->ip_region = $this->IP2Region->btreeSearch($ip)['region'];
                        } else {
                            $result->url_status = 'KO';
                        }
                    }
                    foreach ($xpath->query(".//div[contains(@class, 'b_snippet')]") as $child) {
                        $result->description = $child->nodeValue;
                    }
                    foreach ($xpath->query(".//a[contains(@class, 'b_attribution')]") as $child) {
                        $u = $child->getAttribute('href');
                        $u_array = explode("|", $u);
                        if(count($u_array) == 4) {
                            $result->url_redirected = 'http://cc.bingj.com/cache.aspx?q=test&d='.$u_array[2].'&w='.$u_array[3];
                        }
                    }
                    $result->save();
                }
                $keyword->update(['bing_crawler_exec_date' => Carbon::now()]);
            }
            
            if (in_array("360", $search_engines)) {
                $resp = $client->request('GET', 'https://www.so.com/s?q='.$keyword_data->keyword, [
                    'headers' => $headers
                ]);

                $dom = new \DOMDocument();
                @$dom->loadHTML($resp->getBody());
                $xpath = new \DOMXPath($dom);
                $items = $xpath->query("//li[contains(@class, 'res-list')]");

                foreach ($items as $node) {
                    $result = new Result;
                    $result->keyword_id = $keyword_data->id;
                    $result->search_engine = '360';
                    $result->url_status = 'unreviewed';
                    foreach ($xpath->query(".//h3/a", $node) as $child) {
                        $result->title = $child->nodeValue;

                        $raw = $client->request('GET', $child->getAttribute('href'), [
                            'headers' => $headers,
                            'allow_redirects' => false
                        ])->getBody();

                        preg_match('/<meta http-equiv="refresh" content="0;URL=\'(.*?)\'">/', $raw, $match);

                        if(count($match) == 0) {
                            $result->url = $child->getAttribute('href');
                        } else {
                            $result->url = $match[1];
                        }

                        $host = parse_url($result->url, PHP_URL_HOST);
                        $ip = gethostbyname($host);
                        if ($ip != $host) {
                            $result->ip = $ip;
                            $result->ip_region = $this->IP2Region->btreeSearch($ip)['region'];
                        } else {
                            $result->url_status = 'KO';
                        }

                    }
                    foreach ($xpath->query(".//p[contains(@class, 'res-desc')]") as $child) {
                        $result->description = $child->nodeValue;
                    }
                    foreach ($xpath->query(".//p[contains(@class, 'res-linkinfo')]/a[@class='m']") as $child) {
                        $result->url_redirected = $child->getAttribute('href');
                    }
                    $result->save();
                }
                $keyword->update(['360_crawler_exec_date' => Carbon::now()]);
            }

            $task->update(['status' => 'Succeed']);  
        }
    }
}
