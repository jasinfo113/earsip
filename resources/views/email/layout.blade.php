<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        html,
        body {
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body>
    <div
        style="font-family:Arial,Helvetica,sans-serif; line-height: 1.5; font-weight: normal; font-size: 15px; color: #2F3044; min-height: 100%; margin:0; padding:0; width:100%; background-color:#ffffff;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
            style="border-collapse:collapse;margin:0 auto; padding:0; max-width:600px">
            <tbody>
                <tr>
                    <td align="center" valign="center" style="text-align:center; padding: 20px">
                        <a href="https://pemadam.jakarta.go.id/" rel="noopener" target="_blank">
                            <img src="{{ config('app.placeholder.logo_bundle') }}" style="width:auto;height:75px;"
                                alt="{{ config('app.name') }}" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="center">
                        <div
                            style="text-align:left; margin: 0 20px; padding: 40px; background-color:#edf2f7; border-radius: 6px">
                            <div style="padding-bottom: 30px; font-size: 17px;">
                                <strong>{{ $title }}</strong>
                            </div>
                            @include($content)
                            <div style="border-bottom: 1px solid #eeeeee; margin: 30px 0"></div>
                            <div style="padding-bottom: 10px">
                                Regards,<br />
                                JASINFO's Team.
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="center"
                        style="font-size: 13px; text-align:center;padding: 20px; color: #6d6e7c;">
                        <p style="line-height:1.5;">
                            <strong>Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta</strong>
                            <br />
                            Jalan K.H. Zainul Arifin No.71, Duri Pulo, Gambir, Jakarta Pusat
                            <br /><br />
                            Phone:
                            <a href="tel:62216344579" rel="noopener" style="text-decoration:none;color:#36328d;"
                                target="_blank">(021)
                                634-4579</a> /
                            <a href="tel:622163855357" rel="noopener" style="text-decoration:none;color:#36328d;"
                                target="_blank">638-55357</a>
                            <br />
                            Email:
                            <a href="mailto:damkardki@jakarta.go.id" rel="noopener"
                                style="text-decoration:none;color:#36328d;" target="_blank">damkardki@jakarta.go.id</a>
                        </p>
                        <p>
                            &copy; <?php echo now()->format('Y'); ?> Dinas Penanggulangan Kebakaran dan Penyelamatan Provinsi DKI Jakarta
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
