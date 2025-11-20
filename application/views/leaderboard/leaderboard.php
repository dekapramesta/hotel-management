<main class="container py-5" style="margin-top: 20px;">
  <!-- ====== Header Section ====== -->
  <div class="text-center mb-5">
    <h2 class="fw-bold mb-2" style="color: #1E88E5;">Leaderboard Performance</h2>
    <p class="text-secondary">Rekapitulasi performa tim bulan <b>Desember</b></p>
  </div>

  <!-- ====== Stats Cards Section ====== -->
  <div class="row g-4 mb-5">
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3 border-0 bg-soft-primary">
        <div class="fs-3 fw-bold text-primary">10</div>
        <div class="text-muted small">Total Teams</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3 border-0 bg-soft-success">
        <div class="fs-3 fw-bold text-success">78</div>
        <div class="text-muted small">Average Score</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3 border-0 bg-soft-warning">
        <div class="fs-3 fw-bold text-warning">98</div>
        <div class="text-muted small">Total ACT Minutes</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3 border-0 bg-soft-danger">
        <div class="fs-3 fw-bold text-danger">5.0</div>
        <div class="text-muted small">Top Rating</div>
      </div>
    </div>
  </div>

  <!-- ====== Leaderboard Table ====== -->
  <div class="card leaderboard-card border-0 shadow-lg">
    <div class="card-header bg-light py-3 px-4 border-bottom-0" style="border-radius: 12px 12px 0 0;">
      <h5 class="mb-0 fw-semibold text-dark">Top Performing Teams</h5>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="border-radius: 0 0 12px 12px; overflow: hidden;">
        <thead class="table-light">
          <tr>
            <th>Rank</th>
            <th>Team</th>
            <th>Lantai</th>
            <th>ACT Minutes</th>
            <th>Rating</th>
            <th>Final Score</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="rank-badge rank-1">1</span></td>
            <td class="fw-semibold">Team AB</td>
            <td>Lantai 3</td>
            <td>12</td>
            <td>5.0</td>
            <td><span class="score-badge bg-primary">84</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-2">2</span></td>
            <td class="fw-semibold">Team CD</td>
            <td>Lantai 2</td>
            <td>10</td>
            <td>4.8</td>
            <td><span class="score-badge bg-success">82</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-3">3</span></td>
            <td class="fw-semibold">Team EF</td>
            <td>Lantai 4</td>
            <td>15</td>
            <td>4.5</td>
            <td><span class="score-badge bg-warning">78</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-4">4</span></td>
            <td class="fw-semibold">Team GH</td>
            <td>Lantai 1</td>
            <td>8</td>
            <td>4.4</td>
            <td><span class="score-badge bg-info text-white">75</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-5">5</span></td>
            <td class="fw-semibold">Team IJ</td>
            <td>Lantai 2</td>
            <td>11</td>
            <td>4.2</td>
            <td><span class="score-badge bg-secondary text-white">72</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-6">6</span></td>
            <td class="fw-semibold">Team KL</td>
            <td>Lantai 3</td>
            <td>9</td>
            <td>4.1</td>
            <td><span class="score-badge bg-danger text-white">70</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-7">7</span></td>
            <td class="fw-semibold">Team MN</td>
            <td>Lantai 1</td>
            <td>7</td>
            <td>4.0</td>
            <td><span class="score-badge bg-primary">68</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-8">8</span></td>
            <td class="fw-semibold">Team OP</td>
            <td>Lantai 4</td>
            <td>5</td>
            <td>3.8</td>
            <td><span class="score-badge bg-success">65</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-9">9</span></td>
            <td class="fw-semibold">Team QR</td>
            <td>Lantai 2</td>
            <td>6</td>
            <td>3.7</td>
            <td><span class="score-badge bg-warning">62</span></td>
          </tr>
          <tr>
            <td><span class="rank-badge rank-10">10</span></td>
            <td class="fw-semibold">Team ST</td>
            <td>Lantai 3</td>
            <td>4</td>
            <td>3.5</td>
            <td><span class="score-badge bg-info text-white">60</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<style>

  .bg-soft-primary { background: #e3f2fd; border-radius: 12px; }
  .bg-soft-success { background: #e8f5e9; border-radius: 12px; }
  .bg-soft-warning { background: #fff3e0; border-radius: 12px; }
  .bg-soft-danger { background: #ffebee; border-radius: 12px; }
  .card.shadow-sm {
    border-radius: 12px;
    transition: all 0.3s ease;
  }
  .card.shadow-sm:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }

  /* Leaderboard Card */
  .leaderboard-card {
    border-radius: 16px;
    overflow: hidden;
    background: #ffffff;
  }

  /* Rank Badge */
  .rank-badge {
    display: inline-block;
    width: 36px;
    height: 36px;
    line-height: 36px;
    border-radius: 50%;
    text-align: center;
    font-weight: 700;
    color: #fff;
  }
  .rank-1 { background: #fbc02d; }
  .rank-2 { background: #90caf9; }
  .rank-3 { background: #ffb74d; }
  .rank-4 { background: #4fc3f7; }
  .rank-5 { background: #9e9e9e; }
  .rank-6 { background: #e57373; }
  .rank-7 { background: #64b5f6; }
  .rank-8 { background: #81c784; }
  .rank-9 { background: #ffd54f; }
  .rank-10 { background: #4dd0e1; }

  /* Score Badge */
  .score-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 600;
    color: #fff;
  }
  .bg-primary { background-color: #1E88E5 !important; }
  .bg-success { background-color: #43A047 !important; }
  .bg-warning { background-color: #FB8C00 !important; }
  .bg-info { background-color: #29B6F6 !important; }
  .bg-danger { background-color: #E53935 !important; }
  .bg-secondary { background-color: #757575 !important; }

  /* Table hover effect */
  table.table-hover tbody tr:hover {
    background-color: #E3F2FD !important;
    transition: 0.2s;
  }

  table th, table td {
    vertical-align: middle;
    font-size: 0.95rem;
  }

  table thead th {
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #475569;
    letter-spacing: 0.5px;
  }
</style>
