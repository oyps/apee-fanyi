<?php

require('./Lang_info.php');
/**
 * 有道翻译接口转换为项目接口
 * @author 欧阳鹏
 * @since 2023-01-13
 */
class Youdao_fanyi implements ITranslate
{
    public function trans(string $text, int $from, int $to): array
    {
        $value_salt = time() . '0000';
        $value_sign = md5("fanyideskweb{$text}{$value_salt}Ygy_4c=r#e#4EX^NUGUc5");
        $value_lts = time() . '000';
        $cookie_id = rand(0, 9) . "@" . rand(0, 9) . "." . rand(0, 9) . "." . rand(0, 9) . "." . rand(0, 9);
        $curl = curl_init('https://fanyi.youdao.com/translate_o');
        $post_data = [
            'i' => $text,
            'from' => Lang_info::$youdao_lang[$from],
            'to' => Lang_info::$youdao_lang[$to],
            'smartresult' => 'dict',
            'client' => 'fanyideskweb',
            'salt' => $value_salt,
            'sign' => $value_sign,
            'doctype' => 'json',
            'version' => '2.1',
            'keyfrom' => 'fanyi.web',
            'action' => 'FY_BY_REALTlME',
            'lts' => $value_lts
        ];
        // 设置 POST 请求方式
        curl_setopt($curl, CURLOPT_POST, true);
        // 设置返回字符串
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // 设置 Referer 请求头
        curl_setopt($curl, CURLOPT_REFERER, 'oyp');
        // 设置 User-Agent 请求头
        curl_setopt($curl, CURLOPT_USERAGENT, 'oyp');
        // 设置 Cookie
        curl_setopt($curl, CURLOPT_COOKIE, 'OUTFOX_SEARCH_USER_ID=' . $cookie_id);
        // 设置 POST 参数
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        // 发送请求
        $result = curl_exec($curl);
        $data = json_decode($result, true);
        $list = $data['translateResult'] ?? [];
        for ($x = 0; $x < count($list); $x++) {
            for ($y = 0; $y < count($list[$x]); $y++) {
                $list[$x][$y]['text'] = $list[$x][$y]['tgt'];
                unset($list[$x][$y]['tgt']);
            }
        }
        return $list;
    }
    public function start(): array
    {
        $param = $this->get_param();
        return $this->trans($param['text'], $param['from'], $param['to']);
    }
    public function get_param(): array
    {
        $from = (int)($_POST['from'] ?? $_GET['from'] ?? 0);
        $to = (int)($_POST['to'] ?? $_GET['to'] ?? 0);
        $text = $_POST['text'] ?? $_GET['text'] ?? '你好世界';
        return [
            'text' => $text,
            'from' => $from,
            'to' => $to
        ];
    }
}


interface ITranslate
{
    /**
     * 获取翻译结果
     * @param string $text 待翻译文本
     * @param int $from 来源语言代码
     * @param int $to 目标语言代码
     * @return array 翻译结果（二维对象数组，包含 `text` 属性表示翻译结果）
     */
    public function trans(string $text, int $from, int $to): array;
    /**
     * 启动翻译 API 服务
     * @return array 翻译结果（二维对象数组，包含 `text` 属性表示翻译结果）
     */
    public function start(): array;
    /**
     * 获取 POST 或 GET 请求参数（优先 POST）
     * @return array 关联数组，请求参数
     */
    public function get_param(): array;
}
