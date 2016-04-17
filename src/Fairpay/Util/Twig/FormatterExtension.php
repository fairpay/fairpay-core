<?php


namespace Fairpay\Util\Twig;


use Symfony\Component\Translation\DataCollectorTranslator;

class FormatterExtension extends \Twig_Extension
{
    /** @var DataCollectorTranslator */
    private $translator;

    /**
     * FormatterExtension constructor.
     * @param DataCollectorTranslator $translator
     */
    public function __construct(DataCollectorTranslator $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('format_gender', array($this, 'gender'), array('is_safe' => ['html'])),
            new \Twig_SimpleFilter('format_date', array($this, 'date'), array('is_safe' => ['html'])),
            new \Twig_SimpleFilter('format_phone', array($this, 'phone'), array('is_safe' => ['html'])),
            new \Twig_SimpleFilter('format_price', array($this, 'price'), array('is_safe' => ['html'])),
        );
    }

    public function gender($gender = null)
    {
        if (!in_array($gender, ['male', 'female'])) {
            $gender = 'unknown';
        }

        return $this->translator->trans("student.values.gender.$gender", [], 'entities');
    }

    public function date(\DateTime $date = null)
    {
        if (null === $date) {
            return 'Inconnue';
        }

        return $date->format('d/m/Y');
    }

    public function phone($phone = null)
    {
        if (!$phone) {
            return $this->translator->trans("student.values.phone.unknown", [], 'entities');
        }

        $phone = preg_replace('/[^\d+]/', '', $phone);

        if (preg_match('/^(0\d)(\d{2})(\d{2})(\d{2})(\d{2})$/', $phone, $matches)) {
            return sprintf('%s %s %s %s %s', $matches[1], $matches[2], $matches[3], $matches[4], $matches[5]);
        }

        if (preg_match('/^(\+\d{2})(\d)(\d{2})(\d{2})(\d{2})(\d{2})$/', $phone, $matches)) {
            return sprintf('%s %s %s %s %s %s', $matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6]);
        }

        if (preg_match('/^(\+\d{3})(\d{3})(\d{2})(\d{2})(\d{2})$/', $phone, $matches)) {
            return sprintf('%s %s %s %s %s', $matches[1], $matches[2], $matches[3], $matches[4], $matches[5]);
        }

        return $phone;
    }

    public function price($price = null)
    {
        $price = floatval($price);

        return number_format($price, 2, '.', ' ') . '€';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'formatter_extension';
    }
}