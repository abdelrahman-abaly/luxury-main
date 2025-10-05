<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Omar Talaat - Sales Agent Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('/css/main.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.3.2/af-2.7.0/b-3.2.3/b-colvis-3.2.3/cc-1.0.4/date-1.5.5/r-3.0.4/sc-2.4.3/sb-1.8.2/sp-2.3.3/sl-3.0.1/sr-1.4.1/datatables.min.css" rel="stylesheet" integrity="sha384-PRbVUpGxQJf9AMlQQApBSeM//3AnBDM7UhLJ+kbuzDblGTBB9zydryruPfe0sVYX" crossorigin="anonymous">
    <link rel="shortcut icon" href="{{asset('/images/company-logo.png')}}" type="image/x-icon">
    @yield("styles")
</head>
