<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;
use cusodede\QueueMailer\Mailer;
use yii\mail\MessageInterface;
use cusodede\QueueMailer\jobs\SendMessageJob;
use yii\base\InvalidConfigException;
use yii\queue\sync\Queue;
use yii\swiftmailer\Mailer as SwiftMailer;
use yii\base\ErrorException;
use yii\helpers\FileHelper;

class SendMessageJobTest extends TestCase
{
	public Mailer $mailer;

	public MessageInterface $message;

	public SendMessageJob $a;

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
				'handle' => false, // whether tasks should be executed immediately
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

		$this->a = Yii::createObject([
			'class' => SendMessageJob::class,
			'mailer' => $this->mailer,
			'message' => $this->message,
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
	public function testExecute()
	{
		$this->assertTrue($this->a->execute($this->mailer->queue));
	}
}