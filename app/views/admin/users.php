<section class="admin-panel">
    <div class="panel-heading">
        <h2>Người dùng</h2>
        <span><?= count($users) ?> tài khoản</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Điện thoại</th>
                <th>Quyền</th>
                <th>Ngày tạo</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e($user['phone']) ?></td>
                    <td>
                        <form class="inline-form" method="post" action="<?= url('admin/users/role/' . $user['id']) ?>">
                            <?= csrf_field() ?>
                            <select name="role">
                                <option value="user" <?= selected('user', $user['role']) ?>>user</option>
                                <option value="admin" <?= selected('admin', $user['role']) ?>>admin</option>
                            </select>
                            <button class="btn ghost small" type="submit">Lưu</button>
                        </form>
                    </td>
                    <td><?= e(date('d/m/Y', strtotime($user['created_at']))) ?></td>
                    <td>
                        <?php if ($user['role'] !== 'admin'): ?>
                            <form method="post" action="<?= url('admin/users/delete/' . $user['id']) ?>" onsubmit="return confirm('Xóa người dùng này?')">
                                <?= csrf_field() ?>
                                <button class="btn danger small" type="submit">Xóa</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
