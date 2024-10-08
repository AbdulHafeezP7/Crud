<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Real Madrid</title>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Link Css  -->
     <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="header-container text-center">
    <h1 class="bg-dark text-light py-2">Real Madrid First Team Players <img class="bg-light" src="uploads/real-madrid.png" alt="Logo" class="logo"></h1>
</div>



<!-- Form Modal -->
<?php include 'form.php' ?>
<?php include 'profile.php' ?>

<!-- Page Content -->
<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-10">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-dark text-light"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" class="form-control" placeholder="Search" id="searchQuery">
            </div>
        </div>
        <div class="col-2">
            <button class="btn btn-dark" type="button" data-toggle="modal" data-target="#playerModal" id="addplayerbtn">Add New Player</button>
        </div>
    </div>

      <!-- Player Details Table -->
      <?php include 'playerTable.php' ?> 

    <!-- pagination -->
    <nav aria-label="Page navigation example" id="pagination">
        <!-- <ul class="pagination justify-content-center">
            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul> -->
    </nav>
    <input type="hidden" value="1" name="currentpage" id="currentpage">
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap popper JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<!-- js file -->
 <script src="js/script.js">

 </script>

<!-- js for Age Calculation -->
<script>
    document.getElementById('playerDateOfBirth').addEventListener('change', function() {
        var dob = new Date(this.value);
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        document.getElementById('playerAge').value = age;
    });
</script>

</body>
</html>
