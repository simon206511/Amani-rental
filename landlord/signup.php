<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Email and Password Validation</title>
    <link rel="stylesheet" href="resources/css/sign.css" />

    <!-- Boxicons CSS -->
    <link
      href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="container">
      <header>Register </header>
      <form method="post" action="register.php">
        <div class="field input-field">
            <input type="text" name="name" placeholder="Enter your full name" required>
            <i class="uil uil-user"></i>
        </div>
        <div class="field email-field">
          <div class="input-field">
            <input type="email" name="email" placeholder="Enter your email" class="email" />
          </div>
          <span class="error email-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">Please enter a valid email</p>
          </span>
        </div>
        <div class="field create-password">
          <div class="input-field">
            <input
              type="password"
              name="password"
              placeholder="Create password"
              class="password"
            />
            <i class="bx bx-hide show-hide"></i>
          </div>
          <span class="error password-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">
              Please enter atleast 8 charatcer with number, symbol, small and
              capital letter.
            </p>
          </span>
        </div>
        <div class="field confirm-password">
          <div class="input-field">
            <input
              type="password"
              name="cPassword"
              placeholder="Confirm password"
              class="cPassword"
            />
            <i class="bx bx-hide show-hide"></i>
          </div>
          <span class="error cPassword-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">Password don't match</p>
          </span>
          <div class="field form-group role">
          <label>Role</label>
          <select name="role" required>
            <option value="">Select Your role</option>
            <option value="Tenant">Tenant</option>
            <option value="Landlord">Landlord</option>
            <option value="Admin">Admin</option>
          </select>
        </div>
        <div class="input-field button">
          <input type="submit" value="Submit Now" />
        </div>
        
        <div class="login-signup">
                <span class="text">Already a member?
                    <a href="login1.php" class="text login-link">Login Now</a>
                </span>
            </div>
      </form>
    </div>
    <!-- JavaScript -->
   <script src="resources\signup.js"></script>
  </body>
</html>