<!DOCTYPE html>
<html>
<head>
  <title>PawTrack</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <div class="p-10 text-center">
    <h1 class="text-3xl font-bold text-blue-600">
      PawTrack System Running 🐾
    </h1>
  </div>

</body>
</html>
<?php
include("config/db.php");
include("includes/header.php");
?>

<h1 class="text-2xl font-bold mb-4">🐾 Welcome to PawTrack</h1>

<p class="mb-4">
Browse available cats for adoption in Johor Bahru.
</p>

<a href="/PawTrack/cats/list.php"
   class="bg-blue-500 text-white px-4 py-2 rounded">
   View Available Cats
</a>

<?php include("includes/footer.php"); ?>