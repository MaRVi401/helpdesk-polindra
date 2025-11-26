<!DOCTYPE html>
<html>
<head>
    <title>Edit Position</title>
    <style>
        body { font-family: Arial; background:#f1f5f9; padding: 20px; }
        .container { background:white; padding:30px; border-radius:8px; max-width:600px; margin:auto; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        input, button { padding:10px; width:100%; margin-top:10px; }
        button { background:#2563eb; color:white; border:none; border-radius:5px; cursor:pointer; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Position</h2>

    <form action="<?= route('admin.position.update', $data_position->id) ?>" method="POST">
        <?= csrf_field() ?>
        <?= method_field('PUT') ?>

        <label>Position Name</label>
        <input type="text" name="nama_jabatan" value="<?= $data_position->nama_jabatan ?>">

        <button type="submit">Update</button>
    </form>

    <a href="<?= route('admin.position.index') ?>">← Back</a>
</div>

</body>
</html>
