<h1>Botty's chatterbot</h1>

<div>
    <form class="form-inline" action="/botty/edit/" method="POST">
        <label>Add words: </label><input type="text" name="words" id="words" value="" placeholder="Word" required><br>
        <label>Response: </label><input type="text" name="response" id="response" value="<?=$sentence?>" placeholder="Word" required>

        <input class="btn btn-default" type="submit" name="submit_edit_question" value="Edit">
    </form>
</div>

<div class="box">
    <h3 class="alignCenter">List of words</h3>
    <table class="table table-striped">
        <thead>
            <tr class="grey">
                <th>Word</th>
                <th>Weight</th>
                <th>Delete (disabled)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($words as $word): ?>
                <tr>
                    <td><?=$word['word']?></td>
                    <td><?=$word['weight']?></td>
                    <td><a href="/botty/edit/delete/<?=$id?>" class="btn btn-danger">delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>