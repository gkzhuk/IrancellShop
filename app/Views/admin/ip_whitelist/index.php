<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>مدیریت IP های مجاز (Whitelist)</h2>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIpModal">
        <i class="bi bi-plus-lg"></i> افزودن IP
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>IP Address</th>
                        <th>توضیحات</th>
                        <th>وضعیت</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ips as $ip): ?>
                        <tr>
                            <td><?= $ip['id'] ?></td>
                            <td><?= esc($ip['ip_address']) ?></td>
                            <td><?= esc($ip['label']) ?></td>
                            <td>
                                <?php if ($ip['is_active']): ?>
                                    <span class="badge bg-success">فعال</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">غیرفعال</span>
                                <?php endif; ?>
                            </td>
                            <td dir="ltr">
                                <?= date('Y-m-d H:i', strtotime($ip['created_at'])) ?>
                            </td>
                            <td>
                                <form action="<?= base_url('admin/ip-whitelist/delete/' . $ip['id']) ?>" method="post" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('آیا مطمئن هستید؟')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($ips)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                هیچ IP ثبت نشده است
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add IP Modal -->
<div class="modal fade" id="addIpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">افزودن IP جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?= base_url('admin/ip-whitelist/create') ?>" method="post">
                <?= csrf_field() ?>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">IP Address</label>
                        <input type="text" name="ip_address" class="form-control"
                               placeholder="مثلا: 192.168.1.1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <input type="text" name="label" class="form-control"
                               placeholder="مثلا: سرور اصلی">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>