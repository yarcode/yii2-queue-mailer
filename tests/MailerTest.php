<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
class MailerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \YarCode\Yii2\QueueMailer\Mailer */
    public $mailer;
    /** @var \yii\mail\MessageInterface */
    public $message;
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
    }

    public function testInterface()
    {
        $this->assertInstanceOf(\yii\mail\MailerInterface::class, $this->mailer);
    }

    public function testSend()
    {
        $this->assertTrue($this->mailer->send($this->message));
    }

    public function testSendMultiple()
    {
        $this->assertEquals(2, $this->mailer->send([$this->message, $this->message]));
    }

    public function testGetLastJobId()
    {
        $this->mailer->send($this->message);
        $this->assertEquals(1, $this->mailer->getLastJobId());
    }
}