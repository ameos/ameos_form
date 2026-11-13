<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Service;

use Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface;
use Ameos\AmeosForm\Form\Form;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

final readonly class FormService
{
    public function __construct(private EventDispatcherInterface $eventDispatcher) {}

    /**
     * Create a form. The optional subject is attached as search repository or as entity to update.
     * Pass the current request (e.g. $this->request in an Extbase controller), it defaults to $GLOBALS['TYPO3_REQUEST'].
     */
    public function create(
        string $identifier,
        SearchableRepositoryInterface|AbstractEntity|null $subject = null,
        ?ServerRequestInterface $request = null,
    ): Form {
        /** @var Form $form XCLASS-able */
        $form = GeneralUtility::makeInstance(Form::class, $identifier, $request, $this->eventDispatcher);
        if ($subject instanceof SearchableRepositoryInterface) {
            $form->attachRepository($subject);
        } elseif ($subject instanceof AbstractEntity) {
            $form->attachEntity($subject);
        }
        return $form;
    }
}
