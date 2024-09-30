<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AliDySms
{
    // 阿里云短信配置
    private string $apiurl = 'https://dysmsapi.aliyuncs.com/?';

    private array $config = [
        //        'key' => '', //  Access Key ID
        //        'secret' => '', // Access Key Secret
        //        'sign_name' => '' // 短信签名
    ];

    public function __construct()
    {
        $this->config = config('services.ali_sms');
    }

    /**
     * 阿里云 短信验证
     *
     * @param  $code  // 验证码，变量，如263159
     * @param  string  $template_code  短信模版code
     *
     * @throws \Exception
     */
    public function captcha(string $code, string $tels, string $template_code = 'SMS_297490083'): void
    {
        $data = [
            // 公共参数
            'SignName' => $this->config['sign_name'],
            'Format' => 'JSON',
            'Version' => '2017-05-25',
            'AccessKeyId' => $this->config['key'],
            'SignatureVersion' => '1.0',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid(),
            'Timestamp' => now('GMT')->toIso8601String(),
            // 接口参数
            'RegionId' => 'cn-hangzhou',
            'Action' => 'SendSms',
            'TemplateCode' => $template_code, // 短信模板CODE
            'PhoneNumbers' => $tels, // 目标手机号，多个手机号可以逗号分隔
            'TemplateParam' => '{"code":"'.$code.'"}',
        ];
        //        dd($data);
        // 计算签名并把签名结果加入请求参数
        $data['Signature'] = $this->computeSignature($data, $this->config['secret']);
        // 发送请求 返回json转成数组
        $result = Http::get($this->apiurl.http_build_query($data))->json();

        if (array_key_exists('Code', $result) && $result['Code'] != 'OK') {
            throw new \Exception($result['Code'].': '.$result['Message']);
        }
    }

    /**
     * 实现签名
     * https://help.aliyun.com/document_detail/44363.html.
     */
    public function computeSignature($parameters, $accessKeySecret): string
    {
        // 将参数Key按字典顺序排序
        ksort($parameters);
        // 生成规范化请求字符串
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&'.$this->percentEncode($key).'='.$this->percentEncode($value);
        }
        // 生成用于计算签名的字符串 stringToSign
        $stringToSign = 'GET&%2F&'.$this->percentencode(substr($canonicalizedQueryString, 1));
        // 计算签名，注意accessKeySecret后面要加上字符'&'
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret.'&', true));

        return $signature;
    }

    /**
     * 编码规范处理.
     */
    public function percentEncode(string $str): string
    {
        // 使用urlencode编码后，将"+","*","%7E"做替换即满足ECS API规定的编码规范
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);

        return preg_replace('/%7E/', '~', $res);
    }

    public function sendSms(string $tels, string $template_code, $param = null): void
    {
        $data = [
            // 公共参数
            'SignName' => $this->config['sign_name'],
            'Format' => 'JSON',
            'Version' => '2017-05-25',
            'AccessKeyId' => $this->config['key'],
            'SignatureVersion' => '1.0',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid(),
            'Timestamp' => now('GMT')->toIso8601String(),
            // 接口参数
            'RegionId' => 'cn-hangzhou',
            'Action' => 'SendSms',
            'TemplateCode' => $template_code, // 短信模板CODE
            'PhoneNumbers' => $tels, // 目标手机号，多个手机号可以逗号分隔
        ];
        if (! empty($param)) {
            $data['TemplateParam'] = $param;
        }
        //        dd($data);
        // 计算签名并把签名结果加入请求参数
        $data['Signature'] = $this->computeSignature($data, $this->config['secret']);
        // 发送请求 返回json转成数组
        $result = Http::get($this->apiurl.http_build_query($data))->json();

        if (array_key_exists('Code', $result) && $result['Code'] != 'OK') {
            throw new \Exception($result['Code'].': '.$result['Message']);
        }
    }
}
