<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace YarCode\Yii2\QueueMailer;

use YarCode\Yii2\QueueMailer\Jobs\SendMultipleMessagesJob;
use YarCode\Yii2\QueueMailer\Jobs\SendMessageJob;
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
    protected $queue = 'queue';
    /** @var MailerInterface */
    protected $syncMailer;
    /** @var int|null */
    protected $lastJobId;

    /**
     * @return object|Queue
     * @throws InvalidConfigException
     */
    public function getQueue()
    {
        if (is_callable($this->queue)) {
            $this->queue = call_user_func($this->queue);
        }
        return $this->queue = Instance::ensure($this->queue, Queue::class);
    }

    /**
     * @param mixed $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return object|MailerInterface
     * @throws InvalidConfigException
     */
    public function getSyncMailer()
    {
        if (is_callable($this->syncMailer)) {
            $this->syncMailer = call_user_func($this->syncMailer);
        }
        return $this->syncMailer = Instance::ensure($this->syncMailer, MailerInterface::class);
    }

    /**
     * @param mixed $syncMailer
     */
    public function setSyncMailer($syncMailer)
    {
        $this->syncMailer = $syncMailer;
    }

    /**
     * @inheritdoc
     * @see MailerInterface::compose()
     */
    public function compose($view = null, array $params = [])
    {
        return $this->getSyncMailer()->compose($view, $params);
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
        $this->lastJobId = $this->getQueue()->push($job);
        return $this->lastJobId !== null;
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
        $this->lastJobId = $this->getQueue()->push($job);
        if ($this->lastJobId !== null) {
            return count($messages);
        } else {
            return 0;
        }
    }

    /**
     * @return int|null
     */
    public function getLastJobId()
    {
        return $this->lastJobId;
    }
}