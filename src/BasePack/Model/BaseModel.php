<?php
namespace Chatterbot\BasePack\Model;

use Prim\Model;

class BaseModel extends Model
{
    public function getResponse()
    {
        $query = $this->db->prepare("SELECT BS.sentence, BC.nb
            FROM (
                SELECT t.sentence_id, t.nb
                FROM (
                  SELECT BC.sentence_id, SUM(BC.weight) AS nb
                  FROM bot_words BW
                  LEFT JOIN bot_connection BC ON BW.word_id = BC.word_id
                  WHERE word IN (?)
                  GROUP BY BC.sentence_id
                ) t
                WHERE nb = (
                  SELECT max(tt.nb) AS maximum
                  FROM(
                      SELECT BC.sentence_id, SUM(BC.weight) AS nb
                      FROM bot_words BW
                      LEFT JOIN bot_connection BC ON BW.word_id = BC.word_id
                      WHERE word IN (?)
                      GROUP BY BC.sentence_id
                  ) tt
              )
            ) BCLEFT
            JOIN bot_sentence BS ON BC.sentence_id = BS.sentence_id");
        $query->execute();

        return $query->fetchAll();
    }
}