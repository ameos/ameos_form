.. _constraints:

Constraints
===========

.. only:: html

	**Table of content:**

	.. contents::
		:local:
		:depth: 1

.. _constraints-required:

Required
--------

Set field mandatory. You can add this contraint like this.

::

	$form->addConstraint('my_field', 'required', 'My field is required');


.. _constraints-email:

Email
-----

If set, the field must be filled with valid email. You can add this contraint like this.

::

	$form->addConstraint('my_field', 'email', 'My field must be an email');


.. _constraints-sameas:

Sameas
------

If set, the field must be filled with the same value as a other field. For password or email confirmation for example. You can add this contraint like this.

::

	$form->addConstraint('my_field_confirmation', 'sameas', 'Confirmation is not valid', array('sameas' => 'my_field'));


.. _constraints-unique:

Unique
------

If set, the field must be filled with an unique value. You can add this contraint like this.

::

	$form->addConstraint('username', 'unique', 'Username must be unique', array('repository' => $this->frontenduserRepository));


.. _constraints-custom:

Custom
------

Custom contraint. You can add this contraint like this.

::

	$form->addConstraint('username', 'custom', 'Custom validation not valid', array('method' => function($value, $form) {
		// your code here. return true if result is valid. Else, false
	}));


.. _constraints-filesize:

Filesize
--------

For upload element. Check the upload file max size. You can add this contraint like this.

::

	$form->addConstraint('file', 'filesize', 'File too big', array('maxsize' => '2M')); 


.. _constraints-fileextention:

Fileextension
-------------

For upload element. Check the upload file max size. You can add this contraint like this.

::

	$form->addConstraint('file', 'fileextention', 'Your file must be a picture', array('allowed' => 'jpg,gif,png')); 



