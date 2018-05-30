<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace YarCode\Yii2\QueueMailer\Jobs;

use YarCode\Yii2\QueueMailer\Mailer;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\mail\MessageInterface;
use yii\queue\JobInterface;

class SendMessageJob extends BaseObject implements JobInterface
{
    /** @var MessageInterface */
    public $message;
    /** @var Mailer */
    public $mailer;

    /**
     * @throws InvalidConfigException
     * @throws \InvalidArgumentException
     */
    public function init()
    {
        parent::init();
        if (!$this->message instanceof MessageInterface) {
            throw new \InvalidArgumentException('Message must be an instance of ' . MessageInterface::class);
        }

        /** @var Mailer $mailer */
        $this->mailer = Instance::ensure($this->mailer, Mailer::class);
    }

    /**
     * @inheritdoc
     * @see JobInterface::execute()
     */
    public function execute($queue)
    {

        return $this->mailer->syncMailer->send($this->message);
    }
}