<?php
namespace App\Http\Controllers;
use App\Organization;
use App\ParentChildOrganizations;
use App\Subscription;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class SubscriptionController extends Controller
{
    protected $organization;
    public function getIndex()
    {
        $id = Auth::user()->organization_id;
        //if organization is child and it's parent org does not have a active subscription redirect it to 'subscription expired' page
        $ischild = ParentChildOrganizations::active()->where('child_org_id', '=', $id)->exists();
        $organization = Organization::findOrFail($id);
        if ($ischild) {  //checks if the current org is child
            $parentOrgid = ParentChildOrganizations::active()->where('child_org_id', $id)->value('parent_org_id');
            $organization = Organization::findOrFail($parentOrgid);
            //checks if organization subscription records exists and organization's subscription end date is greater or equal to today
            if ($organization->subscribed('main') AND $organization->trial_ends_at->gte(Carbon::now())) {
                    return redirect('/dashboard');
            } else {
                return view('subscriptions.expiredsubscription');
            }
        } else {

            if (!is_null($organization->trial_ends_at) && $organization->subscribed('main') && $organization->trial_ends_at->gte(Carbon::now()) ) {
                return redirect('/dashboard');
            } else {
                return view('subscriptions.payment');
            }
        }
    }
    public function postJoin(Request $request)
    {
        $id = Auth::user()->organization_id;
        $email = Auth::user()->email;
        $organization = Organization::findOrFail($id);
        $qty = $request->user_locations;
        if($qty == "101+"){
            $qty = 101;    
        }
        $locations = $request->input('user_locations');
        $pickedPlan = $request->get('plan');
        $plan = $pickedPlan . $locations;
        $coupon = $request->get('coupon');
        if ($organization->subscribed('main')) {
            return redirect('subscription')->with('message', 'Plan lready Submitted!');
        } else {
            try{

                $organization->error_message = "";
                $organization->update();
                if ($request->input('plan') == "Annually") {

                    if (isset($coupon)) {
                        $organization->newSubscription('main', $plan)->withCoupon($coupon)
                                    ->withMetadata(array('organization_id' => $organization->id))
                                    ->create($request->input('token'), [
                                        'email' => $email 

                                    ]);

                    } else {

                        $organization->newSubscription('main', $plan)
                                    ->withMetadata(array('organization_id' => $organization->id))
                                    ->create($request->input('token'), [
                                        'email' => $email 

                                    ]);
                    }
                
                    Subscription::where('organization_id', $id)->update(['quantity' => $qty]);

                    $organization->trial_ends_at = Carbon::now()->addYear(1);

                    $organization->save();


                } else {
                    if ($request->input('plan') == "Monthly") {

                        if (isset($coupon)) {

                            $organization->newSubscription('main', $plan)->withCoupon($coupon)
                                        ->withMetadata(array('organization_id' => $organization->id))
                                        ->create($request->input('token'), [
                                            'email' => $email 
                                        ]);

                        } else {
                                $organization->newSubscription('main', $plan)
                                    ->withMetadata(array('organization_id' => $organization->id))
                                    ->create($request->input('token'), ['email' => $email 
                                    ]);
                        }
                        $t = Subscription::where('organization_id', $id);
                        Subscription::where('organization_id', $id)->update(['quantity' => $qty]);
                        $organization->trial_ends_at = Carbon::now()->addMonth(1);
                        $organization->save();
                    }
                }
            }catch (\Exception $e){
                if( !is_null($organization->trial_ends_at)) {
                    $organization->error_message = $e->getMessage();
                    $organization->update();
                }
                
                    return redirect('subscription')->with('message', 'Your payment cannot be processed at this time. Please try again later.');

            }
            return redirect('/dashboard')->with('message', 'Successfully  Subscribed!');

        }
    }

    public function cancel()
    {
        $id = Auth::user()->organization_id;
        $organization = Organization::findOrFail($id);
        $organization->subscription('main')->cancel();
        if ($organization->subscription('main')->cancelled()) {
            $endsAt = DB::table('subscriptions')->where('organization_id', Auth::user()->organization_id)->value('ends_at');
            $endsAt = \Carbon\Carbon::parse($endsAt)->format('m-d-Y');

            return redirect('organizations')->with('message', "Subscription ends at: $endsAt");
        } else {
            return redirect('organizations')->with('message', 'Please contact CharityQ administrator to end the subscription');
        }

    }

    public function resume()
    {

        $organization = Organization::findOrFail(Auth::user()->organization_id);
        if ($organization->subscription('main')->onGracePeriod()) {
            $organization->subscription('main')->resume();

            return redirect('organizations')->with('message', 'Subscription Resumed');
        }

    }

    public function applycoupon(Request $request)
    {
        $coupon = $request->get('coupon');
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $retrievedCoupon = \Stripe\Coupon::retrieve($coupon);
        return $retrievedCoupon->percent_off;

    }


}
