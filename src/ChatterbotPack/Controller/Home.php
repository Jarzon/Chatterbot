<?php
namespace Chatterbot\ChatterbotPack\Controller;

use Prim\Controller;

use Jarzon\Pagination;

class Home extends Controller
{
    /**
     * @var \Chatterbot\ChatterbotPack\Service\SentenceHelper $sentenceHelper
     */
    public $sentenceHelper;

    function build() {
        $this->setTemplate('design', 'ChatterbotPack');

        $this->sentenceHelper = $this->container->getSentenceHelper();
    }

    public function login()
    {
        if(isset($_POST['password'])) {
            if(strcmp($_POST['password'], $this->options['backend_password']) === 0) {
                $_SESSION['auth'] = true;

                $this->redirect('/admin/');
            }
        }

        $this->design('login');
    }

    public function loginVerification()
    {
        if(empty($_SESSION['auth']) || !$_SESSION['auth']) {
            $this->redirect('/admin/login');
        }
    }

    public function index(int $page = 1)
    {
        $this->loginVerification();

        /** @var \Chatterbot\ChatterbotPack\Model\SentenceModel $model */
        $model = $this->getModel('SentenceModel');

        if(isset($_POST['submit_ask'])) {
            $words = $this->sentenceHelper->getWords($_POST['question']);

            $this->addVar('wordCount', count($words));
            $this->addVar('response', $model->getResponse($words));
        }

        $lastId = $model->getConnectionLastId();

        // if we have POST data to create a new sentence entry
        if(isset($_POST['submit_add_sentence'])) {
            $words = $this->sentenceHelper->getWords($_POST['question']);

            $sentenceId = $model->addSentence($_POST['response']);

            $connectionId = ($lastId + 1);

            foreach($words as $word) {
                if($word != null) {
                    $wordRes = $model->getWord($word);

                    if($wordRes) {
                        $wordId = $wordRes->word_id;
                    } else {
                        $wordId = $model->addWord($word);
                    }

                    $model->addConnection($connectionId, $wordId, $sentenceId, $this->sentenceHelper->getWordWeight($word));
                }
            }
        }

        // Pagination
        $questionPerPage = 15;

        $paginator = new Pagination($page, $lastId, $questionPerPage, 5);
        $page = $paginator->getPage();
        $first = $paginator->getFirstPageElement();
        $last = $paginator->getLast();

        $this->design('index', '', [
            'page' => $page,
            'sentences' => $model->getQuestions($first, $last),
            'pagination' => $paginator->showPages(),
        ]);
    }

    public function editQuestion(int $connectionId)
    {
        $this->loginVerification();

        /** @var \Chatterbot\ChatterbotPack\Model\SentenceModel $model */
        $model = $this->getModel('SentenceModel');

        $words = $model->getQuestionWords($connectionId);

        $response = $words[0]['sentence'];
        $responseId = $words[0]['sentence_id'];

        if(isset($_POST['submit_edit_question'])) {
            if($response !== $_POST['response']) {
                $model->updateSentence($response, $responseId);
            }

            if(!empty($_POST['response'])) {
                $words = $this->sentenceHelper->getWords($_POST['question']);

                // Try to get the word in DB else create one
                foreach($words as $word) {
                    if($word != null) {
                        $wordRes = $model->getWord($word);

                        if($wordRes) {
                            $wordId = $wordRes->word_id;
                        } else {
                            $wordId = $model->addWord($word);
                        }

                        $words_list[] = ['id' => $wordId, 'weight' => $this->sentenceHelper->getWordWeight($word)];
                    }
                }

                foreach($words_list as $word) {
                    $model->addConnection($connectionId, $word['id'], $responseId, $word['weight']);
                }
            }
        }

        $this->design('edit', 'ChatterbotPack', [
            'sentenceId' => $responseId,
            'sentence' => $response,
            'words' => $words,
            'id' => $connectionId,
        ]);
    }

    public function deleteSentence(int $sentence_id)
    {
        $this->loginVerification();

        /** @var \Chatterbot\ChatterbotPack\Model\SentenceModel $model */
        $model = $this->getModel('SentenceModel');

        if (isset($sentence_id)) {
            $model->deleteSentence($sentence_id);
        }

        $this->redirect('/admin/');
    }
}