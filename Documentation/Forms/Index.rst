Create a form
=============


Creation and edition form
-------------------------

You can create an form for edit or create record.

$form = \\Ameos\\AmeosForm\\Form\\Factory::make('tx_myextension_myplugin', $mymodel);

Where $mymodel is an instance of an extbase model.

For creation : $mymodel = GeneralUtility::makeInstance('\\\\Vendor\\\\Extension\\\\Domain\\\\Model\\\\MyModel');

For edition : $mymodel = $this->myModelRepository->findByUid($modelIdentifier);


Search form
-----------

You can create a search form.

$form = \\Ameos\\AmeosForm\\Form\\Factory::make('tx_myextension_myplugin', $myrepository);

This 

Add an element to the form
--------------------------

After the creation of your form, you add element easily.

$form->add($name, $type', $configuration);

For example : $form->add('my-radiobutton', 'radio', array('items' => array('value1' => 'label 1', 'value2' => 'label 2')));


Add an validator to the form
----------------------------
