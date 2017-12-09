<?php

use Phinx\Migration\AbstractMigration;

class InitBotty extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('bot_connection', ['id' => false]);
        $table
            ->addColumn('connection_id', 'integer', ['signed' => false])
            ->addColumn('word_id', 'integer', ['signed' => false])
            ->addColumn('sentence_id', 'integer', ['signed' => false])
            ->addColumn('weight', 'integer')
            ->addIndex(['word_id'])
            ->create();

        $table = $this->table('bot_sentence', ['id' => 'sentence_id']);
        $table
            ->addColumn('sentence', 'string', ['limit' => 255])
            ->create();

        $table = $this->table('bot_words', ['id' => 'word_id']);
        $table
            ->addColumn('word', 'string', ['limit' => 45])
            ->addIndex(['word'])
            ->create();
    }
}