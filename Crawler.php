<?php

use QL\QueryList;
class Crawler
{
    protected function s()
    {
        $reportUrl = 'http://api.ttshitu.com/base64';
        $img_content = file_get_contents('C:\Users\Administrator\Desktop\76.jpg');
        $image = base64_encode($img_content);
        $ch = curl_init();
        $postFields = array('username' => '用户名',    //改成你自己的
                            'password' => '密码',    //改成你自己的
                            'typeid' => '3',  //改成你自己的
                            'image' => $image
        );
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $reportUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);  //设置本机的post请求超时时间
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $data = curl_exec($ch);
        curl_close($ch);

        var_dump("返回结果:" .$data);
        if (json_decode($data)->success){
            $result = json_decode($data)->data->result;//识别的结果
            var_dump("识别结果:".$result);
        }else{
            $message = json_decode($data)->message;//识别的结果
            var_dump("错误原因:".$message);
        }
    }
}
