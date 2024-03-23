<?php

namespace Utopia\Tests\Adapter\SMS;

use Utopia\Messaging\Adapter\SMS\AlibabaCloud;
use Utopia\Messaging\Messages\SMS;
use Utopia\Tests\Adapter\Base;

class AlibabaCloudTest extends Base
{
    /**
     * @throws \Exception
     */
    public function testSendSMS(): void
    {
        $sender = new AlibabaCloud(
            \getenv('ALIBABACLOUD_ACCESS_KEY_ID'), 
            \getenv('ALIBABACLOUD_ACCESS_KEY_SECRET'), 
            \getenv('ALIBABACLOUD_TEMPLATE_CODE'));
        $message = new SMS(
            from: \getenv('ALIBABACLOUD_FROM'),
            to: [\getenv('ALIBABACLOUD_TO')],
            content: '123456'
        );

        $response = $sender->send($message);

        $this->assertResponse($response);
    }
}
