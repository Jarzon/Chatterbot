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
            $commonWords = [
                'he', 'and', 'a', 'to', 'is', 'you', 'that', 'it', 'he', 'for', 'as', 'with', 'his', 'they', 'I', 'at', 'this', 'or', 'one', 'by', 'but', 'not', 'what', 'we', 'an', 'your', 'she', 'her', 'him', 'their', 'if', 'there', 'out', 'them', 'these', 'so', 'my', 'than', 'its', 'us'
            ];

            $strip = ['"', '\''];

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
        $this->loginVerification();

        $sentence = new SentenceModel($this->db);

        if (isset($sentence_id)) {
            $sentence->deleteSentence($sentence_id);
        }

        $this->redirect('/admin/');
    }
}