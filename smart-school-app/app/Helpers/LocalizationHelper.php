<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use NumberFormatter;

/**
 * Localization Helper
 * 
 * Prompt 495: Localize Date, Time, and Numbers
 * 
 * Provides locale-aware formatting for dates, times, numbers, and currencies.
 * Uses Carbon for date localization and NumberFormatter for number formatting.
 */
class LocalizationHelper
{
    /**
     * RTL languages list
     */
    protected static array $rtlLanguages = ['ar', 'he', 'fa', 'ur', 'ps', 'sd', 'yi'];

    /**
     * Get current locale
     */
    public static function getLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Check if current locale is RTL
     */
    public static function isRtl(): bool
    {
        return in_array(self::getLocale(), self::$rtlLanguages);
    }

    /**
     * Get text direction
     */
    public static function getDirection(): string
    {
        return self::isRtl() ? 'rtl' : 'ltr';
    }

    /**
     * Format date according to locale
     */
    public static function formatDate($date, string $format = 'medium'): string
    {
        if (empty($date)) {
            return '';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $carbon->locale(self::getLocale());

        switch ($format) {
            case 'short':
                return $carbon->isoFormat('L');
            case 'medium':
                return $carbon->isoFormat('ll');
            case 'long':
                return $carbon->isoFormat('LL');
            case 'full':
                return $carbon->isoFormat('LLLL');
            default:
                return $carbon->format($format);
        }
    }

    /**
     * Format time according to locale
     */
    public static function formatTime($time, string $format = 'short'): string
    {
        if (empty($time)) {
            return '';
        }

        $carbon = $time instanceof Carbon ? $time : Carbon::parse($time);
        $carbon->locale(self::getLocale());

        switch ($format) {
            case 'short':
                return $carbon->isoFormat('LT');
            case 'medium':
                return $carbon->isoFormat('LTS');
            default:
                return $carbon->format($format);
        }
    }

    /**
     * Format datetime according to locale
     */
    public static function formatDateTime($datetime, string $format = 'medium'): string
    {
        if (empty($datetime)) {
            return '';
        }

        $carbon = $datetime instanceof Carbon ? $datetime : Carbon::parse($datetime);
        $carbon->locale(self::getLocale());

        switch ($format) {
            case 'short':
                return $carbon->isoFormat('L LT');
            case 'medium':
                return $carbon->isoFormat('lll');
            case 'long':
                return $carbon->isoFormat('LLL');
            case 'full':
                return $carbon->isoFormat('LLLL');
            default:
                return $carbon->format($format);
        }
    }

    /**
     * Format relative time (e.g., "2 hours ago")
     */
    public static function formatRelativeTime($datetime): string
    {
        if (empty($datetime)) {
            return '';
        }

        $carbon = $datetime instanceof Carbon ? $datetime : Carbon::parse($datetime);
        $carbon->locale(self::getLocale());

        return $carbon->diffForHumans();
    }

    /**
     * Format number according to locale
     */
    public static function formatNumber($number, int $decimals = 0, ?string $locale = null): string
    {
        if (!is_numeric($number)) {
            return (string) $number;
        }

        $locale = $locale ?? self::getLocale();

        if (class_exists('NumberFormatter')) {
            $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
            return $formatter->format($number);
        }

        return number_format($number, $decimals);
    }

    /**
     * Format currency according to locale
     */
    public static function formatCurrency($amount, string $currency = 'USD', ?string $locale = null): string
    {
        if (!is_numeric($amount)) {
            return (string) $amount;
        }

        $locale = $locale ?? self::getLocale();

        if (class_exists('NumberFormatter')) {
            $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
            return $formatter->formatCurrency($amount, $currency);
        }

        return $currency . ' ' . number_format($amount, 2);
    }

    /**
     * Format percentage according to locale
     */
    public static function formatPercentage($number, int $decimals = 0, ?string $locale = null): string
    {
        if (!is_numeric($number)) {
            return (string) $number;
        }

        $locale = $locale ?? self::getLocale();

        if (class_exists('NumberFormatter')) {
            $formatter = new NumberFormatter($locale, NumberFormatter::PERCENT);
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
            return $formatter->format($number / 100);
        }

        return number_format($number, $decimals) . '%';
    }

    /**
     * Format ordinal number (1st, 2nd, 3rd, etc.)
     */
    public static function formatOrdinal(int $number, ?string $locale = null): string
    {
        $locale = $locale ?? self::getLocale();

        if (class_exists('NumberFormatter')) {
            $formatter = new NumberFormatter($locale, NumberFormatter::ORDINAL);
            return $formatter->format($number);
        }

        // Fallback for English
        $suffix = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if (($number % 100) >= 11 && ($number % 100) <= 13) {
            return $number . 'th';
        }
        return $number . $suffix[$number % 10];
    }

    /**
     * Format file size
     */
    public static function formatFileSize(int $bytes, int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        $size = $bytes / pow(1024, $factor);

        return self::formatNumber($size, $decimals) . ' ' . $units[$factor];
    }

    /**
     * Get localized day names
     */
    public static function getDayNames(string $format = 'long'): array
    {
        $days = [];
        $carbon = Carbon::now()->locale(self::getLocale())->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            switch ($format) {
                case 'short':
                    $days[] = $carbon->isoFormat('dd');
                    break;
                case 'abbreviated':
                    $days[] = $carbon->isoFormat('ddd');
                    break;
                default:
                    $days[] = $carbon->isoFormat('dddd');
            }
            $carbon->addDay();
        }

        return $days;
    }

    /**
     * Get localized month names
     */
    public static function getMonthNames(string $format = 'long'): array
    {
        $months = [];
        $carbon = Carbon::now()->locale(self::getLocale())->startOfYear();

        for ($i = 0; $i < 12; $i++) {
            switch ($format) {
                case 'short':
                    $months[] = $carbon->isoFormat('MMM');
                    break;
                default:
                    $months[] = $carbon->isoFormat('MMMM');
            }
            $carbon->addMonth();
        }

        return $months;
    }

    /**
     * Parse localized date string
     */
    public static function parseDate(string $dateString, ?string $locale = null): ?Carbon
    {
        try {
            $locale = $locale ?? self::getLocale();
            return Carbon::parse($dateString)->locale($locale);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get calendar week start day for locale
     */
    public static function getWeekStartDay(?string $locale = null): int
    {
        $locale = $locale ?? self::getLocale();
        
        // Most Arabic countries start week on Saturday
        if (in_array($locale, ['ar', 'fa'])) {
            return Carbon::SATURDAY;
        }
        
        // US, Canada, Japan start on Sunday
        if (in_array($locale, ['en-US', 'en-CA', 'ja'])) {
            return Carbon::SUNDAY;
        }
        
        // Most other countries start on Monday
        return Carbon::MONDAY;
    }

    /**
     * Format duration in human-readable format
     */
    public static function formatDuration(int $seconds): string
    {
        $carbon = Carbon::now()->locale(self::getLocale());
        
        if ($seconds < 60) {
            return $seconds . ' ' . trans_choice('time.seconds', $seconds);
        }
        
        if ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' ' . trans_choice('time.minutes', $minutes);
        }
        
        if ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            return $hours . ' ' . trans_choice('time.hours', $hours);
        }
        
        $days = floor($seconds / 86400);
        return $days . ' ' . trans_choice('time.days', $days);
    }

    /**
     * Get locale-specific date format pattern
     */
    public static function getDatePattern(?string $locale = null): string
    {
        $locale = $locale ?? self::getLocale();
        
        $patterns = [
            'en' => 'MM/DD/YYYY',
            'en-GB' => 'DD/MM/YYYY',
            'de' => 'DD.MM.YYYY',
            'fr' => 'DD/MM/YYYY',
            'es' => 'DD/MM/YYYY',
            'ar' => 'DD/MM/YYYY',
            'ja' => 'YYYY/MM/DD',
            'zh' => 'YYYY/MM/DD',
        ];
        
        return $patterns[$locale] ?? $patterns['en'];
    }

    /**
     * Get locale-specific time format pattern
     */
    public static function getTimePattern(?string $locale = null): string
    {
        $locale = $locale ?? self::getLocale();
        
        // 12-hour format locales
        $twelveHourLocales = ['en', 'en-US', 'en-AU'];
        
        if (in_array($locale, $twelveHourLocales)) {
            return 'hh:mm A';
        }
        
        return 'HH:mm';
    }

    /**
     * Get locale-specific currency symbol
     */
    public static function getCurrencySymbol(string $currency = 'USD', ?string $locale = null): string
    {
        $locale = $locale ?? self::getLocale();
        
        if (class_exists('NumberFormatter')) {
            $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
            return $formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
        }
        
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            'JPY' => '¥',
            'CNY' => '¥',
            'AED' => 'د.إ',
            'SAR' => '﷼',
        ];
        
        return $symbols[$currency] ?? $currency;
    }

    /**
     * Translate a key with locale fallback
     */
    public static function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? self::getLocale();
        
        $translation = __($key, $replace, $locale);
        
        // If translation not found, try fallback locale
        if ($translation === $key) {
            $fallbackLocale = config('app.fallback_locale', 'en');
            if ($locale !== $fallbackLocale) {
                $translation = __($key, $replace, $fallbackLocale);
            }
        }
        
        return $translation;
    }

    /**
     * Get all supported locales
     */
    public static function getSupportedLocales(): array
    {
        return config('app.supported_locales', ['en']);
    }

    /**
     * Check if locale is supported
     */
    public static function isLocaleSupported(string $locale): bool
    {
        return in_array($locale, self::getSupportedLocales());
    }
}
