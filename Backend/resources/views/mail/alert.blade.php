<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:v="urn:schemas-microsoft-com:vml" lang="en">

<head>
    <link rel="stylesheet" type="text/css" hs-webfonts="true"
        href="https://fonts.googleapis.com/css?family=Lato|Lato:i,b,bi">
    <meta property="og:title" content="Email template">

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style type="text/css">
        a {
            text-decoration: underline;
            color: inherit;
            font-weight: bold;
            color: #253342;
        }

        h1 {
            font-size: 56px;
        }

        h2 {
            font-size: 28px;
            font-weight: 900;
        }

        p {
            font-weight: 100;
        }

        td {
            vertical-align: top;
        }

        #email {
            margin: auto;
            width: 600px;
            background-color: white;
        }

        button {
            font: inherit;
            background-color: #FF7A59;
            border: none;
            padding: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 900;
            color: white;
            border-radius: 5px;
            box-shadow: 3px 3px #d94c53;
        }

        .subtle-link {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #CBD6E2;
        }

    </style>

</head>

<body bgcolor="#F5F8FA"
    style="width: 100%; margin: auto 0; padding:0; font-family:Lato, sans-serif; font-size:18px; color:#33475B; word-break:break-word">

    <! View in Browser Link -->

        <div id="email">

            <! Banner -->
                <table role="presentation" width="100%">
                    <tr>
                        <td bgcolor="#191645" align="center" style="color: white;">
                            <h1>Smart Energy Monitoring</h1>
                        </td>
                </table>

                <! First Row -->

                    <table role="presentation" border="0" cellpadding="0" cellspacing="10px"
                        style="padding: 30px 30px 30px 60px;">
                        <tr>
                            <td>
                                <h2>Unusual usage</h2>
                                <p>
                                    {{ $alert->alert }}
                                </p>
                                <p>
                                    {{ $alert->created_at }}
                                </p>
                            </td>
                        </tr>
                    </table>

                    <! Banner Row -->
                        <table role="presentation" bgcolor="#EAF0F6" width="100%" style="margin-top: 50px;">
                            <tr>
                                <td align="center" style="padding: 30px 30px;">

                                    <h2>You need help?</h2>
                                    <p>
                                        If you do indeed need any help, feel free to contact us on <b>geral@sem.com</b>
                                    </p>
                                </td>
                            </tr>
                        </table>
        </div>
</body>

</html>
