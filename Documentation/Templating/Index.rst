.. _templating:

Templating with fluid
======================

.. only:: html

	**Table of content:**

	.. contents::
		:local:
		:depth: 1

.. _templating-form:

Templating a form
-----------------

Example : 

Php code

::	
	
	$mymodel = $this->myModelRepository->findByUid($modelIdentifier);
	$form = \Ameos\AmeosForm\Form\Factory::make('tx_myplugin', $mymodel);
	$form->add('name', 'text')->addConstraint('name', 'required', 'Name is mandatory');
	$form->add('email', 'email')->addConstraint('email', 'email', 'Email is not valid');
	$form->add('submit', 'submit', array('label' => 'Send'));
	$this->view->assign('form', $form);

Template html

::

	{namespace form=Ameos\AmeosForm\ViewHelpers}
	<form:form form="{form}">
		<f:if condition="{errors}">
			<div class="errors">
				<f:for each="{errors}" as="error">
					<span class="error">{error}</span>
				</f:for>			
			</div>
		</f:if>
		<div class="form">
			<p><label for="tx_myplugin_name"><f:translate key="name" /></label>{name}</p>
			<p><label for="tx_myplugin_email"><f:translate key="email" /></label>{email}</p>
			<p><form:element element="{submit}" class="button" />
		</div>
	</form:form>



.. _templating-searchform:

Templating a searchform
-----------------------

Example : 

Php code (search action)

::	
	
	$searchform = \Ameos\AmeosForm\Form\Factory::make('tx_myplugin', $this->myModelRepository);
	$searchform->add('name', 'text');
	$searchform->add('email', 'email');
	$searchform->add('submit', 'submit', array('label' => 'Search'));
	
	$template = ExtensionManagementUtility::extPath('my_ext') . 'Resources/Private/Templates/Controller/List.html';
	$list = $this->objectManager->create(\Ameos\AmeosForm\Library\RecordList::class, $searchform, $template, $this->controllerContext);

	$this->view->assign('my-searchform', $searchform);
	$this->view->assign('my-list', $list);

Template html Search.html

::

	{namespace form=Ameos\AmeosForm\ViewHelpers}
	<form:form form="{my-searchform}">
		<div class="form">
			<p><label for="tx_myplugin_name"><f:translate key="name" /></label>{name}</p>
			<p><label for="tx_myplugin_email"><f:translate key="email" /></label>{email}</p>
			<p><form:element element="{submit}" class="button" />
		</div>
	</form:form>
	<form:list list="{my-list}" />







