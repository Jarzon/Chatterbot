<?php
namespace Omnishaven\ChatterbotPack\Controller;

use Prim\Controller;

use Omnishaven\ChatterbotPack\Model\SentenceModel;
use Omnishaven\BasePack\Service\Paginator;

/**
 * Class Sentences
 *
 */
class Home extends Controller
{
    function buildSentence()
    {
        $this->loginVerification();
    }

    public function loginVerification()
    {
        if(!$_SESSION['auth']) {
            header('location: /login');
            exit();
        }
    }

    /**
     * PAGE: index
     * @param int $page Current page
     */
    public function index($page = 1)
    {
        $sentence = new SentenceModel($this->db);

        $lastId = $sentence->getConnectionLastId();
        $lastId = $lastId->last_id;

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
     * ACTION: addSentence
     */
    public function addSentence()
    {
        // if we have POST data to create a new sentence entry
        if(isset($_POST['question'])) {
            $commonWords = [
                'he', 'of', 'and', 'a', 'to', 'in', 'is', 'you', 'that', 'it', 'he', 'was', 'for', 'on', 'are', 'as', 'with', 'his', 'they', 'I', 'at', 'be', 'this', 'have', 'from', 'or', 'one', 'had', 'by', 'word', 'but', 'not', 'what', 'all', 'were', 'we', 'when', 'your', 'can', 'said', 'there', 'use', 'an', 'each', 'which', 'she', 'do', 'how', 'their', 'if', 'will', 'up', 'there', 'about', 'out', 'many', 'then', 'them', 'these', 'so', 'some', 'her', 'would', 'make', 'like', 'him', 'into', 'time', 'has', 'look', 'two', 'more', 'write', 'go', 'see', 'number', 'no', 'way', 'could', 'people', 'my', 'than', 'first', 'important', 'been', 'call', 'who', 'oil', 'its', 'now', 'find', 'long', 'down', 'day', 'did', 'get', 'come', 'made', 'may', 'part', 'us', 'because'
            ];

            $strip = ['"', '\''];

            $sentence = new SentenceModel($this->db);

            $words_list = [];

            $question = $_POST['question'];

            $question = str_replace($strip, '', $question);
            $question = str_replace('!', '', $question, $countExclamation);
            $question = str_replace('?', '', $question, $countInterrogation);

            $words = explode(' ', $question);

            $words = array_map('strtolower', $words);

            foreach($words as $word) {
                if($word != null) {
                    $wordRes = $sentence->getWord($word);

                    $weight = 2;
                    if($wordRes) {
                        $wordId = $wordRes->word_id;
                    } else {
                        $wordId = $sentence->addWord($word);
                    }

                    if(in_array($word, $commonWords)) $weight = 1;

                    $words_list[] = ['id' => $wordId, 'weight' => $weight];
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

        $this->redirect('/botty/');
    }

    /**
     * ACTION: updateSentence
     */
    public function editQuestion($connectionId)
    {
        $sentence = new SentenceModel($this->db);

        $words = $sentence->getQuestionWords($connectionId);

        $response = $words[0]['sentence'];
        $responseId = $words[0]['sentence_id'];

        if(isset($_POST['submit_edit_question'])) {
            if($response !== $_POST['response']) {
                $sentence->updateSentence($responseId, $response);
            }

            if(!empty($_POST['response'])) {
                $commonWords = [
                    'he', 'of', 'and', 'a', 'to', 'in', 'is', 'you', 'that', 'it', 'he', 'was', 'for', 'on', 'are', 'as', 'with', 'his', 'they', 'I', 'at', 'be', 'this', 'have', 'from', 'or', 'one', 'had', 'by', 'word', 'but', 'not', 'what', 'all', 'were', 'we', 'when', 'your', 'can', 'said', 'there', 'use', 'an', 'each', 'which', 'she', 'do', 'how', 'their', 'if', 'will', 'up', 'there', 'about', 'out', 'many', 'then', 'them', 'these', 'so', 'some', 'her', 'would', 'make', 'like', 'him', 'into', 'time', 'has', 'look', 'two', 'more', 'write', 'go', 'see', 'number', 'no', 'way', 'could', 'people', 'my', 'than', 'first', 'important', 'been', 'call', 'who', 'oil', 'its', 'now', 'find', 'long', 'down', 'day', 'did', 'get', 'come', 'made', 'may', 'part', 'us', 'because'
                ];

                $strip = ['"', '\''];

                $words_list = [];

                $question = $_POST['question'];

                $question = str_replace($strip, '', $question);
                $question = str_replace('!', '', $question, $countExclamation);
                $question = str_replace('?', '', $question, $countInterrogation);

                $words = explode(' ', $question);

                $words = array_map('strtolower', $words);

                // Try to get the word in DB else create one
                foreach($words as $word) {
                    if($word != null) {
                        $wordRes = $sentence->getWord($word);

                        $weight = 2;
                        if($wordRes) {
                            $wordId = $wordRes->word_id;
                        } else {
                            $wordId = $sentence->addWord($word);
                        }

                        if(in_array($word, $commonWords)) $weight = 1;

                        $words_list[] = ['id' => $wordId, 'weight' => $weight];
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
        $sentence = new SentenceModel($this->db);

        if (isset($sentence_id)) {
            $sentence->deleteSentence($sentence_id);
        }

        $this->redirect('/botty/');
    }
}