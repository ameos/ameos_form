# Ameos Form (ameos_form)

Form api for extbase and TYPO3

## Example

	use Ameos\AmeosForm\Service\FormService;
	use Ameos\Test\Domain\Model\Movie;
	use Ameos\Test\Domain\Repository\MovieRepository;
	use Psr\Http\Message\ResponseInterface;
	use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

	class TestController extends ActionController
	{
		public function __construct(private FormService $formService, private MovieRepository $movieRepository)
		{
		}

		public function addMovieAction(): ResponseInterface
		{
			$movie = new Movie();
			$form = $this->formService->create('tx_test_testameosform', $movie);
			$form->add('title', 'text')->add('year', 'text')->add('submit', 'submit');

			if ($form->isSubmitted()) {
				$form
					->addConstraint('title', 'required', 'Title is required')
					->addConstraint('year', 'required', 'Year is required');

				if ($form->isValid()) {
					$this->movieRepository->add($movie);
					$this->addFlashMessage('Movie added');
					$this->redirect('index');
				}
			}

			$this->view->assign('form', $form);

			return $this->htmlResponse();
		}
	}

## Documentation

You can find all the documentation on the typo3 extension repository

http://docs.typo3.org/typo3cms/extensions/ameos_form/
