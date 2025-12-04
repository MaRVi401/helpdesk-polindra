<!DOCTYPE html>
<html>
<head>
    <title>Edit Position</title>
    <style>
        body { font-family: Arial; background:#f1f5f9; padding: 20px; }
        .container { 
            background:white; 
            padding:30px; 
            border-radius:8px; 
            max-width:600px; 
            margin:auto; 
            box-shadow:0 4px 10px rgba(0,0,0,0.1); 
        }
        input, button { padding:10px; width:100%; margin-top:10px; }
        button { background:#2563eb; color:white; border:none; border-radius:5px; cursor:pointer; }
        a { display:inline-block; margin-top:10px; }

        .alert { padding:12px; border-radius:6px; margin-bottom:15px; }
        .alert-error { background:#fee2e2; color:#b91c1c; }
        .alert-success { background:#d1fae5; color:#065f46; }
        .text-danger { color:#b91c1c; font-size:14px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Position</h2>

    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?= session('success') ?>
        </div>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if(session('error')): ?>
        <div class="alert alert-error">
            <?= session('error') ?>
        </div>
    <?php endif; ?>

    <!-- Validation Error Global -->
    <?php if($errors->any()): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach($errors->all() as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= route('position.update', $data_position->id) ?>" method="POST">
        <?= csrf_field() ?>
        <?= method_field('PUT') ?>

        <label>Position Name</label>
        <input 
            type="text" 
            name="nama_jabatan" 
            value="<?= old('nama_jabatan', $data_position->nama_jabatan) ?>"
        >

        <!-- Field Error -->
        <?php if($errors->has('nama_jabatan')): ?>
            <div class="text-danger"><?= $errors->first('nama_jabatan') ?></div>
        <?php endif; ?>

        <button type="submit">Update</button>
    </form>

    <a href="<?= route('position.index') ?>">← Back</a>
</div>

</body>
</html>
