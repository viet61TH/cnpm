<?php 
// Khởi tạo phiên
session_start();
 
// Kiểm tra xem người dùng đã đăng nhập chưa, nếu có thì chuyển hướng người đó đến trang chào mừng
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ");
    exit;
}
 
// Bao gồm tệp cấu hình
require_once "config.php";
 
// Xác định các biến và khởi tạo với các giá trị trống
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Xử lý dữ liệu biểu mẫu khi biểu mẫu được gửi
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // kiểm tra nếu tài khoản trống
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // kiểm tra nếu mật khẩu trống
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Xác thực thông tin đăng nhập
    if(empty($username_err) && empty($password_err)){
        // Chuẩn bị một tuyên bố lựa chọn
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Liên kết các biến với câu lệnh đã chuẩn bị dưới dạng tham số
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Cố gắng thực hiện tuyên bố đã chuẩn bị
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Kiểm tra xem tên người dùng có tồn tại không, nếu có thì xác minh mật khẩu
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Các biến kết quả ràng buộc
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Mật khẩu chính xác, vì vậy hãy bắt đầu một phiên mới
                            session_start();
                            
                            // Lưu trữ dữ liệu vào database
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: ");
                        } else{
                            // Mật khẩu không hợp lệ, hiển thị thông báo lỗi chung
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Tên người dùng không tồn tại, hiển thị thông báo lỗi chung
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleslogin.css">
</head>
<body>
    <!-- header -->
<div class="header">
    <!-- chuyen mau trnag cho navbar -->
    <nav class="navbar navbar-light bg-light p-3 mb-2 bg-white text-dark">

    </nav>
</div>
    <!-- header -->


<div class="content">
    <p class="title-login1">
    </p>
    <p class="title-login2">
        <h6 class="text-center">
            Đăng nhập tài khoản
        </h6>
    </p>
<?php 
    if(!empty($login_err)){
        echo '<div class="alert alert-danger">' . $login_err . '</div>';
}        
?>
       
<form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group form1 text-center">
        <label>Tài khoản </label>
        <input type="text" name="username" placeholder="nhập tên đăng nhập" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
        <span class="invalid-feedback"><?php echo $username_err; ?></span>
    </div>    
    <div class="form-group form2 text-center">
        <label>Mật khẩu </label>
        <input type="password" name="password" placeholder="nhập mật khẩu"  <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
        <span class="invalid-feedback"><?php echo $password_err; ?></span>
    </div>
        <div class="form-group form3 text-center">
            <input type="submit" class="btn btn-primary" value="Login">
        </div>
        <p class="text-center">Bạn chưa có tài khoản ? <a href="/restaurant/login/logup.php">Đăng ký ngay</a>.</p>
</form>
</div>

<script>
window.onscroll = function() {myFunction()};

var header = document.getElementById("header");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}
</script>
</body>
</html>
