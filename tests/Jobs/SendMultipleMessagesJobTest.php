<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use cusodede\QueueMailer\Mailer;
use yii\mail\MessageInterface;
use yii\base\InvalidConfigException;
use yii\queue\sync\Queue;
use yii\swiftmailer\Mailer as SwiftMailer;
use cusodede\QueueMailer\jobs\SendMultipleMessagesJob;
use yii\base\ErrorException;
use yii\helpers\FileHelper;

class SendMultipleMessagesJobTest extends TestCase
{
	public Mailer $mailer;

	public MessageInterface $message;

	public MessageInterface $message2;

	public SendMultipleMessagesJob $a;

	/**
	 * @throws InvalidConfigException
	 */
	public function setUp(): void
	{
		parent::setUp();

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

		$this->message2 = $this->mailer->compose();
		$this->message2->setTo('test@example.org');
		$this->message2->setFrom('no-reply@example.org');
		$this->message2->setHtmlBody('test message 2');

		$this->a = Yii::createObject([
			'class' => SendMultipleMessagesJob::class,
			'mailer' => $this->mailer,
			'messages' => [
				$this->message,
				$this->message2,
			],
		]);
	}

	/**
	 * @throws ErrorException
	 */
	public function tearDown(): void
	{
		FileHelper::removeDirectory(Yii::getAlias('@runtime/mail'));
	}

	/**
	 * @throws InvalidConfigException
	 */
	public function testExecute(): void
	{
		$this->assertEquals(2, $this->a->execute($this->mailer->queue));
	}
}