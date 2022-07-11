<?php
declare(strict_types = 1);

namespace cusodede\QueueMailer\jobs;

use cusodede\QueueMailer\Mailer;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\mail\MessageInterface;
use yii\queue\JobInterface;
use InvalidArgumentException;

class SendMessageJob extends BaseObject implements JobInterface
{
	public array|MessageInterface $message = [];

	public string|Mailer $mailer = Mailer::class;

	/**
	 * @throws InvalidConfigException
	 * @throws InvalidArgumentException
	 * @see JobInterface::execute()
	 *
	 */
	public function execute($queue): bool
	{
		if (false === $this->message instanceof MessageInterface) {
			throw new InvalidArgumentException('Message must be an instance of ' . MessageInterface::class);
		}
		/** @var Mailer $mailer */
		$mailer = Instance::ensure($this->mailer, Mailer::class);
		return $mailer->getSyncMailer()->send($this->message);
	}
}