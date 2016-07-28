<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace YarCode\Yii2\AsyncMailer;

use bazilio\async\AsyncComponent;
use YarCode\Yii2\AsyncMailer\Tasks\SendMessageTask;
use YarCode\Yii2\AsyncMailer\Tasks\SendMultipleMessagesTask;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\mail\MailerInterface;

class Mailer extends Component implements MailerInterface
{
    public $asyncComponentName = 'async';

    /** @var MailerInterface */
    protected $syncMailer;

    public function init()
    {
        parent::init();

        if (empty($this->syncMailer)) {
            throw new InvalidConfigException('Missing sync mailer configuration');
        }
    }

    public function compose($view = null, array $params = [])
    {
        return $this->getSyncMailer()->compose($view, $params);
    }

    public function send($message)
    {
        $task = new SendMessageTask();
        $task->setMailMessage($message);
        $this->getAsyncComponent()->sendTask($task);
    }

    public function sendMultiple(array $messages)
    {
        $task = new SendMultipleMessagesTask();
        $task->setMailMessages($messages);
        $this->getAsyncComponent()->sendTask($task);
    }

    /**
     * @return AsyncComponent|null|object
     */
    public function getAsyncComponent()
    {
        return \Yii::$app->get($this->asyncComponentName);
    }

    public function setSyncMailer($config)
    {
        if ($config instanceof MailerInterface) {
            $this->syncMailer = $config;
        } else {
            $this->syncMailer = \Yii::createObject($config);
        }
    }

    /**
     * @return MailerInterface
     */
    public function getSyncMailer()
    {
        return $this->syncMailer;
    }
}