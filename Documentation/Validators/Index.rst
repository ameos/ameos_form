.. _validators:

Validators
==========

.. only:: html

	**Table of content:**

	.. contents::
		:local:
		:depth: 1

.. _validators-required:

Required
--------

Set field mandatory. You can add this validator like this.

::

	$form->validator('my_field', 'required', 'My field is required');


.. _validators-email:

Email
-----

If set, the field must be filled with valid email. You can add this validator like this.

::

	$form->validator('my_field', 'email', 'My field must be an email');


.. _validators-sameas:

Sameas
------

If set, the field must be filled with the same value as a other field. For password or email confirmation for example. You can add this validator like this.

::

	$form->validator('my_field_confirmation', 'sameas', 'Confirmation is not valid', array('sameas' => 'my_field'));


.. _validators-unique:

Unique
------

If set, the field must be filled with an unique value. You can add this validator like this.

::

	$form->validator('username', 'unique', 'Username must be unique', array('repository' => $this->frontenduserRepository));


.. _validators-custom:

Custom
------

Custom validator. You can add this validator like this.

::

	$form->validator('username', 'custom', 'Custom validation not valid', array('method' => function($value, $form) {
		// your code here. return true if result is valid. Else, false
	}));


.. _validators-filesize:

Filesize
--------

For upload element. Check the upload file max size. You can add this validator like this.

::

	$form->validator('file', 'filesize', 'File too big', array('maxsize' => '2M')); 


.. _validators-fileextention:

Fileextension
-------------

For upload element. Check the upload file max size. You can add this validator like this.

::

	$form->validator('file', 'fileextention', 'Your file must be a picture', array('allowed' => 'jpg,gif,png')); 



