<div class="container-fluid px-4" style="margin-top: 100px;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <!-- HEADER & FILTERS -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div>
                            <h4 class="fw-bold mb-1 text-primary">Booking Report</h4>
                            <p class="text-muted small mb-0">Timeline of all reservations, check-ins, and check-outs</p>
                        </div>
                        
                        <form action="<?= base_url('report/index'); ?>" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: auto;">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event small"></i></span>
                                <input type="date" name="start_date" class="form-control bg-light border-start-0" value="<?= $start_date; ?>" title="Start Date">
                                <span class="input-group-text bg-light border-start-0 border-end-0">to</span>
                                <input type="date" name="end_date" class="form-control bg-light border-start-0" value="<?= $end_date; ?>" title="End Date">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                                Filter
                            </button>
                            <?php if($start_date || $end_date): ?>
                                <a href="<?= base_url('report/index'); ?>" class="btn btn-light btn-sm rounded-pill px-3 border">Reset</a>
                            <?php endif; ?>
                            <a href="<?= base_url('report/export?start_date='.$start_date.'&end_date='.$end_date); ?>" class="btn btn-success btn-sm rounded-pill px-3">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                            </a>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th class="border-0 pb-3" style="width: 50px;">No</th>
                                    <th class="border-0 pb-3">Room & Guest</th>
                                    <th class="border-0 pb-3">Reserved At</th>
                                    <th class="border-0 pb-3">Checked-in At</th>
                                    <th class="border-0 pb-3">Checked-out At</th>
                                    <th class="border-0 pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php 
                                        $no = ($this->uri->segment(3)) ? $this->uri->segment(3) + 1 : 1; 
                                        foreach ($logs as $log): 
                                    ?>
                                        <tr>
                                            <td class="py-3 fw-medium"><?= $no++; ?></td>
                                            <td class="py-3">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-dark"><?= $log['guest_name'] ?: 'Unknown'; ?></span>
                                                    <span class="text-muted small">Room: <?= $log['room_number'] ?: '-'; ?></span>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <?php if($log['reserved_at']): ?>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium small"><?= date('d M Y', strtotime($log['reserved_at'])); ?></span>
                                                        <span class="text-muted" style="font-size: 0.75rem;"><?= date('H:i', strtotime($log['reserved_at'])); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3">
                                                <?php if($log['checked_in_at']): ?>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium small"><?= date('d M Y', strtotime($log['checked_in_at'])); ?></span>
                                                        <span class="text-muted" style="font-size: 0.75rem;"><?= date('H:i', strtotime($log['checked_in_at'])); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3">
                                                <?php if($log['checked_out_at']): ?>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium small"><?= date('d M Y', strtotime($log['checked_out_at'])); ?></span>
                                                        <span class="text-muted" style="font-size: 0.75rem;"><?= date('H:i', strtotime($log['checked_out_at'])); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3">
                                                <?php 
                                                    $badgeClass = 'bg-secondary';
                                                    if ($log['current_status'] == 'booked') $badgeClass = 'bg-info';
                                                    elseif ($log['current_status'] == 'checked_in' || $log['current_status'] == 'occupied') $badgeClass = 'bg-success';
                                                    elseif ($log['current_status'] == 'checked_out') $badgeClass = 'bg-warning text-dark';
                                                ?>
                                                <span class="badge <?= $badgeClass; ?> rounded-pill px-3">
                                                    <?= ucfirst(str_replace('_', ' ', $log['current_status'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            No booking logs found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <?php if(isset($pagination)): ?>
                        <div class="mt-4">
                            <?= $pagination; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
