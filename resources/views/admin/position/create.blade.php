<!DOCTYPE html>
<html>
<head>
    <title>Add Position</title>
    <style>
        body { font-family: Arial; background:#f1f5f9; padding: 20px; }
        .container { background:white; padding:30px; border-radius:8px; max-width:600px; margin:auto; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        input, button { padding:10px; width:100%; margin-top:10px; }
        button { background:#2563eb; color:white; border:none; border-radius:5px; cursor:pointer; }
        a { display:inline-block; margin-top:10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Tambah Jabatan</h2>

    <form action="<?= route('admin.position.store') ?>" method="POST">
        <?= csrf_field() ?>

        <label>Nama Jabatan</label>
        <input type="text" name="nama_jabatan" placeholder="Enter position name">

        <button type="submit">Save</button>
    </form>

    <a href="<?= route('admin.position.index') ?>">← Back</a>
</div>

</body>
</html>
