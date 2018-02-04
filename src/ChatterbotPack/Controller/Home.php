<?php
namespace Chatterbot\ChatterbotPack\Controller;

use Prim\Controller;

use Jarzon\Pagination;

class Home extends Controller
{
    /** @var $sentenceHelper \Chatterbot\ChatterbotPack\Service\SentenceHelper */
    protected $sentenceHelper;

    function build() {
        $this->setTemplate('design', 'ChatterbotPack');

        $this->sentenceHelper = $this->container->getSentenceHelper();
    }

    public function login()
    {
        if(isset($_POST['password'])) {
            if(strcmp($_POST['password'], 'Ijustlovekillingbotty') === 0) {
                $_SESSION['auth'] = true;

                $this->redirect('/admin/');
            }
        }

        $this->design('login');
    }

    public function loginVerification()
    {
        if(!$_SESSION['auth']) {
            $this->redirect('/admin/login');
        }
    }

    public function index(int $page = 1)
    {
        $this->loginVerification();

        /** @var \Chatterbot\ChatterbotPack\Model\SentenceModel $sentence */
        $sentence = $this->getModel('SentenceModel');

        if(isset($_POST['submit_ask'])) {
            $words = $this->sentenceHelper->getWords($_POST['question']);

            $this->addVar('wordCount', count($words));
            $this->addVar('response', $sentence->getResponse($words));
        }

        // if we have POST data to create a new sentence entry
        if(isset($_POST['submit_add_sentence'])) {
            $words = $this->sentenceHelper->getWords($_POST['question']);

            foreach($words as $word) {
                if($word != null) {
                    $wordRes = $sentence->getWord($word);

                    if($wordRes) {
                        $wordId = $wordRes->word_id;
                    } else {
                        $wordId = $sentence->addWord($word);
                    }

                    $words_list[] = ['id' => $wordId, 'weight' => $this->sentenceHelper->getWordWeight($word)];
                }
            }

            $sentenceId = $sentence->addSentence($_POST['response']);
            $lastId = $sentence->getConnectionLastId();
            $lastId = $lastId->last_id;

            $connectionId = ($lastId + 1);

            foreach($words_list as $word) {
                $sentence->addConnection($connectionId, $word['id'], $sentenceId, $word['weight']);
            }
        }

        $lastId = $sentence->getConnectionLastId();
        $lastId = $lastId->last_id;

        if($lastId == null) $lastId = 0;

        // Pagination
        $questionPerPage = 15;

        $paginator = new Pagination($page, $lastId, $questionPerPage, 5);
        $page = $paginator->getPage();
        $first = $paginator->getFirstPageElement();
        $last = $paginator->getLast();

        $this->design('index', '', [
            'page' => $page,
            'sentences' => $sentence->getQuestions($first, $last),
            'pagination' => $paginator->showPages(),
        ]);
    }

    public function editQuestion(int $connectionId)
    {
        $this->loginVerification();

        $sentence = new SentenceModel($this->db);

        $words = $sentence->getQuestionWords($connectionId);

        $response = $words[0]['sentence'];
        $responseId = $words[0]['sentence_id'];

        if(isset($_POST['submit_edit_question'])) {
            if($response !== $_POST['response']) {
                $sentence->updateSentence($response, $responseId);
            }

            if(!empty($_POST['response'])) {
                $words = $this->getWords($_POST['question']);

                // Try to get the word in DB else create one
                foreach($words as $word) {
                    if($word != null) {
                        $wordRes = $sentence->getWord($word);

                        if($wordRes) {
                            $wordId = $wordRes->word_id;
                        } else {
                            $wordId = $sentence->addWord($word);
                        }

                        $words_list[] = ['id' => $wordId, 'weight' => $this->getWordWeight($word)];
                    }
                }

                foreach($words_list as $word) {
                    $sentence->addConnection($connectionId, $word['id'], $responseId, $word['weight']);
                }
            }
        }

        $this->addVar('sentenceId', $responseId);
        $this->addVar('sentence', $response);
        $this->addVar('words', $words);
        $this->addVar('id', $connectionId);


        $this->design('edit');
    }

    public function deleteSentence(int $sentence_id)
    {
        $this->loginVerification();

        $sentence = new SentenceModel($this->db);

        if (isset($sentence_id)) {
            $sentence->deleteSentence($sentence_id);
        }

        $this->redirect('/admin/');
    }
}