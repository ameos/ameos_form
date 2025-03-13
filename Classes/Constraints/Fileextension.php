<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Fileextension extends ConstraintAbstract
{
    use Traits\DisableGlobalErrorMessage;

    /**
     * return true if the element is valide
     *
     * @param   array|string $value value to test
     * @return  bool true if the element is valide
     */
    public function isValid($value)
    {
        if (!is_array($value) && empty($value)) {
            return true;
        }

        $valid = true;
        /** @var array */
        $values = $value;

        foreach ($values as $fileName) {
            $pathInfo = pathinfo($fileName);
            $extensionValid = GeneralUtility::inList(
                $this->configuration['allowed'],
                strtolower($pathInfo['extension'])
            );

            if (!$extensionValid) {
                $this->element->removeFileFromValue($fileName);
                $this->form->getErrorManager()->add(
                    str_replace(
                        [
                            '%file_name%',
                            '%extensions%'
                        ],
                        [
                            $fileName,
                            implode(', ', explode(',', $this->configuration['allowed']))
                        ],
                        $this->message
                    ),
                    $this->element->getName()
                );
                $valid = false;
            }
        }

        return $valid;
    }
}
