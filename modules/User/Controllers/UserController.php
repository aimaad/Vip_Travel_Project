<?php
namespace Modules\User\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Matrix\Exception;
use Modules\Boat\Models\Boat;
use Modules\Booking\Models\Service;
use Modules\Event\Models\Event;
use Modules\Flight\Models\Flight;
use Modules\FrontendController;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Tour\Models\Tour;
use Modules\User\Events\NewVendorRegistered;
use Modules\User\Events\UserSubscriberSubmit;
use Modules\User\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Vendor\Models\VendorRequest;
use Validator;
use Modules\Booking\Models\Booking;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Modules\Booking\Models\Enquiry;
use Illuminate\Support\Str;
use Modules\User\Models\User; 
use Modules\User\Models\Role; 
use App\Models\Agency;
use App\Models\RoleRequest;
use App\Events\NewRoleRequestSubmitted;
use App\Events\RoleRequestUpdated;
use App\Notifications\RoleRequestStatusChanged;




class UserController extends FrontendController
{
    use AuthenticatesUsers;

    protected $enquiryClass;
    private Booking $booking;

    public function __construct(Booking $booking, Enquiry $enquiry)
    {
        $this->enquiryClass = $enquiry;
        parent::__construct();
        $this->booking = $booking;
    }

    public function dashboard(Request $request)
    {
        $this->checkPermission('dashboard_vendor_access');
        $user_id = Auth::id();
        $data = [
            'cards_report'       => $this->booking->getTopCardsReportForVendor($user_id),
            'earning_chart_data' => $this->booking->getEarningChartDataForVendor(strtotime('monday this week'), time(), $user_id),
            'page_title'         => __("Vendor Dashboard"),
            'breadcrumbs'        => [
                [
                    'name'  => __('Dashboard'),
                    'class' => 'active'
                ]
            ]
        ];
        return view('User::frontend.dashboard', $data);
    }

    public function reloadChart(Request $request)
    {
        $chart = $request->input('chart');
        $user_id = Auth::id();
        switch ($chart) {
            case "earning":
                $from = $request->input('from');
                $to = $request->input('to');
                return $this->sendSuccess([
                    'data' => $this->booking->getEarningChartDataForVendor(strtotime($from), strtotime($to), $user_id)
                ]);
                break;
        }
    }

    public function profile(Request $request)
{
    $user = Auth::user();
    $cities = [
        'Agadir', 'Aguelmous', 'Ait Baha', 'Ait Melloul', 'Ait Ourir', 'Al Hoceima', 'Al Aaiún',
        'Asilah', 'Assa', 'Azemmour', 'Azilal', 'Azrou', 'Beni Mellal', 'Ben Guerir', 'Benslimane',
        'Berkane', 'Berrechid', 'Bouarfa', 'Boujdour', 'Boulemane', 'Bouznika', 'Casablanca', 'Chefchaouen',
        'Chichaoua', 'Dakhla', 'El Aaiún', 'El Jadida', 'El Kelaa des Sraghna', 'Errachidia', 'Essaouira',
        'Fes', 'Figuig', 'Gourrama', 'Guelmim', 'Guercif', 'Ifrane', 'Inezgane', 'Imouzzer Kandar',
        'Jorf', 'Jerada', 'Kenitra', 'Khemisset', 'Khouribga', 'Ksar El Kebir',
        'Larache', 'Laayoune', 'Martil', 'Marrakech', 'Meknes', 'Midelt', 'Missour', 'Mohammedia',
        'Nador', 'Ouarzazate', 'Oued Zem', 'Oujda', 'Oulad Teima', 'Ouazzane', 'Rabat', 'Safi',
        'Sefrou', 'Settat', 'Sidi Bennour', 'Sidi Ifni', 'Sidi Kacem', 'Sidi Slimane', 'Sidi Yahya El Gharb',
        'Skhirat', 'Smara', 'Tafraout', 'Tan-Tan', 'Tanger', 'Taourirt', 'Taounate',
        'Taroudant', 'Tata', 'Taza', 'Temara', 'Tetouan', 'Tiflet', 'Tinghir', 'Tiznit', 
        'Youssoufia', 'Zagora', 'Zawyat an Nwacer', 'Zerhoun'
    ];

    $data = [
        'dataUser'         => $user,
        'page_title'       => __("Profile"),
        'breadcrumbs'      => [
            [
                'name'  => __('Setting'),
                'class' => 'active'
            ]
        ],
        'is_vendor_access' => $this->hasPermission('dashboard_vendor_access'),
        'cities'           => $cities,
    ];
    return view('User::frontend.profile', $data);
}

    
public function profileUpdate(Request $request)
{
    if (is_demo_mode()) {
        return back()->with('error', "Demo mode: disabled");
    }

    $user = Auth::user();
    $messages = [
        'first_name.required' => __('The First Name field is required.'),
        'last_name.required' => __('The Last Name field is required.'),
        'email.required' => __('The E-mail field is required.'),
        'civilite.required' => __('The Civilite field is required.'),
        'city.required' => __('The City field is required.'),
    ];

    $request->validate([
        'civilite' => 'required|string|max:3',
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users')->ignore($user->id)
        ],
        'city' => 'required|string|max:255',
        'birthday' => 'nullable|date',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], $messages);

    // Handle avatar upload if needed
    if ($request->hasFile('avatar')) {
        $avatar = $request->file('avatar');
        $avatarPath = $avatar->store('avatars', 'public');
        $user->avatar_id = $avatarPath;
    }

    // Update user info
    $user->fill($request->except(['birthday']));
    if ($request->has('birthday')) {
        $user->birthday = date("Y-m-d", strtotime($request->input('birthday')));
    }
    $user->civilite = $request->input('civilite');
    $user->save();

    return redirect()->back()->with('success', __('Update successfully'));
}

    
    public function bookingHistory(Request $request)
    {
        $user_id = Auth::id();
        $data = [
            'bookings' => $this->booking->getBookingHistory($request->input('status'), $user_id),
            'statues'     => config('booking.statuses'),
            'breadcrumbs' => [
                [
                    'name'  => __('Booking History'),
                    'class' => 'active'
                ]
            ],
            'page_title'  => __("Booking History"),
        ];
        return view('User::frontend.bookingHistory', $data);
    }

    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255'
        ]);
        $check = Subscriber::withTrashed()->where('email', $request->input('email'))->first();
        if ($check) {
            if ($check->trashed()) {
                $check->restore();
                return $this->sendSuccess([], __('Thank you for subscribing'));
            }
            return $this->sendError(__('You are already subscribed'));
        } else {
            $a = new Subscriber();
            $a->email = $request->input('email');
            $a->first_name = $request->input('first_name');
            $a->last_name = $request->input('last_name');
            $a->save();

            event(new UserSubscriberSubmit($a));

            return $this->sendSuccess([], __('Thank you for subscribing'));
        }
    }

    public function upgradeVendor(Request $request){
        $user = Auth::user();
        $vendorRequest = VendorRequest::query()->where("user_id",$user->id)->where("status","pending")->first();
        if(!empty($vendorRequest)){
            return redirect()->back()->with('warning', __("You have just done the become vendor request, please wait for the Admin's approved"));
        }
        // check vendor auto approved
        $vendorAutoApproved = setting_item('vendor_auto_approved');
         $dataVendor['role_request'] = setting_item('vendor_role');
        if ($vendorAutoApproved) {
            if ($dataVendor['role_request']) {
                $user->assignRole($dataVendor['role_request']);
            }
            $dataVendor['status'] = 'approved';
            $dataVendor['approved_time'] = now();
        } else {
            $dataVendor['status'] = 'pending';
        }
        $vendorRequestData = $user->vendorRequest()->save(new VendorRequest($dataVendor));
        try {
            event(new NewVendorRegistered($user, $vendorRequestData));
        } catch (Exception $exception) {
            Log::warning("NewVendorRegistered: " . $exception->getMessage());
        }
        return redirect()->back()->with('success', __('Request vendor success!'));
    }



    public function permanentlyDelete(Request $request){
        if(is_demo_mode()){
            return back()->with('error',"Demo mode: disabled");
        }
        if(!empty(setting_item('user_enable_permanently_delete')))
        {
            $user = Auth::user();
            \DB::beginTransaction();
            try {
                Service::where('author_id',$user->id)->delete();
                Tour::where('author_id',$user->id)->delete();
                Space::where('author_id',$user->id)->delete();
                Hotel::where('author_id',$user->id)->delete();
                Event::where('author_id',$user->id)->delete();
                Flight::where('author_id',$user->id)->delete();
                $user->sendEmailPermanentlyDelete();
                $user->delete();
                \DB::commit();
                Auth::logout();
                if(is_api()){
                    return $this->sendSuccess([],'Deleted');
                }
                return redirect(route('home'));
            }catch (\Exception $exception){
                \DB::rollBack();
            }
        }
        if(is_api()){
            return $this->sendError('Error. You can\'t permanently delete');
        }
        return back()->with('error',__('Error. You can\'t permanently delete'));

    }

    //become
    public function showBecomeAgentForm()
    {
        $user = Auth::user();
        $agencies = Agency::pluck('name');
        $breadcrumbs = [
            ['name' => __('Become an Agent'), 'url' => ''],
        ];
    
        return view('user.change_role', compact('breadcrumbs', 'user', 'agencies'))->with('role_name', 'Agent de voyage');
    }
    
    public function showBecomeDistributorForm()
    {
        $user = Auth::user();
        $agencies = Agency::pluck('name');
        $breadcrumbs = [
            ['name' => __('Become a Distributor'), 'url' => ''],
        ];
    
        return view('user.change_role', compact('breadcrumbs', 'user', 'agencies'))->with('role_name', 'Distributeur de voyage');
    }
    

    public function changeUserRole(Request $request)
    {
        Log::info('Début du processus de changement de rôle pour l\'utilisateur ID: ' . Auth::id());
    
        $request->validate([
            'agency_name' => 'required|string|max:255',
            'iata_office_id' => 'nullable|string|max:255',
        ]);
    
        try {
            $user = Auth::user();
            $roleName = $request->input('role_name');
    
            Log::info('Rôle demandé: ' . $roleName . ' pour l\'utilisateur ID: ' . $user->id);
    
            // Vérifier si l'utilisateur a déjà le rôle
            if ($user->role->name === $roleName) {
                Log::warning('L\'utilisateur a déjà le rôle: ' . $roleName);
                return redirect()->back()->with('alert', 'Vous avez déjà le rôle de ' . $roleName . '.');
            }
    
            // Vérifier s'il existe déjà une demande en attente pour ce rôle
            if ($user->hasPendingRoleRequest($roleName)) {
                Log::warning('Une demande en attente pour ce rôle existe déjà.');
                return redirect()->back()->with('alert', 'Vous avez déjà une demande en attente pour ce rôle.');
            }
    
            // Créer une nouvelle demande de rôle
            $roleRequest = new RoleRequest();
            $roleRequest->user_id = $user->id;
            $roleRequest->type = $roleName;
            $roleRequest->agency_name = $request->input('agency_name');
            if ($request->input('agency_name') === 'other') {
                $roleRequest->agency_name = $request->input('other_agency_name');
            }
            $roleRequest->iata_office_id = $request->input('iata_office_id');
            $roleRequest->status = 'pending';
            $roleRequest->save();
    
            Log::info('Nouvelle demande de rôle créée avec succès pour l\'utilisateur ID: ' . $user->id);
    
            // Déclencher l'événement de notification
            event(new NewRoleRequestSubmitted($user, $roleRequest));
    
            return redirect()->route('user.profile.index')->with([
                'success' => 'Votre demande a été soumise et est en attente d\'approbation.',
                'info' => 'Vous serez informé une fois que votre demande aura été examinée par un administrateur.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la soumission de la demande de rôle: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Un problème est survenu lors de la soumission de votre demande. Veuillez réessayer.');
        }
    }
    
    
    

public function agentUpgradeRequest()
{
    $this->checkPermission('user_view');
    $roleRequests = RoleRequest::where('type', 'Agent de voyage')->where('status', 'pending')->get();
    return view('admin.role_requests.index', compact('roleRequests'))->with('page_title', __('Agent Upgrade Requests'));
}

public function distributorUpgradeRequest()
{
    $this->checkPermission('user_view');
    $roleRequests = RoleRequest::where('type', 'Distributeur de voyage')->where('status', 'pending')->get();
    return view('admin.role_requests.index', compact('roleRequests'))->with('page_title', __('Distributor Upgrade Requests'));
}

public function roleUpgradeApprove($id)
{
    $this->checkPermission('user_view');
    $roleRequest = RoleRequest::find($id);
    if ($roleRequest) {
        $roleRequest->status = 'approved';
        $roleRequest->approved_by = auth()->user()->id;
        $roleRequest->save();

        // Update the user's role and additional fields
        $user = $roleRequest->user;
        $user->role_id = Role::where('name', $roleRequest->type)->first()->id;

        // Update the agency name and IATA office ID
        $user->agency_name = $roleRequest->agency_name;
        $user->iata_office_id = $roleRequest->iata_office_id;

        $user->save();

        // Log the event firing
        Log::info('RoleRequestUpdated event fired for request ID: ' . $roleRequest->id);

        // Send notification to the user
        $user->notify(new RoleRequestStatusChanged($roleRequest, 'approved'));

        return redirect()->route('user.admin.agentUpgrade')->with('success', __('Role request approved successfully.'));
    }
    return redirect()->route('user.admin.roleUpgrade')->with('error', __('Role request not found.'));
}



public function roleUpgradeDecline($id)
{
    $this->checkPermission('user_view');
    $roleRequest = RoleRequest::find($id);
    if ($roleRequest) {
        $roleRequest->status = 'declined';
        $roleRequest->approved_by = auth()->user()->id;
        $roleRequest->save();

        // Log the event firing
        Log::info('RoleRequestUpdated event fired for request ID: ' . $roleRequest->id);

        // Send notification to the user
        $roleRequest->user->notify(new RoleRequestStatusChanged($roleRequest, 'declined'));

        return redirect()->route('user.admin.agentUpgrade')->with('success', __('Role request declined successfully.'));
    }
    return redirect()->route('user.admin.roleUpgrade')->with('error', __('Role request not found.'));
}

}