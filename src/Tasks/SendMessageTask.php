<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
namespace YarCode\Yii2\AsyncMailer\Tasks;

use bazilio\async\models\AsyncTask;
use YarCode\Yii2\AsyncMailer\AsyncMailerComponent;
use yii\base\InvalidConfigException;
use yii\mail\MessageInterface;

class SendMessageTask extends AsyncTask
{
    public static $queueName = 'mailer';

    /** @var MessageInterface */
    public $mailMessage;
    /** @var string */
    public $mailerComponent = 'mailer';

    public function setMailMessage($mailMessage)
    {
        if (!$mailMessage instanceof MessageInterface) {
            throw new \InvalidArgumentException('Message must be an instance of ' . MessageInterface::class);
        }
        $this->mailMessage = $mailMessage;
    }

    public function execute()
    {
        $asyncMailer = \Yii::$app->get($this->mailerComponent);
        if (!$asyncMailer instanceof AsyncMailerComponent) {
            throw new InvalidConfigException('Mailer must be an instance of ' . AsyncMailerComponent::class);
        }

        return $asyncMailer->getSyncMailer()->send($this->mailMessage);
    }
}