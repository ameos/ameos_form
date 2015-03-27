Create a form
=============

.. only:: html

	**Table of content:**

	.. contents::
		:local:
		:depth: 1


.. _forms-creation:

Creation and edition form
-------------------------

You can create an form for edit or create record.

::

	$form = \Ameos\AmeosForm\Form\Factory::make('tx_myextension_myplugin', $mymodel);

Where $mymodel is an instance of an extbase model.

For creation :

::

	$mymodel = GeneralUtility::makeInstance(\Vendor\Extension\Domain\Model\MyModel::class);

For edition :

::

	$mymodel = $this->myModelRepository->findByUid($modelIdentifier);


.. _forms-search:

Search form
-----------

You can create a search form.

::

	$searchform = \Ameos\AmeosForm\Form\Factory::make('tx_myextension_myplugin', $myrepository);

You can add where clause with "addWhereClause" method.

Example :

::

	$searchform->addWhereClause([
		'type'    => 'logicalOr',
		'clauses' => [
			['field' => 'myfield', 'type'  => 'like', 'value' => 'myvalue1'],
			['field' => 'myfield', 'type'  => 'like', 'value' => 'myvalue2'],
			['field' => 'myfield', 'type'  => 'like', 'value' => 'myvalue3'],
		]
	]);
	$searchform->addWhereClause([
		'field' => 'myfield',
		'type'  => 'equals',
		'value' => 'myvalue'
	]);

You can associate a list with a search form.

::

	$template = ExtensionManagementUtility::extPath('ameos_intervention') . 'Resources/Private/Templates/Controller/ActionList.html';
	$list = $this->objectManager->create(\Ameos\AmeosForm\Library\RecordList::class, $searchform, $template, $this->controllerContext, 'field_to_order', 'order_direction (ASC | DESC)');

	$this->view->assign('searchform', $searchform);
	$this->view->assign('list', $list);


For templaing list, see :ref:`Templating with fluid <templating>` part.


.. _forms-other:

Other form
----------

You can create a form without model or repository link.

For example, a contact form without database insertion.

::

	$form = \Ameos\AmeosForm\Form\Factory::make('tx_myextension_myplugin');

.. _forms-bindrequest:

Bind a request
--------------

After submit, you can bind the request to the form. And after that, you can do everything you want : store in database, send a mail...

::
	
	$mymodel = $this->myModelRepository->findByUid($modelIdentifier);

	$form = \Ameos\AmeosForm\Form\Factory::make('tx_myplugin', $mymodel);
	$form->add('name', 'text')->validator('name', 'required', 'Name is mandatory');
	$form->add('email', 'email')->validator('email', 'email', 'Email is not valid');
	$form->add('submit', 'submit', array('label' => 'Send'));
	
	if($form->isSubmitted()) {
		$form->bindRequest($this->request);
		if($form->isValid()) {
			
			$this->myModelRepository->add($mymodel);
			
			$this->addFlashMessage('New record created');
			$this->redirect('index')
		}
	}

	$this->view->assign('form', $form);


.. _forms-addelement:

Add an element to the form
--------------------------

After the creation of your form, you add element easily.

::

	$form->add($name, $type', $configuration);

For example : $form->add('my-radiobutton', 'radio', array('items' => array('value1' => 'label 1', 'value2' => 'label 2')));


All elements are detailled on :ref:`Elements <elements>` part.


.. _forms-addvalidator:

Add an validator to the form
----------------------------

After the creation of your form, you add validator easily.

::

	$form->validator($field, $type, $message, $configuration); //  $configuration is optional. Depends the type of validator

For example : $form->validator('my_field', 'required', LocalizationUtility::translate('my_field.required', 'ExtensionKey'));

All validators are detailled on :ref:`Validators <validators>` part.
