# ameos_form
Form api for extbase and TYPO3

## Example

$form = \\Ameos\\AmeosForm\\Form\\Factory::make('tx_ameos-test', $testModel);  
$form  
 ->add('label', 'text')  
 ->add('publishdate', 'date')  
 ->add('pid', 'dropdown', ['items' => $availableStorageFolders])  
 ->add('color', 'checkbox', ['items' => $availableColors])  
 ->add('image', 'upload', ['directory' => '/fileadmin/user_upload/ameos/'])  
 ->add('captcha', 'captcha')  
 ->add('submit', 'submit')  
 ->validator('label', 'required', 'Le label is mandatory')  
 ->validator('image', 'fileextension', 'File extension not valid', ['allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']])  
 ->validator('image', 'filesize', 'Maximum file size : 2M', ['maxsize' => '2M']);

if($this->request->getMethod() == 'POST') {  
  $form->bindRequest($this->request);  
  if($form->isValid()) {  
    $this->testRepository->add($testModel);  
  }  
}  
$this->view->assign('form', $form);  
