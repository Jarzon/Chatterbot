<h1>Botty's chatterbot</h1>

<div>
    <p>Add a new question</p>
    <form class="form-inline" action="/botty/" method="POST">
        <input type="text" name="question" id="question" value="" placeholder="Question" required>

        <input type="text" name="response" id="response" value="" placeholder="Response" required>

        <input class="btn btn-default" type="submit" name="submit_add_sentence" value="Create">
    </form>
</div>

<div class="box">
    <h3 class="alignCenter">List of sentences</h3>
    <table class="table table-striped">
        <thead>
            <tr class="grey">
                <th>Question</th>
                <th>Response</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($sentences as $sentence): ?>
                <tr>
                    <td><?=$sentence['question']?></td>
                    <td><?=$sentence['sentence']?></td>
                    <td><a href="/botty/edit/<?=$sentence['sentence_id']?>" class="btn btn-default">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?=$pagination?>
</div>