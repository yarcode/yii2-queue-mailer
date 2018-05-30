<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */
class MailerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \YarCode\Yii2\QueueMailer\Mailer */
    public $a;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function setUp()
    {
        parent::setUp();
        $this->a = \Yii::createObject([
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
    }

    public function testInterface()
    {
        $this->assertInstanceOf(\yii\mail\MailerInterface::class, $this->a);
    }
}