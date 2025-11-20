<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hotel Management Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
  body {
    background: linear-gradient(to right, #1e3a8a, #3b82f6);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
  }
  .login-card {
    background: #fff;
    border-radius: 1rem;
    padding: 2rem;
    max-width: 400px;
    width: 100%;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  }
  .login-title { font-weight: 700; color: #1e3a8a; text-align: center; margin-bottom: 1.5rem; }
  .btn-login { background: #1e3a8a; color: #fff; font-weight: 600; border-radius: 50px; }
  .btn-login:hover { background: #2563eb; }
  .alert { display: none; }
</style>
</head>
<body>

<div class="login-card">
  <h3 class="login-title">🏨 Hotel Management Login</h3>
  
  <div class="alert alert-danger" id="error-msg"></div>
  
  <form id="loginForm">
    <div class="mb-3">
      <label>Username</label>
      <input type="text" name="username" class="form-control" placeholder="alifaji11" required>
    </div>
    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" placeholder="••••••••" required>
    </div>
    <div class="d-grid">
      <button type="submit" class="btn btn-login btn-lg">Login</button>
    </div>
  </form>
</div>

<script>
$(document).ready(function() {
  $('#loginForm').submit(function(e) {
    e.preventDefault();
    var formData = $(this).serialize();

    $.ajax({
      url: '<?= base_url("login/login_process") ?>',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(res) {
        if(res.status === 'success'){
          window.location.href = '<?= base_url("dashboard") ?>';
        } else {
          $('#error-msg').text(res.message).fadeIn().delay(3000).fadeOut();
        }
      },
      error: function() {
        $('#error-msg').text('Terjadi kesalahan server').fadeIn().delay(3000).fadeOut();
      }
    });
  });
});
</script>

</body>
</html>
