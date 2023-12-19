<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class StringUtility
{
    /**
     * @var \Ameos\AmeosForm\Form $form form
     */
    protected $form;

    /**
     * @param   \Ameos\AmeosForm\Form $form form
     */
    public function __construct($form)
    {
        $this->form = $form;
    }

    /**
     * camel case
     * @param string $string
     * @return string
     */
    public static function camelCase($string)
    {
        $output = '';
        foreach (explode('_', $string) as $part) {
            $output .= ucfirst($part);
        }
        return $output;
    }

    /**
     * Replace EXT, EXTCONF ...
     *
     * @param string $string the raw string
     * @return string eval string
     */
    public function smart($string)
    {
        // if is callable function
        if (is_callable($string)) {
            $string = call_user_func($string);
        }

        // return locallang value
        if (substr($string, 0, 4) === 'LLL:') {
            $string = str_replace('LLL:', '', $string);
            $string = LocalizationUtility::translate($string, $this->form->getExtensionName());
        }

        // Search in typoscript configuration
        if (substr($string, 0, 3) === 'TS:') {
            $path = str_replace('TS:', '', $string);
            $parts = GeneralUtility::trimExplode('.', $path);
            $string = $GLOBALS['TSFE']->tmpl->setup;
            $lastPart = array_pop($parts);
            foreach ($parts as $part) {
                if (!isset($string[$part . '.'])) {
                    $string = '';
                    break;
                }
                $string = $string[$part . '.'];
            }

            $string = isset($string[$lastPart]) ? $string[$lastPart] : '';
        }

        // search in $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']
        if (substr($string, 0, 8) === 'EXTCONF:') {
            $path = str_replace('EXTCONF:', '', $string);
            $parts = GeneralUtility::trimExplode('/', $path);
            $string = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'];
            foreach ($parts as $part) {
                if (!isset($string[$part])) {
                    $string = '';
                    break;
                }
                $string = $string[$part];
            }
        }

        // replace EXT:my_ext by ext path
        if (substr($string, 0, 4) === 'EXT:') {
            $string = str_replace('EXT:', '', $string);
            list($ext, $file) = explode('/', $string, 2);

            $string = ExtensionManagementUtility::siteRelPath($ext) . $file;
        }

        return $string;
    }
}
