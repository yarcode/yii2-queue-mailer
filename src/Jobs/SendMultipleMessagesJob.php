<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace YarCode\Yii2\QueueMailer\Jobs;

use YarCode\Yii2\QueueMailer\Mailer;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\mail\MessageInterface;
use yii\queue\JobInterface;

class SendMultipleMessagesJob implements JobInterface
{
    /** @var MessageInterface[] */
    public $messages = [];
    /** @var string */
    public $mailer;

    /**
     * @inheritdoc
     * @see JobInterface::execute()
     *
     * @throws InvalidConfigException
     * @throws \InvalidArgumentException
     */
    public function execute($queue)
    {
        if (!is_array($this->messages)) {
            throw new \InvalidArgumentException('Message must be an instance of ' . MessageInterface::class);
        }
        /** @var Mailer $mailer */
        $mailer = Instance::ensure($this->mailer, Mailer::class);
        return $mailer->getSyncMailer()->sendMultiple($this->messages);
    }
}