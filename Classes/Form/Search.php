<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Form;

use Ameos\AmeosForm\Utility\UserUtility;
use TYPO3\CMS\Core\Http\ApplicationType;

abstract class Search extends AbstractForm
{
    /**
     * @var bool $storeSearchInSession
     */
    protected $storeSearchInSession = true;

    /**
     * @constuctor
     *
     * @param   string $identifier form identifier
     */
    public function __construct($identifier)
    {
        parent::__construct($identifier);
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            if (UserUtility::isLogged()) {
                $GLOBALS['TSFE']->fe_user->setKey('user', 'form-' . $this->getIdentifier() . '-clauses', $this->clauses);
            } else {
                $GLOBALS['TSFE']->fe_user->setKey('ses', 'form-' . $this->getIdentifier() . '-clauses', $this->clauses);
            }
            $GLOBALS['TSFE']->fe_user->storeSessionData();
        } elseif (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            session_start();
            $_SESSION['form-' . $this->getIdentifier() . '-clauses'] = $this->clauses;
        }

        if (!is_array($this->clauses)) {
            $this->clauses = [];
        }
    }

    /**
     * set if the search criterias are stored in session
     * @param   bool    $storeSearchInSession
     * @return  \Ameos\AmeosForm\Form this
     */
    public function storeSearchInSession($storeSearchInSession = true)
    {
        $this->storeSearchInSession = $storeSearchInSession;
        return $this;
    }

    /**
     * add element fo the form
     *
     * @param   string  $type element type
     * @param   string  $name element name
     * @param   array   $configuration element configuration
     * @return  \Ameos\AmeosForm\Form this
     */
    public function add($name, $type = '', $configuration = [], $overrideFunction = false)
    {
        parent::add($name, $type, $configuration);
        if ($overrideFunction !== false) {
            $this->elements[$name]->setOverrideClause($overrideFunction);
        }
        return $this;
    }

    /**
     * set value from session
     */
    public function setValueFromSession()
    {
        foreach ($this->clauses as $clause) {
            if (($element = $this->getElement($clause['elementname'])) !== false) {
                $element->setValue($clause['elementvalue']);
            }
        }
    }
}
