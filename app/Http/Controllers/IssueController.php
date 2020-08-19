<?php

namespace App\Http\Controllers;

use App\MailerCustom;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function report(Request $request)
    {
        // get input fields
        $message = $request->input('message');
        $location = $request->input('location');

        $mailer = new MailerCustom;

        $to =  [
            'nigeljaure.t@gmail.com'
        ];

        $subject = 'NEW BUG';

        $body = '
            Good day <br/> <br/>

            New bug has been reported. <br/>
            <table width="100%" border="0">
                <tr>
                    <td><b>Message:</b></td> <td style="color:red; font-weight:bold">'.$message.'</td>
                </tr>
                <tr>
                    <td><b>Location:</b></td> <td>'.$location.'</td>
                </tr>
            </table>
            <br/> <br/>

            <strong>Regards,</strong>
            <br/>
            FlexiPMS Team
        ';

        $mailer->mailer($to, $subject, $body);
    }
}
