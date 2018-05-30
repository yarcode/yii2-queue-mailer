<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace YarCode\Yii2\QueueMailer\Mailer\Jobs;

use YarCode\Yii2\QueueMailer\Mailer;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\mail\MessageInterface;
use yii\queue\JobInterface;

class SendMessageJob implements JobInterface
{
    /** @var MessageInterface */
    public $message;
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
        if (!$this->message instanceof MessageInterface) {
            throw new \InvalidArgumentException('Message must be an instance of ' . MessageInterface::class);
        }

        /** @var Mailer $mailer */
        $mailer = Instance::ensure($this->mailer, Mailer::class);
        return $mailer->syncMailer->send($this->message);
    }
}