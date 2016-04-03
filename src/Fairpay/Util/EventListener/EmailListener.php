<?php


namespace Fairpay\Util\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class EmailListener implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * EmailListener constructor.
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     */
    public function init(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * Render a twig template.
     *
     * @param string $template
     * @param array  $context
     * @return string
     */
    protected function render($template, array $context = [])
    {
        return $this->twig->render($template, $context);
    }

    /**
     * Send an email.
     *
     * @param string $subject
     * @param string $to
     * @param string $body
     * @param string $from
     */
    protected function send($subject, $to, $body, $from = 'noreply@fairpay.local')
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body)
        ;

        $this->mailer->send($message);
    }
}