<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use cusodede\QueueMailer\Mailer;
use yii\mail\MessageInterface;
use yii\base\InvalidConfigException;
use yii\queue\sync\Queue;
use yii\swiftmailer\Mailer as SwiftMailer;
use yii\mail\MailerInterface;

/**
 * Class MailerTest
 */
class MailerTest extends TestCase
{
	public Mailer $mailer;

	public MessageInterface $message;

	/**
	 * @throws InvalidConfigException
	 */
	public function setUp(): void
	{
		parent::setUp();

		/** @noinspection PhpFieldAssignmentTypeMismatchInspection Это тесты*/
		$this->mailer = Yii::createObject([
			'class' => Mailer::class,
			'queue' => [
				'class' => Queue::class,
				'handle' => false,
			],
			'syncMailer' => [
				'class' => SwiftMailer::class,
				'useFileTransport' => true,
			],
		]);

		$this->message = $this->mailer->compose();
		$this->message->setTo('test@example.org');
		$this->message->setFrom('no-reply@example.org');
		$this->message->setHtmlBody('test message');
	}

	/**
	 * @return void
	 */
	public function testInterface(): void
	{
		$this->assertInstanceOf(MailerInterface::class, $this->mailer);
	}

	/**
	 * @return void
	 * @throws InvalidConfigException
	 */
	public function testSend(): void
	{
		$this->assertTrue($this->mailer->send($this->message));
	}

	/**
	 * @return void
	 * @throws InvalidConfigException
	 */
	public function testSendMultiple(): void
	{
		$this->assertEquals(2, $this->mailer->send([$this->message, $this->message]));
	}

	/**
	 * @return void
	 * @throws InvalidConfigException
	 */
	public function testGetLastJobId(): void
	{
		$this->mailer->send($this->message);
		$this->assertEquals('', $this->mailer->getLastJobId());
	}
}