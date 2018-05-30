<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
class SendMultipleMessagesJobTest extends \PHPUnit\Framework\TestCase
{
    /** @var \YarCode\Yii2\QueueMailer\Mailer */
    public $mailer;
    /** @var \yii\mail\MessageInterface */
    public $message;
    /** @var \yii\mail\MessageInterface */
    public $message2;
    /** @var \YarCode\Yii2\QueueMailer\Jobs\SendMessageJob */
    public $a;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function setUp()
    {
        parent::setUp();

        $this->mailer = \Yii::createObject([
            'class' => \YarCode\Yii2\QueueMailer\Mailer::class,
            'queue' => [
                'class' => \yii\queue\sync\Queue::class,
                'handle' => false, // whether tasks should be executed immediately
            ],
            'syncMailer' => [
                'class' => \yii\swiftmailer\Mailer::class,
                'useFileTransport' => true,
            ],
        ]);

        $this->message = $this->mailer->compose();
        $this->message->setTo('test@example.org');
        $this->message->setFrom('no-reply@example.org');
        $this->message->setHtmlBody('test message');

        $this->message2 = $this->mailer->compose();
        $this->message2->setTo('test@example.org');
        $this->message2->setFrom('no-reply@example.org');
        $this->message2->setHtmlBody('test message 2');

        $this->a = \Yii::createObject([
            'class' => \YarCode\Yii2\QueueMailer\Jobs\SendMultipleMessagesJob::class,
            'mailer' => $this->mailer,
            'messages' => [
                $this->message,
                $this->message2,
            ],
        ]);
    }

    protected function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        try {
            \yii\helpers\FileHelper::removeDirectory(Yii::getAlias('@runtime/mail'));
        } catch (\Throwable $e) {}
    }

    public function testExecute()
    {
        $this->assertEquals(2, $this->a->execute($this->mailer->queue));
    }
}