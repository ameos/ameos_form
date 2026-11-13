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
	
	$form = $this->formService->create('tx_myplugin', $mymodel, $this->request);
	$form->add('name', 'text')->add('email', 'email')->add('submit', 'submit', ['label' => 'Send']);
	if ($form->isSubmitted()) {
		$form->addConstraint('name', 'required', 'Name is mandatory')->addConstraint('email', 'email', 'Email is not valid');
		...
	}
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

	$searchform = $this->formService->create('tx_myplugin_search', $this->myModelRepository, $this->request);
	$searchform->add('name', 'text');
	$searchform->add('email', 'email');
	$searchform->add('submit', 'submit', ['label' => 'Search']);

	$this->view->assign('searchform', $searchform);
	$this->view->assign('results', $searchform->getResults('name'));

Template html

::

	{namespace form=Ameos\AmeosForm\ViewHelpers}
	<form:form form="{searchform}">
		<div class="form">
			<p><label for="tx_myplugin_search_name"><f:translate key="name" /></label>{name}</p>
			<p><label for="tx_myplugin_search_email"><f:translate key="email" /></label>{email}</p>
			<p><form:element element="{submit}" class="button" /></p>
		</div>
	</form:form>
	<f:for each="{results}" as="record">
		<p>{record.name}</p>
	</f:for>


.. _templating-compiled:

Rendering all the elements
--------------------------

Inside the form tag, ``<form:compiledForm form="{form}" />`` renders all the elements in the order they were added.

The element variables (``{name}``) render the HTML of the element. ``<form:element>`` accepts ``class``,
``errorClass``, ``placeholder``, ``style`` and ``custom`` arguments overriding the element configuration.
Each element also provides ``{name.renderingInformation}`` (value, errors, required, htmlid...) to build
your own markup.
