<!DOCTYPE html>
<html>
<head>
    <title>Position Management</title>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <style>
        body { font-family: Arial, sans-serif; background: #f1f5f9; padding: 20px; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 900px; margin: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; }
        th { background: #f8fafc; text-transform: uppercase; font-size: 0.8rem; }
        .button { padding: 8px 12px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.9rem; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-danger { background: none; color: #dc2626; border: none; cursor: pointer; }
        .btn-edit { color: #2563eb; }
    </style>
</head>
<body>

<div class="container">
    <h1>Manajemen Jabatan</h1>

    <?php if(session('success')): ?>
        <div style="background:#bbf7d0; padding:10px; margin-bottom:15px; border-radius:5px;">
            <?= session('success') ?>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div style="background:#fecaca; padding:10px; margin-bottom:15px; border-radius:5px;">
            <?= session('error') ?>
        </div>
    <?php endif; ?>

    <a href="<?= route('admin.position.create') ?>" class="button btn-primary">Tambah Jabatan</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Jabatan</th>
                <th style="width: 20%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($data_positions) > 0): ?>
                <?php foreach($data_positions as $index => $pos): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $pos->nama_jabatan ?></td>
                    <td>
                        <a href="<?= route('admin.position.edit', $pos->id) ?>" class="btn-edit">Edit</a>

                        <form action="<?= route('admin.position.destroy', $pos->id) ?>" method="POST" style="display:inline;" onsubmit="return confirm('Delete this position?')">
                            <?= csrf_field() ?>
                            <?= method_field('DELETE') ?>
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center;">Tidak Ada Data Jabatan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
