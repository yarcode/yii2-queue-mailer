# Queue mailer decorator for Yii2 framework
Send your emails in the background using Yii2 queues.

[![Build Status](https://travis-ci.org/yarcode/yii2-queue-mailer.svg?branch=master)](https://travis-ci.org/yarcode/yii2-mailgun-mailer)
[![GitHub license](https://img.shields.io/github/license/yarcode/yii2-queue-mailer.svg)](https://github.com/yarcode/yii2-mailgun-mailer/blob/master/LICENSE.md)


## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yarcode/yii2-queue-mailer
```

or add

```json
"yarcode/yii2-queue-mailer": "*"
```

## Usage

Configure `queue` component of your application.
You can find the details here: https://www.yiiframework.com/extension/yiisoft/yii2-queue

Configure `YarCode\Yii2\QueueMailer\Mailer` as your primary mailer.
```
  'mailer' => [
      'class' => \YarCode\Yii2\QueueMailer\Mailer::class,
      'syncMailer' => [
          'class' => \yii\swiftmailer\Mailer::class,
          'useFileTransport' => true,
      ],
  ],
```
Now you can send your emails as usual.
```
$message = \Yii::$app->mailer->compose()
  ->setSubject('test subject')
  ->setFrom('test@example.org')
  ->setHtmlBody('test body')
  ->setTo('user@example.org');

\Yii::$app->mailer->send($message);
```
