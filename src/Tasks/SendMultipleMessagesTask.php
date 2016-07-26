<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace YarCode\Yii2\AsyncMailer\Tasks;

use bazilio\async\models\AsyncTask;
use yarcode\yii2\async\mailer\AsyncMailerComponent;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class SendMultipleMessagesTask extends AsyncTask
{
    public static $queueName = 'mailer';

    /** @var MessageInterface[] */
    public $mailMessages;
    /** @var string */
    public $mailerComponent = 'mailer';

    public function setMailMessages(array $mailMessages)
    {
        $this->mailMessages = $mailMessages;
    }

    public function execute()
    {
        $asyncMailer = \Yii::$app->get($this->mailerComponent);
        if (!$asyncMailer instanceof AsyncMailerComponent) {
            throw new InvalidConfigException('Mailer must be an instance of AsyncMailerComponent');
        }

        return $asyncMailer->getSyncMailer()->sendMultiple($this->mailMessages);
    }
}