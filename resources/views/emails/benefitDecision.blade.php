<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Title</title>
    @include('emails.sharedStyles')
</head>

<body>
    <!-- <style> -->
    <table class="body" data-made-with-foundation="">
        <tr>
            <td class="float-center" align="center" valign="top">
                <center data-parsed="">
                    <table class="spacer float-center">
                        <tbody>
                            <tr>
                                <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                            </tr>
                        </tbody>
                    </table>
                    <table align="center" class="container float-center">
                        <tbody>
                            <tr>
                                <td>
                                    <table class="spacer">
                                        <tbody>
                                            <tr>
                                                <td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="row">
                                        <tbody>
                                            <tr>
                                                <td height="16px" style="font-size:16px;line-height:16px;">

                                                    <img height="150" src="data:image/svg+xml;base64, {{ base64_encode( $benefitUser->benefits->encoded_logo )}}" alt="Benefit_Logo">

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="row">
                                        <tbody>
                                            <tr>
                                                <th class="small-12 large-12 columns first last">
                                                    <table>
                                                        <tr>
                                                            <th>
                                                                <h1>{{ $benefitUser->user->name }}</h1>
                                                                <table class="spacer">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td height="16px"
                                                                                style="font-size:16px;line-height:16px;">
                                                                                &#xA0;</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <table class="callout">
                                                                    <tr>
                                                                        <th class="callout-inner secondary">
                                                                            <table class="row">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <th
                                                                                            class="small-12 large-6 columns first">
                                                                                            <table>
                                                                                                <tr>
                                                                                                    <th>
                                                                                                        <p>
                                                                                                            <strong>Beneficio</strong><br>
                                                                                                            {{ $benefitUser->benefits->name }}
                                                                                                        </p>
                                                                                                        <p> <strong>Fecha
                                                                                                                de
                                                                                                                Redención</strong><br>
                                                                                                            {{ \Carbon\Carbon::parse($benefitUser->benefit_begin_time)->format('d/m/Y') }}
                                                                                                        </p>
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </th>
                                                                                        <th
                                                                                            class="small-12 large-6 columns last">
                                                                                            <table>
                                                                                                <tr>
                                                                                                    <th>
                                                                                                        <p>
                                                                                                            <strong>Detalle</strong><br>
                                                                                                            {{ $benefitUser->benefit_detail->name }}
                                                                                                        </p>
                                                                                                        <p>
                                                                                                            <strong>Fecha
                                                                                                                de
                                                                                                                Registro</strong><br>
                                                                                                            {{ \Carbon\Carbon::parse($benefitUser->created_at)->format('d/m/Y') }}
                                                                                                        </p>
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </th>
                                                                                    </tr>
                                                                                    <tr>

                                                                                        <p>
                                                                                        <p>
                                                                                            Queremos
                                                                                            informarte
                                                                                            que tu
                                                                                            beneficio
                                                                                            solicitado
                                                                                            fue:
                                                                                        </p>
                                                                                        <strong>{{ $benefitUser->is_approved->value === 1 ? 'Aprobado' : 'Rechazado' }}</strong><br>
                                                                                        </p>
                                                                                        {{ $benefitUser->decision_comment }}
                                                                                        </p>

                                                                                    </tr>

                                                                                </tbody>
                                                                            </table>
                                                                        </th>
                                                                        <th class="expander"></th>
                                                                    </tr>
                                                                </table>
                                                                <table class="spacer">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td height="16px"
                                                                                style="font-size:16px;line-height:16px;">
                                                                                &#xA0;</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>

                                    @if ($benefitUser->is_approved === 1)
                                        <table class="row">
                                            <tbody>
                                                <tr>
                                                    <th class="small-12 large-12 columns first last">
                                                        <small>* Adjunto te enviamos también tu cita para que la
                                                            <b>importes</b> en tu calendario</small><br>
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif

                                    <table class="row footer text-center">
                                        <tbody>
                                            <tr>
                                                <th class="small-12 large-3 columns first">
                                                    <table>
                                                        <tr>
                                                            <th>
                                                                <img src={{ $message->embed(realpath(public_path() . '/images/logo_no_slogan.png')) }}
                                                                    alt="logo">
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </th>
                                                <th class="small-12 large-3 columns">
                                                    <table>
                                                        <tr>
                                                            <th>
                                                                <p><a href="mailto:juancamilo.soto@outlook.com"
                                                                        style="color: #C8102E;">¿Novedades?
                                                                        Reportalas aquí</a><br></p>
                                                            </th>
                                                        </tr>
                                                    </table>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </center>
            </td>
        </tr>
    </table>
</body>

</html>
