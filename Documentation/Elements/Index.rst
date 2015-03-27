.. _elements:

Elements
=========


.. only:: html

	**Table of content:**

	.. contents::
		:local:
		:depth: 1

.. _elements-button:

Button
------

Classic html button.

.. figure:: ../Images/button.png
   
You can add a button like this

::

	$form->add('my-button', 'button', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        custom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
label         label of the button                                          click me!
============  ===================================  ======================  ===================================================


Captcha
-------

.. figure:: ../Images/captcha.png

You can add a captcha like this

::

	$form->add('my-captcha', 'captcha', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                Enter the code
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
errormessage  Message if the captcha is not valid  Captcha is not valid    Captcha is not valid
============  ===================================  ======================  ===================================================


.. _elements-checkbox:

Checkbox
--------

.. figure:: ../Images/checkbox.png
   
You can add a checkbox like this

::

	$form->add('my-checkbox', 'checkbox', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        custom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
items         avalaible choice for checkbox                                array('1' => 'Item 1', '2' => 'Item 2');
============  ===================================  ======================  ===================================================



.. _elements-color:

Color
-----

.. figure:: ../Images/color.png

You can add a input color like this. Work only with html5 browser.

::

	$form->add('my-color-field', 'color', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                my firstname
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
============  ===================================  ======================  ===================================================


.. _elements-date:

Date
----

.. figure:: ../Images/date.png

You can add a date like this.

::

	$form->add('birthdate', 'date', $configuration);

Available configuration

===================  =================================================  ======================  ===================================================
Property             Description                                        Default value           Example
===================  =================================================  ======================  ===================================================
class                css class for the html tag                                                 big
format-display       sort field.d for day, m for month and y for year   dmy                     dmy, mdy, ymd 
year-minimum-limit   minimum availaible year                            1900                    1970
year-maximum-limit   maximum availaible year                            current year + 20       2050
format-output        format of the result                               timestamp               timestamp or format compatible with strftime
===================  =================================================  ======================  ===================================================


.. _elements-dropdown:

Dropdown
--------

.. figure:: ../Images/dropdown.png

You can add a checkbox like this

::

	$form->add('my-dropdown', 'dropdown', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        custom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
items         avalaible choice for checkbox                                array('1' => 'Item 1', '2' => 'Item 2') or a repository result ($this->myrepo->findAll())
============  ===================================  ======================  ===================================================


.. _elements-email:

Email
-----

.. figure:: ../Images/email.png

You can add a input email like this. Work only with html5 browser.

::

	$form->add('my-email', 'email', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                my firstname
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
============  ===================================  ======================  ===================================================


.. _elements-hidden:

hidden
------

You can add a input hidden like this.

::

	$form->add('my-hidden-field', 'hidden', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                my firstname
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
============  ===================================  ======================  ===================================================


.. _elements-number:

Number
------

.. figure:: ../Images/number.png

You can add a input hidden like this. Work only with html5 browser.

::

	$form->add('my-number-field', 'number', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                my firstname
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
min           minimum number                                               10
max           maximum number                                               100
step          increment step between each number   1                       10
============  ===================================  ======================  ===================================================


.. _elements-password:

Password
--------

.. figure:: ../Images/password.png

You can add a input password like this.

::

	$form->add('my-password', 'password', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                my firstname
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
============  ===================================  ======================  ===================================================



.. _elements-radio:

Radio button
------------

.. figure:: ../Images/radio.png

You can add a checkbox like this

::

	$form->add('my-radiobutton', 'radio', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        custom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
items         avalaible choice for checkbox                                array('1' => 'Item 1', '2' => 'Item 2');
============  ===================================  ======================  ===================================================


.. _elements-range:

Range
-----

.. figure:: ../Images/range.png

You can add a input hidden like this. Work only with html5 browser.

::

	$form->add('my-range-field', 'range', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                my firstname
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
min           minimum number                                               10
max           maximum number                                               100
step          increment step between each number   1                       10
============  ===================================  ======================  ===================================================


.. _elements-submit:

Submit
------

.. figure:: ../Images/submit.png

You can add a submit button like this

::

	$form->add('my-button', 'submit', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
custom        custom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
label         label of the button                                          click me!
============  ===================================  ======================  ===================================================


.. _elements-text:

Text
----

An input text field

.. figure:: ../Images/text.png

You can add a input text like this

::

	$form->add('my-text', 'text', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                my firstname
style         css style for the html tag                                   width: 150px;
disabled      true if the html field is disabled   false                   true or false
title         html tag title                                               my field title
datalist      datalist for autocomplete                                    array('key-1' => 'value 1', 'key-2' => 'value 2')
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
============  ===================================  ======================  ===================================================


.. _elements-textarea:

Textarea
--------

.. figure:: ../Images/textarea.png

You can add a textarea like this

::

	$form->add('my-textarea', 'textarea', $configuration);

Available configuration

============  ===================================  ======================  ===================================================
Property      Description                          Default value           Example
============  ===================================  ======================  ===================================================
placeholder   place holder for the html tag                                my firstname
style         css style for the html tag                                   width: 150px;
title         html tag title                                               my field title
custom        cutsom html attribute                                        customattr="customvalue"
class         css class for the html tag                                   big
============  ===================================  ======================  ===================================================


.. _elements-upload:

Upload
------

.. figure:: ../Images/upload.png

You can add a textarea like this

::

	$form->add('my-file', 'update', $configuration);

Available configuration

============  =====================================  ======================  ===================================================
Property      Description                            Default value           Example
============  =====================================  ======================  ===================================================
placeholder   place holder for the html tag                                  my firstname
style         css style for the html tag                                     width: 150px;
title         html tag title                                                 my field title
custom        cutsom html attribute                                          customattr="customvalue"
class         css class for the html tag                                     big
directory     target upload directory                                        fileadmin/user_upload/my_ext/
filename      target upload file name                                        my-file.pdf
canoverwrite  if true, overwrite existing file                               true or false
show_link     if true, display an link to the file                           true or false
============  =====================================  ======================  ===================================================

