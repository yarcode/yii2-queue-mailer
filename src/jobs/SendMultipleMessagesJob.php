<?php
declare(strict_types = 1);

namespace cusodede\QueueMailer\jobs;

use cusodede\QueueMailer\Mailer;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\mail\MessageInterface;
use yii\queue\JobInterface;
use InvalidArgumentException;

class SendMultipleMessagesJob implements JobInterface
{
	public array|MessageInterface $messages = [];

	public string|Mailer $mailer = Mailer::class;

	/**
	 * @throws InvalidConfigException
	 * @throws InvalidArgumentException
	 * @see JobInterface::execute()
	 *
	 */
	public function execute($queue): int
	{
		if (false === is_array($this->messages)) {
			throw new InvalidArgumentException('Message must be an instance of ' . MessageInterface::class);
		}
		/** @var Mailer $mailer */
		$mailer = Instance::ensure($this->mailer, Mailer::class);
		return $mailer->getSyncMailer()->sendMultiple($this->messages);
	}
}