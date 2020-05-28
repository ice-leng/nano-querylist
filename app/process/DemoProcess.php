<?php

namespace App\Process;

use Hyperf\Process\AbstractProcess;
use QL\Ext\AbsoluteUrl;
use QL\QueryList;
use Swlib\Saber;

class DemoProcess extends AbstractProcess
{
    public function handle(): void
    {
        $channel = 'ypllt';
        $domain = "https://www.xxxx.com/";
        $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.120 Safari/535.2';
        $headers = [
            'User-Agent' => $agent,
//            'proxy'      => '175.43.179.11:9999',
        ];

        $ql = QueryList::getInstance();
        $ql->use(AbsoluteUrl::class);
        $http = Saber::session([
            'headers'    => $headers,
            'retry_time' => 5,
        ])->get($domain);
        $html = $http->getBody()->getContents();
        $result = $ql->html($html)->rules([
            'content' => ['ul', 'html', '-.top_3'],
        ])->range('.card-body')->queryData(function ($item) use ($ql, $domain) {
            $item['content'] = $ql->html($item['content'])->absoluteUrl($domain)->rules([
                'province' => ['a:eq(2)', 'text'],
                'url'      => ['a:eq(1)', 'href'],
                'name'     => ['a:eq(1)', 'text'],
            ])->range('.subject')->queryData();
            return $item;
        });
        var_dump($result);
    }
}
