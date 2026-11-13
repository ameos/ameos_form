Create a form
=============

.. only:: html

	**Table of content:**

	.. contents::
		:local:
		:depth: 1


.. _forms-creation:

Get a form
----------

Forms are created by the ``\Ameos\AmeosForm\Service\FormService`` (inject it in your controller):

::

	use Ameos\AmeosForm\Service\FormService;

	public function __construct(private readonly FormService $formService)
	{
	}

	public function editAction(?MyModel $mymodel = null): ResponseInterface
	{
		$form = $this->formService->create('tx_myextension_myplugin', $mymodel ?? new MyModel(), $this->request);
		...
	}

``create(string $identifier, $subject = null, ?ServerRequestInterface $request = null)``

* ``$identifier``: name of the form, fields are named ``identifier[field]``. Use the plugin namespace
  (``tx_myextension_myplugin``) to get the error flash messages in the queue of the plugin.
* ``$subject``: an Extbase entity (edition form), a searchable repository (search form) or nothing.
* ``$request``: the current request (``$this->request`` in an Extbase controller). Defaults to ``$GLOBALS['TYPO3_REQUEST']``.

.. important::

	The form reads the submitted values, stores the CSRF token, the captcha digits and the search in the
	frontend user session: the actions displaying a form must be **non-cacheable**.


.. _forms-entity:

Creation and edition form
-------------------------

When an entity is given, each element is initialized with the value of its getter (``getMyField()`` for
the element ``my_field``), and each submitted value is given to its setter (``setMyField()``).

Submitted values are strings: they are cast to the type of the setter parameter (``int``, ``float``,
``bool``, ``string``; arrays are converted to comma separated lists for ``string``), and an empty string
becomes ``null`` when the parameter is nullable. Dates are given as ``\DateTime`` (or ``\DateTimeImmutable``
when the setter expects it).

Unchecked checkboxes and multiple select boxes without selection are not sent by the browser: when the
form is submitted, these elements are emptied (except when they are disabled).

::

	public function editAction(?MyModel $mymodel = null): ResponseInterface
	{
		$mymodel ??= new MyModel();
		$form = $this->formService->create('tx_myplugin_pi1', $mymodel, $this->request);
		$form->add('name', 'text')->add('email', 'email')->add('submit', 'submit', ['label' => 'Send']);

		if ($form->isSubmitted()) {
			$form->addConstraint('name', 'required', 'Name is mandatory')
				->addConstraint('email', 'email', 'Email is not valid');

			if ($form->isValid()) {
				$mymodel->getUid() === null ? $this->myModelRepository->add($mymodel) : $this->myModelRepository->update($mymodel);
				$this->addFlashMessage('Record saved');
				return $this->redirect('list');
			}
		}

		$this->view->assign('form', $form);
		return $this->htmlResponse();
	}

The entity is modified as soon as the elements are added: persist it only when the form is valid.


.. _forms-search:

Search form
-----------

Give a repository implementing ``\Ameos\AmeosForm\Domain\Repository\SearchableRepositoryInterface``.
The easiest way is to extend ``\Ameos\AmeosForm\Domain\Repository\SearchableRepository`` (or to use the trait
``\Ameos\AmeosForm\Domain\Repository\Trait\SearchableRepository`` in an Extbase repository).

::

	$searchform = $this->formService->create('tx_myextension_search', $this->myRepository, $this->request);
	$searchform->add('title', 'text')->add('submit', 'submit', ['label' => 'Search']);

	$this->view->assign('searchform', $searchform);
	$this->view->assign('results', $searchform->getResults('title', 'ASC'));

Each element adds a clause (``like`` for text fields, ``equals`` for dropdown, radio and range, ``in`` for
multiple dropdown, ``contains`` for checkbox). The clause of an element can be replaced:

::

	$searchform->get('year_min')->setOverrideClause(
		fn(mixed $value): array => ['field' => 'year', 'type' => 'greaterThanOrEqual', 'value' => (int)$value]
	);

You can add where clauses with "addWhereClause" method.

::

	$searchform->addWhereClause([
		'type'    => 'logicalOr',
		'clauses' => [
			['field' => 'myfield', 'type' => 'like', 'value' => 'myvalue1'],
			['field' => 'myfield', 'type' => 'like', 'value' => 'myvalue2'],
		]
	]);
	$searchform->addWhereClause(['field' => 'myfield', 'type' => 'equals', 'value' => 'myvalue']);

Available clause types: ``equals``, ``like``, ``contains`` (relations), ``inList`` (comma separated values
stored in a string field), ``in``, ``lessThan``, ``lessThanOrEqual``, ``greaterThan``, ``greaterThanOrEqual``,
``logicalAnd`` / ``logicalOr`` (with ``clauses``) and ``logicalNot`` (with ``clause``).

The search is stored in the frontend user session and restored when the form is displayed again without
being submitted. Disable it with ``$searchform->disableSearchStoringInSession()``.


.. _forms-other:

Other form
----------

You can create a form without model or repository link.

For example, a contact form without database insertion.

::

	$form = $this->formService->create('tx_myextension_contact', null, $this->request);

Read the values with ``$form->get('name')->getValue()`` once the form is valid.


.. _forms-addelement:

Add an element to the form
--------------------------

After the creation of your form, you add element easily.

::

	$form->add($name, $type, $configuration);

For example : ``$form->add('my-radiobutton', 'radio', ['items' => ['value1' => 'label 1', 'value2' => 'label 2']]);``

The types are available as constants of ``\Ameos\AmeosForm\Enum\Element`` (``Element::RADIO``...).

All elements are detailled on :ref:`Elements <elements>` part.


.. _forms-addconstraint:

Add a constraint to the form
----------------------------

After the creation of your form, you add constraint easily.

::

	$form->addConstraint($field, $type, $message, $configuration); //  $configuration is optional. Depends the type of validator

For example : ``$form->addConstraint('my_field', 'required', LocalizationUtility::translate('my_field.required', 'ExtensionKey'));``

The types are available as constants of ``\Ameos\AmeosForm\Enum\Constraint``.

All constraints are detailled on :ref:`Constraints <constraints>` part.

Errors are added as flash messages in the queue ``extbase.flashmessages.<form identifier>``. Disable them
with ``$form->getErrorManager()->disableFlashMessage()``.

When the form is valid, the ``\Ameos\AmeosForm\Event\ValidFormEvent`` PSR-14 event is dispatched (once): it
hashes the passwords and moves the uploaded files. Submitted values can be changed by listening to
``\Ameos\AmeosForm\Event\BindValueFromRequestEvent``.


.. _forms-disableformprotection:

Form protection
---------------

The form ViewHelper adds a CSRF token (stored in the frontend user session) and a honeypot field (hidden
field which must stay empty). Both are checked when the form is submitted: when they are not valid, an error
is added with the key ``\Ameos\AmeosForm\Form\Form::FORM_ERROR_KEY`` and the form is not valid.

If you want, you can disable form protection.

Csrf protection :

::

    $form->disableCsrftoken();

Honeypot protection :

::

    $form->disableHoneypot();
