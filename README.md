# ameos_form
Form api for extbase and TYPO3

## Example

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

## Documentation

You can find all the documentation on the typo3 extension repository

http://docs.typo3.org/typo3cms/extensions/ameos_form/
