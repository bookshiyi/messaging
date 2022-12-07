<?php

namespace Utopia\Messaging\Adapters;

use Utopia\Messaging\Adapter;
use Utopia\Messaging\Message;
use Utopia\Messaging\Messages\Push as PushMessage;

abstract class Push extends Adapter
{
    public function getType(): string
    {
        return 'push';
    }

    public function getMessageType(): string
    {
        return PushMessage::class;
    }

    /**
     * Send a push message.
     *
     * @param PushMessage $message Message to process.
     * @return string The response body.
     */
    abstract protected function process(PushMessage $message): string;
}
