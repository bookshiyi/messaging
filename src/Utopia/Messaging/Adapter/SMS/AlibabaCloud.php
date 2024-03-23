<?php

namespace Utopia\Messaging\Adapter\SMS;

use Utopia\Messaging\Adapter\SMS as SMSAdapter;
use Utopia\Messaging\Messages\SMS as SMSMessage;
use Utopia\Messaging\Response;

use Darabonba\OpenApi\OpenApiClient;
use AlibabaCloud\OpenApiUtil\OpenApiUtilClient;

use Darabonba\OpenApi\Models\Config;
use Darabonba\OpenApi\Models\Params;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Darabonba\OpenApi\Models\OpenApiRequest;

class AlibabaCloud  extends SMSAdapter{
    
    protected const NAME = 'AlibabaCloud';

    /**
     * @param  string  $accessKeyId AlibabaCloud Access Key ID
     * @param  string  $accessKeySecret AlibabaCloud Access Key Secret
     * @param  string  $signName AlibabaCloud SMS Sign Name
     * @param  string  $templateCode AlibabaCloud SMS Template Code
     */
    public function __construct(
        private string $accessKeyId,        
        private string $accessKeySecret,    
        private string $signName,           
        private string $templateCode,       
    ) {
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function getMaxMessagesPerRequest(): int
    {
        return 1;
    }
    /**
     * Create OpenApiClient
     * @return OpenApiClient
     */
    private function createClient(){
        $config = new Config([
            "accessKeyId" => $this->accessKeyId,
            "accessKeySecret" => $this->accessKeySecret
        ]);
        $config->endpoint = "dysmsapi.aliyuncs.com";
        $client = new OpenApiClient($config);
        return $client;
    }

    /**
     * API Info
     * @return Params OpenApi.Params
     */
    private static function createApiInfo(){
        $params = new Params([
            "action" => "SendSms",
            "version" => "2017-05-25",
            "protocol" => "HTTPS",
            "method" => "POST",
            "authType" => "AK",
            "style" => "RPC",
            "pathname" => "/",
            "reqBodyType" => "json",
            "bodyType" => "json"
        ]);
        return $params;
    }
    /**
     * {@inheritdoc}
     */
    protected function process(SMSMessage $message): array
    {
        $client = self::createClient();
        $params = self::createApiInfo();
        $queries = [
            "PhoneNumbers" => $message->getTo()[0],
            "SignName" => $this->signName,
            "TemplateCode" => $this->templateCode,
            "TemplateParam" => json_encode(["code" => $message->getContent()])
        ];

        // runtime options
        $runtime = new RuntimeOptions([]);
        $request = new OpenApiRequest([
            "query" => OpenApiUtilClient::query($queries)
        ]);
        
        $response = new Response($this->getType());
        try {
            $result = $client->callApi($params, $request, $runtime);
            var_dump($result);
            if($result['statusCode'] == 200){
                $response->setDeliveredTo(1);
                $response->addResult($message->getTo()[0]);
            }
            
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $response->addResult($message->getTo()[0], $e->getMessage());
        }
        return $response->toArray();
    }
}
