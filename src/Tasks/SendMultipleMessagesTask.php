<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace YarCode\Yii2\AsyncMailer\Tasks;

use bazilio\async\models\AsyncTask;
use YarCode\Yii2\AsyncMailer\Mailer;
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
        if (!$asyncMailer instanceof Mailer) {
            throw new InvalidConfigException('Mailer must be an instance of ' . Mailer::class);
        }

        return $asyncMailer->getSyncMailer()->sendMultiple($this->mailMessages);
    }
}