<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace YarCode\Yii2\QueueMailer;

use YarCode\Yii2\QueueMailer\Jobs\SendMultipleMessagesJob;
use YarCode\Yii2\QueueMailer\Mailer\Jobs\SendMessageJob;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\mail\MailerInterface;
use yii\queue\Queue;

class Mailer extends Component implements MailerInterface
{
    /** @var string */
    public $id = 'mailer';
    /** @var Queue */
    public $queue = 'queue';
    /** @var MailerInterface */
    public $syncMailer;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        Instance::ensure($this->queue, Queue::class);
        Instance::ensure($this->syncMailer, MailerInterface::class);
    }

    /**
     * @inheritdoc
     * @see MailerInterface::compose()
     */
    public function compose($view = null, array $params = [])
    {
        return $this->syncMailer->compose($view, $params);
    }

    /**
     * @inheritdoc
     * @see MailerInterface::send()
     *
     * @throws InvalidConfigException
     */
    public function send($message)
    {
        $job = \Yii::createObject(SendMessageJob::class);
        $job->mailer = $this->id;
        $job->message = $message;
        return $this->queue->push($job);
    }

    /**
     * @inheritdoc
     * @see MailerInterface::sendMultiple()
     *
     * @throws InvalidConfigException
     */
    public function sendMultiple(array $messages)
    {
        $job = \Yii::createObject(SendMultipleMessagesJob::class);
        $job->mailer = $this->id;
        $job->messages = $messages;
        return $this->queue->push($job);
    }
}