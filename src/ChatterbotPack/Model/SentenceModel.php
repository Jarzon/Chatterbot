<?php
namespace Chatterbot\ChatterbotPack\Model;

class SentenceModel extends \Prim\Model
{
    public function getAllSentences()
    {
        $query = $this->prepare("SELECT sentence_id, sentence FROM bot_sentence");
        $query->execute();

        return $query->fetchAll();
    }

    public function getResponse($words)
    {
        $qMarks = str_repeat('?,', count($words) - 1) . '?';

        $query = $this->prepare("SELECT t.sentence, t.sumWeight, t.totalConnections, t.totalWords
            FROM (
                SELECT BS.sentence, BC.sumWeight, COUNT(BCT.sentence_id) AS totalConnections, COUNT(BW.word_id) AS totalWords
                FROM (
                   SELECT t.sentence_id, t.sumWeight
                   FROM (
                       SELECT tt.sentence_id, tt.sumWeight, max(tt.sumWeight) AS maxWeight
                       FROM (
                              SELECT BC.sentence_id, SUM(BC.weight) AS sumWeight
                              FROM bot_words BW
                                LEFT JOIN bot_connection BC ON BW.word_id = BC.word_id
                              WHERE word IN ($qMarks)
                              GROUP BY BC.sentence_id
                            ) tt
                     GROUP BY tt.sentence_id
                        ) t
                   WHERE t.sumWeight = t.maxWeight
                 ) BC
                LEFT JOIN bot_sentence BS ON BS.sentence_id = BC.sentence_id
                LEFT JOIN bot_connection BCT ON BCT.sentence_id = BC.sentence_id
                LEFT JOIN bot_words BW ON BW.word_id = BCT.word_id AND word IN ($qMarks)
                GROUP BY BCT.sentence_id
                ORDER BY BC.sumWeight DESC
            ) t
            WHERE t.totalWords * (100 / t.totalConnections) > 50
            GROUP BY t.sentence
            ORDER BY t.totalWords * (100 / t.totalConnections) DESC");

        $words = array_merge($words, $words);

        $query->execute($words);

        return $query->fetchAll();
    }

    public function getConnectionLastId()
    {
        $query = $this->prepare("SELECT MAX(connection_id) AS last_id FROM bot_connection");
        $query->execute();

        $result = $query->fetch();

        return $result->last_id ?? 0;
    }

    /**
     * Get all the question for the board
     */
    public function getQuestions($first, $last)
    {
        $sql = 'SELECT BC.sentence_id,
                  BS.sentence,
                  GROUP_CONCAT(BW.word ORDER BY BW.word_id ASC SEPARATOR " ") AS question
                FROM bot_connection BC
                    LEFT JOIN bot_words BW ON BW.word_id = BC.word_id
                    LEFT JOIN bot_sentence BS ON BS.sentence_id = BC.sentence_id
                WHERE BC.connection_id BETWEEN :first AND :last
                GROUP BY BC.connection_id, BC.sentence_id, BS.sentence';

        $parameters = [':first' => $first, ':last' => $last];

        $query = $this->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all the words of a question
     */
    public function getQuestionWords($id)
    {
        $sql = 'SELECT BW.word, BC.weight, BC.sentence_id, BS.sentence
                FROM bot_connection BC
                    LEFT JOIN bot_words BW ON BW.word_id = BC.word_id
                    LEFT JOIN bot_sentence BS ON BS.sentence_id = BC.sentence_id
                WHERE BC.connection_id = :id';

        $parameters = [':id' => $id];

        $query = $this->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Add a sentence to database
     */
    public function addSentence($sentence)
    {
        $query = $this->prepare("INSERT INTO bot_sentence (sentence) VALUES (:sentence)");
        $parameters = [':sentence' => $sentence];

        $query->execute($parameters);

        return $this->db->lastInsertId();
    }

    /**
     * Add a sentence to database
     */
    public function addWord($word)
    {
        $query = $this->prepare("INSERT INTO bot_words (word) VALUES (:word)");
        $parameters = [':word' => $word];

        $query->execute($parameters);

        return $this->db->lastInsertId();
    }

    /**
     * Delete a sentence in the database
     */
    public function deleteSentence($sentence_id)
    {
        $query = $this->prepare('DELETE FROM bot_sentence WHERE id = :sentence_id');
        $parameters = [':sentence_id' => sentence_id];

        $query->execute($parameters);
    }

    /**
     * Get a sentence from database
     */
    public function getWord($word)
    {
        $query = $this->prepare('SELECT word_id FROM bot_words WHERE word = :word LIMIT 1');
        $parameters = [':word' => $word];

        $query->execute($parameters);

        return $query->fetch();
    }

    /**
     * Add a sentence to database
     */
    public function addConnection($connectionId, $wordId, $sentenceId, $weight = 1)
    {
        $query = $this->prepare("INSERT INTO bot_connection (connection_id, word_id, sentence_id, weight) VALUES (:connectionId, :wordId, :sentenceId, :weight)");
        $parameters = [':connectionId' => $connectionId, ':wordId' => $wordId, ':sentenceId' => $sentenceId, 'weight' => $weight];

        $query->execute($parameters);

        return $this->db->lastInsertId();
    }

    /**
     * Update a sentence in database
     */
    public function updateSentence($sentence, $sentence_id)
    {
        $query = $this->prepare("UPDATE bot_sentence SET sentence = :sentence WHERE sentence_id = :sentence_id");
        $parameters = [':sentence' => $sentence, ':sentence_id' => $sentence_id];

        $query->execute($parameters);
    }
}