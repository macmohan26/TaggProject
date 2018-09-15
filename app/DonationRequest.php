<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonationRequest extends Model
{
    protected $fillable = [
        'organization_id',
        'requester',
        'requester_type',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'street_address1',
        'street_address2',
        'city',
        'state',
        'zipcode',
        'tax_exempt',
        'file_url',
        'item_requested',
        'other_item_requested',
        'dollar_amount',
        'item_purpose',
        'other_item_purpose',
        'needed_by_date',
        'event_name',
        'event_type',
        'est_attendee_count',
        'event_start_date',
        'est_attendee_count',
        'venue',
        'marketing_opportunities',
        'approved_dollar_amount',
        'approved_organization_id',
        'approved_user_id',
        'rule_process_date',
        'approval_status_id',
        'approval_status_reason',
        'email_sent'
    ];
    protected $casts = [
        'tax_exempt' => 'boolean' // compare true false on incoming donation request 
    ];

//     protected $table = 'donation_requests';

    public function donationRequestTypes()
    {
        return $this->hasOne('App\Requester_type', 'id', 'requester_type');
    }

    public function donationRequestType()
    {
        return $this->belongsTo('App\Request_item_type', 'item_requested', 'id');
    }

    public function donationRequestPurpose()
    {
        return $this->hasOne('App\Request_item_purpose', 'item_purpose', 'id');
    }

    public function donationOrganization()
    {
        return $this->hasOne('App\Organization_type');
    }

    public function donationApprovalStatus()
    {
        return $this->belongsTo('App\Approval_Status', 'approval_status_id', 'id');
    }

    public function organization()
    {
        return $this->hasOne('App\Organization', 'id', 'organization_id');
    }
}