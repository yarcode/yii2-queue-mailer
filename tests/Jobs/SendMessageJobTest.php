<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
class SendMessageJobTest extends \PHPUnit\Framework\TestCase
{
    /** @var \YarCode\Yii2\QueueMailer\Mailer */
    public $mailer;
    /** @var \yii\mail\MessageInterface */
    public $message;
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

        $this->a = \Yii::createObject([
            'class' => \YarCode\Yii2\QueueMailer\Jobs\SendMessageJob::class,
            'mailer' => $this->mailer,
            'message' => $this->message,
        ]);
    }

    protected function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        \yii\helpers\FileHelper::removeDirectory(Yii::getAlias('@runtime/mail'));
    }

    public function testExecute()
    {
        $this->assertTrue($this->a->execute($this->mailer->queue));
    }
}