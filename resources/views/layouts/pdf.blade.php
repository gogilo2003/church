@php
    $photoPath = file_exists(public_path('logo.png'))
        ? public_path('logo.png')
        : ($photoPath = public_path('logo-placeholder.png'));

    $photoBase64 = base64_encode(file_get_contents($photoPath));
    $photoMimeType = mime_content_type($photoPath);
    $logo = "data:$photoMimeType;base64,$photoBase64";
@endphp
<!DOCTYPE html>
<html>

<head>
    <title>Member Report</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        .poppins-thin {
            font-family: "Poppins", sans-serif;
            font-weight: 100;
            font-style: normal;
        }

        .poppins-extralight {
            font-family: "Poppins", sans-serif;
            font-weight: 200;
            font-style: normal;
        }

        .poppins-light {
            font-family: "Poppins", sans-serif;
            font-weight: 300;
            font-style: normal;
        }

        .poppins-regular {
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .poppins-medium {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-style: normal;
        }

        .poppins-semibold {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-style: normal;
        }

        .poppins-bold {
            font-family: "Poppins", sans-serif;
            font-weight: 700;
            font-style: normal;
        }

        .poppins-extrabold {
            font-family: "Poppins", sans-serif;
            font-weight: 800;
            font-style: normal;
        }

        .poppins-black {
            font-family: "Poppins", sans-serif;
            font-weight: 900;
            font-style: normal;
        }

        .poppins-thin-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 100;
            font-style: italic;
        }

        .poppins-extralight-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 200;
            font-style: italic;
        }

        .poppins-light-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 300;
            font-style: italic;
        }

        .poppins-regular-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            font-style: italic;
        }

        .poppins-medium-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-style: italic;
        }

        .poppins-semibold-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-style: italic;
        }

        .poppins-bold-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 700;
            font-style: italic;
        }

        .poppins-extrabold-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 800;
            font-style: italic;
        }

        .poppins-black-italic {
            font-family: "Poppins", sans-serif;
            font-weight: 900;
            font-style: italic;
        }

        * {
            font-family: 'Open Sans', sans-serif;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            line-height: 1.5;
        }

        table {
            --color: #d0d0f5;
            text-align: left;
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid;
        }

        thead {
            border-block-end: 2px solid;
            background: whitesmoke;
        }

        tfoot {
            border-block: 2px solid;
            background: whitesmoke;
        }

        thead,
        tfoot {
            background: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        th,
        td {
            border: 1px solid lightgrey;
            padding: 0.25rem 0.75rem;
        }

        .container {
            /* background-color: whitesmoke; */
        }

        .logo {
            height: 128px;
            margin: 0 auto;
            display: block;
        }

        .uppercase {
            text-transform: uppercase
        }

        .capitalize {
            text-transform: capitalize
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
        }

        h1,
        h2,
        h3,
        h4,
        h5 {
            margin: 0;
            padding: 0;
        }
    </style>
    @stack('styles')
</head>

<body class="poppins-regular">
    <div class="container">
        <img class="logo" src="{!! $logo !!}">
        <h1 class="poppins-black uppercase text-center">{{ env('APP_NAME') }}</h1>
        <div>
            <span class="address">{{ env('email') }}</span>
        </div>
        <h2 class="text-center uppercase poppins-medium">@yield('heading')</h2>
    </div>
    @yield('body')
</body>

</html>
