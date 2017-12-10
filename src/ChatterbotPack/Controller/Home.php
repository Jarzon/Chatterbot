<?php
namespace Chatterbot\ChatterbotPack\Controller;

use Prim\Controller;

use Chatterbot\ChatterbotPack\Model\SentenceModel;
use PrimUtilities\Paginator;

/**
 * Class Sentences
 *
 */
class Home extends Controller
{
    /**
     * PAGE: login
     */
    public function login()
    {
        if(isset($_POST['password'])) {
            if(strcmp($_POST['password'], 'Ijustlovekillingbotty') === 0) {
                $_SESSION['auth'] = true;

                header('location: /admin/');
                exit();
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

    public function getWords($question) : array {
        $question = strtolower($question);

        $question = str_replace(['"', '\'', '.', '!', '?'], '', $question);

        return explode(' ', $question);
    }

    public function getWordWeight($word) : int {
        $commonWords = [
            'he', 'and', 'a', 'to', 'is', 'you', 'that', 'it', 'he', 'for', 'as', 'with', 'his', 'they', 'I', 'at', 'this', 'or', 'one', 'by', 'but', 'not', 'what', 'we', 'an', 'your', 'she', 'her', 'him', 'their', 'if', 'there', 'out', 'them', 'these', 'so', 'my', 'than', 'its', 'us'
        ];

        $weight = 2;

        if(in_array($word, $commonWords)) $weight--;

        return $weight;
    }

    /**
     * PAGE: index
     * @param int $page Current page
     */
    public function index($page = 1)
    {
        $this->loginVerification();

        $sentence = new SentenceModel($this->db);

        if(isset($_POST['submit_ask'])) {
            $question = strtolower($_POST['question']);

            $words = explode(' ', $question);

            $wordCount = count($words);

            $response = $sentence->getResponse($words);

            $this->addVar('wordCount', $wordCount);
            $this->addVar('response', $response);
        }

        // if we have POST data to create a new sentence entry
        if(isset($_POST['submit_add_sentence'])) {


            $words = $this->getWords($_POST['question']);

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

        $paginator = new Paginator($page, $lastId, $questionPerPage, 5);
        $page = $paginator->getPage();
        $first = $paginator->getFirstPageElement();
        $last = $paginator->getLast();

        $this->addVar('page', $page);
        $this->addVar('sentences', $sentence->getQuestions($first, $last));
        $this->addVar('pagination', $paginator->showPages());
        

        $this->design('index');
    }

    /**
     * ACTION: updateSentence
     */
    public function editQuestion($connectionId)
    {
        $this->loginVerification();

        $sentence = new SentenceModel($this->db);

        $words = $sentence->getQuestionWords($connectionId);

        $response = $words[0]['sentence'];
        $responseId = $words[0]['sentence_id'];

        if(isset($_POST['submit_edit_question'])) {
            if($response !== $_POST['response']) {
                $sentence->updateSentence($responseId, $response);
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
                    $sentence->addConnection($connectionId, $word['id'], $sentenceId, $word['weight']);
                }
            }
        }

        $this->addVar('sentenceId', $responseId);
        $this->addVar('sentence', $response);
        $this->addVar('words', $words);
        $this->addVar('id', $connectionId);


        $this->design('edit');
    }

    /**
     * ACTION: deleteSentence
     * @param int $sentence_id Id of the to-delete sentence
     */
    public function deleteSentence($sentence_id)
    {
        $this->loginVerification();

        $sentence = new SentenceModel($this->db);

        if (isset($sentence_id)) {
            $sentence->deleteSentence($sentence_id);
        }

        $this->redirect('/admin/');
    }
}