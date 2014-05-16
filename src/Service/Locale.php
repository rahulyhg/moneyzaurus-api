<?php

namespace Api\Service;

use SebastianBergmann\Money\Currency;
use SebastianBergmann\Money\Money;
use SebastianBergmann\Money\IntlFormatter;
use Api\Service\AccessorTrait;

/**
 * Class Locale
 *
 * @package Api\Service
 *
 * @method Locale setLocale($locale)
 * @method Locale setTimezone($timezone)
 * @method string getLocale()
 * @method string getTimezone()
 */
class Locale
{
    use AccessorTrait;

    /**
     * @return string
     */
    public function getLanguage()
    {
        return \Locale::getPrimaryLanguage($this->getLocale());
    }

    /**
     * @return string
     */
    public function getDisplayLanguage()
    {
        return \Locale::getDisplayLanguage($this->getLocale());
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return \Locale::getRegion($this->getLocale());
    }

    /**
     * @return bool
     */
    public function isValidLocale()
    {
        $locale          = $this->getLocale();
        $language        = $this->getLanguage();
        $region          = $this->getRegion();
        $displayLanguage = $this->getDisplayLanguage();

        return !empty($locale) && !empty($language) && !empty($region) && !empty($displayLanguage);
    }

    /**
     * @return bool
     */
    public function isValidTimezone()
    {
        $data = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

        return in_array($this->getTimezone(), $data);
    }

    /**
     * @return \IntlDateFormatter
     */
    public function getDateTimeFormatter($dateType = \IntlDateFormatter::SHORT)
    {
        $formatter = new \IntlDateFormatter(
            $this->getLocale(),
            $dateType,
            \IntlDateFormatter::MEDIUM
        );
        $formatter->setTimezone($this->getTimezone());

        return $formatter;
    }

    /**
     * @return \IntlDateFormatter
     */
    public function getDateFormatter($dateType = \IntlDateFormatter::SHORT)
    {
        $formatter = new \IntlDateFormatter(
            $this->getLocale(),
            $dateType,
            \IntlDateFormatter::NONE
        );
        $formatter->setTimezone($this->getTimezone());

        return $formatter;
    }

    /**
     * @param string $currency
     * @param int    $amount
     *
     * @return string
     */
    public function getFormattedMoney($currency, $amount)
    {
        $currency  = new Currency($currency);
        $money     = new Money($amount, $currency);
        $formatter = new IntlFormatter($this->getLocale());

        return $formatter->format($money);
    }

}