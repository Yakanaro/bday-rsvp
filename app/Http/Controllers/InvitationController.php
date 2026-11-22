<?php

namespace App\Http\Controllers;

use App\Drink;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /**
     * Show the invitation ticket. After a sign-up without JavaScript the
     * guest's ticket comes back already filled in.
     */
    public function __invoke(Request $request): View
    {
        $ticketGuestId = $request->session()->get('ticket_guest_id');

        return view('invitation', [
            'drinks' => Drink::cases(),
            'ticketGuest' => $ticketGuestId ? Guest::find($ticketGuestId) : null,
        ]);
    }
}
