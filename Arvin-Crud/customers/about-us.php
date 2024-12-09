<?php
session_start();
include '../dbcon.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../design.css">
    <title>About Us</title>

</head>

<body>
    <!-- Navbar -->
    <nav class="navbar sticky-top navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="customers-page.php">
                <img src="../resources/logo1.webp" alt="Starbucks Logo" class="logo" style="width: 100px; height: 40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link " href="customers-page.php">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link bg-secondary rounded" href="about-us.php">
                            <i class="bi bi-info-circle"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact-us.php">
                            <i class="bi bi-envelope"></i> Contact Us
                        </a>
                    </li>
                </li>
                </ul>
                
                <!-- Right-aligned links -->
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id'])) : ?>
                        <!-- If customer is logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#cartModal"><i class="bi bi-cart4"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link dropdown-toggle" href="#" role="button" id="navbarDropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person"></i> Settings
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdownAuthLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Authenticate
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAuthLink">
                                <li><a class="dropdown-item" href="../customers-login.php">Login</a></li>
                                <li><a class="dropdown-item" href="../customers-signup.php">Sign Up</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- About Us Content -->
    <div class="container mt-4">
            <div class="d-flex justify-content-center mb-2">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="mb-4">ABOUT OXGN</h2>
                <p>OXGN is a fashion brand designed for all. From cool styles, affordable essentials and one of a kind collaboration pieces, our customers are sure to find that signature OXGN brand of style. With timeless classics from our GENERATIONS collection, the leveled-up sports inspired pieces from PREMIUM THREADS and our inclusive co-gender line COED, our brand serves to make looking cool as easy as breathing.</p>
                
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="mb-4">ABOUT GOLDEN ABC INC.</h2>
                <p>GOLDEN ABC, Inc. (GABC) is a multi-awarded international fashion enterprise that is home to top proprietary brands shaping the retail industry today. Produced, marketed, and retailed under a fast-growing, dynamic family of well-differentiated, proprietary brands: PENSHOPPE, OXYGEN, FORME, MEMO, REGATTA, and BOCU. Originating in the Philippines in 1986, GABC now has more than 1000 strategically located sites and presence in different countries around Asia.</p>
                
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="mb-4">FASHION WITH A CONSCIENCE</h2>
                <p>Side by side its business goals, GOLDEN ABC, along with its employees, actively fulfills responsive stewardship. Through its Corporate Social Responsibility platform called GET UP, the company advocates for arts & culture, education, environment, and community building.</p>

                <p>Get Up has been recognized by the Philippine Quill Awards for CSR Excellence and the Asia-Pacific Tambuli Awards for Best Integrated Digital Program for its public launch. It is also included in the company's People Program of the Year Award given by the People Management Association of the Philippines (PMAP).</p>
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <?php include 'cart.php'; ?>

</body>
</html>
