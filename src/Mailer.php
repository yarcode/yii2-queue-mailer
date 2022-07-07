<?php
declare(strict_types = 1);

namespace cusodede\QueueMailer;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\mail\MailerInterface;
use yii\queue\Queue;
use yii\swiftmailer\Mailer as SwiftMailer;
use cusodede\QueueMailer\jobs\SendMultipleMessagesJob;
use cusodede\QueueMailer\jobs\SendMessageJob;
use yii\mail\MessageInterface;

/**
 * Декоратор Mailer с функционалом очереди.
 *
 * @property string|array|Queue $queue
 */
class Mailer extends Component implements MailerInterface
{
	/**
	 * @var string|self Компонент Mailer.
	 */
	public string|self $id = 'mailer';

	/**
	 * @var string|array|Queue Компонент очереди.
	 */
	protected string|array|Queue $queue = Queue::class;

	/**
	 * @var string|array|SwiftMailer Декорируемый Mailer.
	 */
	protected string|array|MailerInterface $syncMailer = SwiftMailer::class;

	/**
	 * Идентификатор задания.
	 */
	protected string $lastJobId = '';

	/**
	 * @throws InvalidConfigException
	 */
	public function getQueue(): array|object|string
	{
		if (is_callable($this->queue)) {
			$this->queue = call_user_func($this->queue);
		}
		return $this->queue = Instance::ensure($this->queue, Queue::class);
	}

	public function setQueue(string|array|Queue $queue): void
	{
		$this->queue = $queue;
	}

	/**
	 * @throws InvalidConfigException
	 */
	public function getSyncMailer(): array|object|string
	{
		if (is_callable($this->syncMailer)) {
			$this->syncMailer = call_user_func($this->syncMailer);
		}
		return $this->syncMailer = Instance::ensure($this->syncMailer, MailerInterface::class);
	}

	public function setSyncMailer(array $syncMailer): void
	{
		$this->syncMailer = $syncMailer;
	}

	/**
	 * @see MailerInterface::compose()
	 *
	 * @throws InvalidConfigException
	 */
	public function compose(mixed $view = null, array $params = []): MessageInterface
	{
		return $this->getSyncMailer()->compose($view, $params);
	}

	/**
	 * @see MailerInterface::send()
	 *
	 * @throws InvalidConfigException
	 */
	public function send(mixed $message): bool
	{
		$job = Yii::createObject(SendMessageJob::class);
		$job->mailer = $this->id;
		$job->message = $message;
		return null !== $this->getQueue()->push($job);
	}

	/**
	 * @see MailerInterface::sendMultiple()
	 *
	 * @throws InvalidConfigException
	 */
	public function sendMultiple(mixed $messages): int
	{
		$job = Yii::createObject(SendMultipleMessagesJob::class);
		$job->mailer = $this->id;
		$job->messages = $messages;
		return null !== $this->getQueue()->push($job) ? count($messages) : 0;
	}

	public function getLastJobId(): null|string
	{
		return $this->lastJobId;
	}
}