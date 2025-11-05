<!DOCTYPE html>
<html>
<head>
    <title>Your App</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
   
    @include("Layout::user")

    <!-- Contenu de la page -->
    

    <!-- Include jQuery and Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Stackable scripts specific to the page -->
    @stack('scripts')

</body>
</html>
